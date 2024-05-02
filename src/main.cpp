#ifdef __APPLE__

/*
	silly goofy fix because it errors if it comes after geode includes
	than you prevter and ninxout
	- raydeeux
*/

#define CommentType CommentTypeDummy
#include <CoreGraphics/CoreGraphics.h>
#include <CoreServices/CoreServices.h>
#undef CommentType

#endif

#include <Geode/modify/MenuLayer.hpp>
#include <Geode/modify/ProfilePage.hpp>
#include <Geode/utils/web.hpp>
#include <matjson.hpp>

#ifndef TARGET_OS_IOS
#include <imgui.h>
#include <imgui-cocos.hpp>
#include "imgui_internal.h"
#endif // TARGET_OS_IOS

#include <iostream>
#include <sstream>
#include <cctype>
#include <iomanip>
#include <string>
#include <ghc/filesystem.hpp>


using namespace geode::prelude;
namespace fs = ghc::filesystem;

std::string urlEncode(const std::string& str) {
	std::ostringstream encoded;
	encoded << std::hex << std::uppercase;
	for (char ch : str) {
		if (std::isalnum(ch) || ch == '-' || ch == '.' || ch == '~' || ch == '\n') {
			if (ch == '\n') {
				encoded << "%0A";
			} else {
				encoded << ch;
			}
		} else {
			encoded << '%' << std::setw(2) << int(static_cast<unsigned char>(ch));
		}
	}
	return encoded.str();
}



class showBio : public geode::Popup<std::string const&> {
protected:
    bool setup(std::string const& value) override {
        auto winSize = CCDirector::sharedDirector()->getWinSize();

        // convenience function provided by Popup 
        // for adding/setting a title to the popup
        this->setTitle("About Me");

		CCSize MDTextSize = { 329.5, 200.0f };


		auto RealBio = MDTextArea::create(value.c_str(), MDTextSize);
		RealBio->setPosition(ccp(0.0f, -10.0f));
		m_buttonMenu->addChild(RealBio);


        return true;
    }

public:
    static showBio* create(std::string const& text) {
        auto ret = new showBio();
        if (ret && ret->init(350.0f, 250.f, text)) {
            ret->autorelease();
            return ret;
        }
        CC_SAFE_DELETE(ret);
        return nullptr;
    }
};

class AboutMeHandler : public cocos2d::CCObject {
	char buffer[4096] = "";
	bool theWindowShow = true;
	public:
	void onAboutMe(CCObject* pSender) {
		theWindowShow = true;
		bool& localWindowShow = theWindowShow;
		ImGuiCocos::get().setup([]{
			auto* arialFont = ImGui::GetIO().Fonts->AddFontFromFileTTF((Mod::get()->getResourcesDir() / "arial.ttf").string().c_str(), 24.0f);
			ImGui::GetIO().FontDefault = arialFont;
		}).draw([this, &localWindowShow] {
			if (localWindowShow) {
				if (ImGui::Begin("Add Bio", &localWindowShow, ImGuiWindowFlags_AlwaysAutoResize | ImGuiWindowFlags_NoCollapse)) {

					ImGuiStyle& style = ImGui::GetStyle();

					// styling
					style.WindowRounding = 12.0f;
					style.FrameRounding = 12.0f;



					ImGui::InputTextMultiline("##source", buffer, IM_ARRAYSIZE(buffer), ImVec2(500, 300)); // moved to be above the button, added in 1.1.0

					ImGui::Spacing();

					// upload bio code
					if (ImGui::Button("Upload!", ImVec2(500, 0))) {
						std::string AuthPass = Mod::get()->getSavedValue<std::string>("my-saved-value");
						std::string BioRequestBody = "accountID=" + std::to_string(GJAccountManager::get()->m_accountID) + "&bio=" + urlEncode(buffer) + "&authCode=" + AuthPass;
						web::AsyncWebRequest()
						.bodyRaw(BioRequestBody)
						.post("https://yellowcat98.5v.pl/profilebio/PB_uploadProfileBio.php")
						.text()
						.then([=](std::string const& response) {
							log::info("{}", response);
							bool showWindow = true;
							ImGuiCocos::get().setup([]{
								auto* arialFont = ImGui::GetIO().Fonts->AddFontFromFileTTF((Mod::get()->getResourcesDir() / "arial.ttf").string().c_str(), 16.0f);
								ImGui::GetIO().FontDefault = arialFont;
							}).draw([=]() mutable {
								if (showWindow) {
									if (ImGui::Begin("Server Response", &showWindow, ImGuiWindowFlags_AlwaysAutoResize | ImGuiWindowFlags_NoCollapse)) {
										ImGui::Text("%s", response.c_str());
									}
								}
							});
						})
						.expect([BioRequestBody](std::string const& error) {
							log::info("{}", error);
						});
					}
				
				}
			ImGui::End();
			}
		});
	}
};


std::string dataBio;
std::string realBio;

int userAccID = GJAccountManager::get()->m_accountID;

class $modify(PBProfilePage, ProfilePage) {
private:
	int m_accountID;
	bool m_ownProfile;
public:

	bool init(int accountID, bool ownProfile) {
		if (!ProfilePage::init(accountID, ownProfile)) { return false; }
		m_accountID = accountID;
		m_ownProfile = ownProfile;
		return true;
	}
	void loadPageFromUserInfo(GJUserScore* p0) {
		ProfilePage::loadPageFromUserInfo(p0);
		int accountID = m_accountID;
		auto loadingCircle = LoadingCircle::create();
		loadingCircle->setPosition(ccp(-195.0f, 85.0f));
		loadingCircle->setScale(0.5f, 0.5f);
		loadingCircle->show();
		m_buttons->addObject(loadingCircle);
		web::AsyncWebRequest()
		.fetch("https://yellowcat98.5v.pl/profilebio/PB_getProfileBio.php?accountID=" + std::to_string(accountID))
		.text()
		.then([=](std::string const& bioData) {
			dataBio = bioData;
				if (dataBio != "-1" && userAccID > 0) {
					auto socialsMenu = this->getChildByIDRecursive("socials-menu");
					auto bioShowSpr = CCSprite::create("showBio.png"_spr);
					auto bioShow = CCMenuItemSpriteExtra::create(bioShowSpr, this, menu_selector(PBProfilePage::showBio));
					bioShow->setID("show-bio-btn"_spr);
					socialsMenu->addChild(bioShow);
					m_buttons->addObject(bioShow); // avoid duplication
					socialsMenu->updateLayout();
					loadingCircle->fadeAndRemove();
					

					// the actual data here (turned into a json)
					auto bioObject = matjson::parse(bioData);
					auto bioAccountID = bioObject["accountID"].as_string();
					auto bioBio = bioObject["bio"].as_string();
					auto bioID = bioObject["id"].as_string();
					realBio = bioBio;
					
				} else {
					loadingCircle->fadeAndRemove();
				}
			});

			// if ownProfile && geode is nono mobile
			#ifndef GEODE_IS_MOBILE
			if (m_ownProfile) {
				auto addBioSpr = CCSprite::create("addAboutMe.png"_spr);
				auto aboutMeBtn = CCMenuItemSpriteExtra::create(addBioSpr, nullptr, nullptr);
				aboutMeBtn->setEnabled(true);
				aboutMeBtn->setID("add-bio-btn"_spr);
				auto bottomMenu = this->getChildByIDRecursive("bottom-menu");
				bottomMenu->addChild(aboutMeBtn);
				bottomMenu->updateLayout();
				auto aboutMeHandler = new AboutMeHandler();
				aboutMeBtn->setTarget(aboutMeHandler, menu_selector(AboutMeHandler::onAboutMe));
			}
			#endif
		}
	void showBio(CCObject* pSender) {
		auto showbioPopup = showBio::create(realBio);
		showbioPopup->show();
	}
};