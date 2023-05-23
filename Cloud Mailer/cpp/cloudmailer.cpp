/*
 * Cloud Mail 2022 C++ Edition - Demo Application
 *
 * Copyright (c) 2023 /n software inc. - All rights reserved. - www.nsoftware.com
 *
 */

#include <iostream>
#include <string>
#include "../../include/cloudmail.h"

#define LINE_LEN 80
#define MESSAGE_LEN 1024


int main()
{
    char command[LINE_LEN];     // user's command

    CloudMailer cloudmailer1;

    // Welcome Message
    printf("Welcome to the Cloud Mailer demo.\n");
    printf("Follow the prompts to send an email using the component.\n");
    printf("------------------------------------------------------------\n");

    printf("Which mail service provider would you like to use to send your message? \n  [0] - Amazon SES\n  [1] - GMail\n  [2] - Office365\n");
    fgets(command, LINE_LEN, stdin);
    if (std::stoi(command) == 0) {
        cloudmailer1.SetServiceProvider(0);
        printf("Enter your Amazon secret key: ");
        fgets(command, LINE_LEN, stdin);
        cloudmailer1.SetAccountAmazonAccessKey(command);
        printf("Enter your Amazon secret key: ");
        cloudmailer1.SetAccountAmazonSecretKey(command);
        fgets(command, LINE_LEN, stdin);
    }
    else if (std::stoi(command) == 1) {
        cloudmailer1.SetServiceProvider(1);
        cloudmailer1.SetOAuthServerAuthURL("https://accounts.google.com/o/oauth2/auth");
        cloudmailer1.SetOAuthServerTokenURL("https://accounts.google.com/o/oauth2/token");
        printf("Enter your OAuth Client ID: ");
        fgets(command, LINE_LEN, stdin);
        cloudmailer1.SetOAuthClientId(command);
        printf("Enter your OAuth client secret: ");
        fgets(command, LINE_LEN, stdin);
        cloudmailer1.SetOAuthClientSecret(command);
        printf("Enter desired authorization scopes (leave a space between each one if providing multiple): ");
        fgets(command, LINE_LEN, stdin);
        cloudmailer1.SetOAuthAuthorizationScope(command);
        printf("Authenticating...\n");
        cloudmailer1.Authorize();
        printf("Authentication successful.\n");
    }
    else if (std::stoi(command) == 2) {
        cloudmailer1.SetServiceProvider(2);
        cloudmailer1.SetOAuthServerAuthURL("https://login.microsoftonline.com/common/oauth2/v2.0/authorize");
        cloudmailer1.SetOAuthServerTokenURL("https://login.microsoftonline.com/common/oauth2/v2.0/token");
        printf("Enter your OAuth Client ID: ");
        fgets(command, LINE_LEN, stdin);
        cloudmailer1.SetOAuthClientId(command);
        printf("Enter your OAuth client secret: ");
        fgets(command, LINE_LEN, stdin);
        cloudmailer1.SetOAuthClientSecret(command);
        printf("Enter desired authorization scopes (leave a space between each one if providing multiple): ");
        fgets(command, LINE_LEN, stdin);
        cloudmailer1.SetOAuthAuthorizationScope(command);
        printf("Authenticating...\n");
        cloudmailer1.Authorize();
        printf("Authentication successful.\n");
    }
    else {
        throw std::invalid_argument("Invalid mail service provider.");
    }

    // Prompt for message information.
    printf("Provide email address(es) to receive message (leave a comma between each one if providing multiple): ");
    fgets(command, LINE_LEN, stdin);
    cloudmailer1.SetSendTo(command);
    printf("Provide email address that is sending message: ");
    fgets(command, LINE_LEN, stdin);
    cloudmailer1.SetFrom(command);
    printf("Provide message subject (optional): ");
    fgets(command, LINE_LEN, stdin);
    cloudmailer1.SetSubject(command);
    printf("Provide HTML message text: ");
    fgets(command, MESSAGE_LEN, stdin);
    cloudmailer1.SetMessageHTML(command);
    printf("Would you like to add an attachment? \n[0] - No \n[1] - Yes\n");
    fgets(command, LINE_LEN, stdin);
    if (std::stoi(command) == 1) {
        printf("Enter full path of attachment to add: ");
        fgets(command, LINE_LEN, stdin);
        std::string path = command;
        size_t end = path.find_last_not_of(" \n\r\t\f\v");    
        path = (end == std::string::npos) ? "" : path.substr(0, end + 1);
        cloudmailer1.AddAttachment(path.c_str());
    }

    // Send email
    printf("Sending message...\n");
    if (cloudmailer1.Send() != 0) {
        printf("Error %d: %s", cloudmailer1.GetLastErrorCode(), cloudmailer1.GetLastError());
    }
    else {
        printf("Message sent successfully");
    }
    exit(0);
}



