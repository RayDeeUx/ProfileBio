/*
 * explanation how this monstrosity works:
 * basically, it generates a random string, that string is sent to PB_Auth.php with your accountID
 * PB_Auth.php stored those 2 parameters into the Auth table in the database
 * now, each time the mod loads, it checks for whether the accountID has a Auth string attached to it
*/
#include <Geode/Geode.hpp>
#include <Geode/utils/web.hpp>
#include <Geode/modify/LoadingLayer.hpp>
#include <random>
#include <ctime>

using namespace geode::prelude;

int accID = GJAccountManager::get()->m_accountID;

std::string generateRandomString(int length) {
    const char charset[] = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    const int max_index = sizeof(charset) - 1;

    std::string randomString;
    randomString.reserve(length);

    // Use current time as part of the seed
    std::time_t seed = std::time(nullptr);

    std::mt19937 gen(seed);
    std::uniform_int_distribution<> dis(0, max_index);

    for (int i = 0; i < length; ++i) {
        randomString += charset[dis(gen)];
    }

    return randomString;
}

$on_mod(Loaded) {
    if (accID > 0) {
        std::string AuthPass = generateRandomString(16);
        auto Auth = web::fetch("http://yellowcat98.5v.pl/profilebio/PB_hasAuthCode.php?accountID=" + std::to_string(accID) + "&AuthCode=" + AuthPass);

        auto hasAuth = Auth.value();

        if (hasAuth != "1") {
            log::info("Looks like you have Authentication!");
        } else {
            log::info("No authentication code found in DB, adding Authentication...");
            Mod::get()->setSavedValue<std::string>("auth-code", AuthPass);
        }
    }
};