cmake_minimum_required(VERSION 3.21)
set(CMAKE_CXX_STANDARD 20)
set(CMAKE_CXX_STANDARD_REQUIRED ON)
set(CMAKE_OSX_ARCHITECTURES "x86_64")
set(CMAKE_CXX_VISIBILITY_PRESET hidden)

if(APPLE)
    project(ProfileBio VERSION 1.0.0 LANGUAGES CXX OBJCXX)
    
    if(${GEODE_TARGET_PLATFORM} STREQUAL "iOS")
        message(STATUS "building for ios :3c")
        unset(CMAKE_OSX_ARCHITECTURES)
        set(CMAKE_OSX_ARCHITECTURES arm64)
        set(CMAKE_OSX_DEPLOYMENT_TARGET "14.0")
        set(CMAKE_SYSTEM_NAME "iOS")
		add_compile_definitions(TARGET_OS_IOS)
    endif()
else()
    project(ProfileBio VERSION 1.0.0)
endif()

file(GLOB SOURCES
    src/*.cpp
    src/*.h
)

# Set up the mod binary
add_library(${PROJECT_NAME} SHARED ${SOURCES})

if (NOT DEFINED ENV{GEODE_SDK})
    message(FATAL_ERROR "Unable to find Geode SDK! Please define GEODE_SDK environment variable to point to Geode")
else()
    message(STATUS "Found Geode: $ENV{GEODE_SDK}")
endif()

add_subdirectory($ENV{GEODE_SDK} ${CMAKE_CURRENT_BINARY_DIR}/geode)

# and it turns out iOS does not support imgui, truly incredible.
# GEODE_TARGET_PLATFORM did not work
if(NOT ${CMAKE_SYSTEM_NAME} STREQUAL "iOS")
    set(IMGUI_VERSION "v1.89.2")
    CPMAddPackage("gh:matcool/gd-imgui-cocos#b252045")

    CPMAddPackage("gh:ocornut/imgui#${IMGUI_VERSION}")

    target_include_directories(${PROJECT_NAME} PUBLIC ${imgui_SOURCE_DIR})

    target_link_libraries(${PROJECT_NAME} imgui-cocos)
endif()

setup_geode_mod(${PROJECT_NAME})
