/*
 * Cloud Mail 2022 C++ Edition - Demo Application
 *
 * Copyright (c) 2023 /n software inc. - All rights reserved. - www.nsoftware.com
 *
 */

#include <stdio.h>
#include <ctype.h>
#include <string.h>
#include <stdlib.h>
#include "../../include/cloudmail.h"

#define LINE_LEN 80

char* prompt(char* command, char* punctuation, char* default_str, char* ret) {
	printf("%s [%s] %s ", command, default_str, punctuation);
	fgets(ret, LINE_LEN, stdin);
	if (strlen(ret) == 1) {
		return default_str;
	}
	ret[strlen(ret) - 1] = '\0';
	return ret;
}

int main(int argc, char * argv[])
{
		Office365 office365;

		char command[LINE_LEN];     // user's command
		char buffer[LINE_LEN];      // text buffer
		char *argument;             // arguments following command
		char *currFolder = "";

		int msgnum = 0;             // current message number for next command
		char msg_range[LINE_LEN];
		char msg_limit[10];
		int ret_code;
	
		printf("Press Enter to Authenticate.");
		getchar();

		office365.SetOAuthClientId("cd3ac0b7-c936-4b69-a958-ba45a4fb7963");
		office365.SetOAuthClientSecret("h1I8Q~3BTa.iBJremF5SjLAWwPSc7w164pFjkabZ");
		office365.SetOAuthAuthorizationScope("user.read mail.readwrite mail.send mailboxsettings.readwrite");
		office365.SetOAuthServerAuthURL("https://login.microsoftonline.com/common/oauth2/v2.0/authorize");
		office365.SetOAuthServerTokenURL("https://login.microsoftonline.com/common/oauth2/v2.0/token");

		ret_code = office365.Authorize();

		if (ret_code) goto done;

		printf("\nAuthorization Successful\n");

		printf("Type \"?\" for a list of commands.\n\n");
		while (1)
		{
			printf("> ");
			fgets(command, LINE_LEN, stdin);
			buffer[strlen(command) - 1] = '\0';
			argument = strtok(command, " \n\t");

			if (!strcmp(command, "?") || !strcmp(command, ""))
			{

				printf("Office365 Commands\n");
				printf("l                   list folders\n");
				printf("s <folder>          select a subfolder\n");
				printf("m                   list messages\n");
				printf("v <message number>  view the content of a listed message\n");
				printf("c                   compose and send a message\n");
				printf("?                   display options\n");
				printf("q                   quit\n");

			}

			else if (!strcmp(command, "l"))
			{

				office365.ListFolders(currFolder); // Lists the root child folders.

				for (int i = 0; i < office365.GetFolderCount(); i++)
				{
					printf("  %s\n", office365.GetFolderDisplayName(i));
				}

			}

			else if (!strcmp(command, "s"))
			{

				// Get the folder ID
				boolean found = false;
				office365.ListFolders(currFolder); // Lists the root child folders.

				argument = strtok(NULL, " \t\n");

				for (int i = 0; i < office365.GetFolderCount(); i++)
				{
					if (!strcmp(office365.GetFolderDisplayName(i), argument))
					{
						currFolder = office365.GetFolderId(i);
						found = true;
						break;
					}
				}
				if (!found) {
					printf("No matching subfolder. Setting the current folder to the root folder.\n");
					currFolder = "";
				}

			}

			else if (!strcmp(command, "m"))
			{

				office365.Config("MessagePageSize = 15");
				office365.ListMessages(currFolder, "");

				int i = 0;
				while (true) {
					if (i < office365.GetMessageInfoCount()) {
						printf("  %i: %s\n", i, office365.GetMessageInfoSubject(i));
					}
					else {
						if (!strcmp(prompt("List Additional Messages (y/n)", ":", "y", command), "y"))
							office365.ListMessages(currFolder, "");
						else
							break;
					}
					i = i + 1;
				}

			}

			else if (!strcmp(command, "v"))
			{
				argument = strtok(NULL, " \t\n");
				int messageNum = atoi(argument);
				printf("Subject: %s\n", office365.GetMessageInfoSubject(messageNum));
				printf("From: %s\n", office365.GetMessageInfoFrom(messageNum));
				printf("Date: %s\n", office365.GetMessageInfoReceivedDate(messageNum));
				printf("Content: %s\n", office365.GetMessageInfoBodyContent(messageNum));

			}

			else if (!strcmp(command, "c"))
			{
				office365.SetMessageSubject(prompt("Subject", ":", "Test", command));
				office365.SetMessageBodyContentType("Text");
				office365.SetMessageBodyContent(prompt("Body", ":", "This message was sent using the CloudMail Office365 Class.", command));
				office365.SetMessageTo(prompt("To", ":", "test@gmail.com", command));

				office365.SendMail(true);

			}

			else if (!strcmp(command, "q"))
			{
				exit(0);
			}

			else
			{
				printf("Bad command / Not implemented in demo.\n");
			} // end of command checking

			ret_code = office365.GetLastErrorCode();

			if (ret_code)     // Got an error.  The user is done.
			{
				printf("\nError: %d", ret_code);
				if (office365.GetLastError())
				{
					printf(" \"%s\"\n", office365.GetLastError());
				}
			}
			ret_code = 0;   // flush out error
		}  // end of main while loop

	done:
		if (ret_code)     // Got an error.  The user is done.
		{
			printf("\nError: %d", ret_code);
			if (office365.GetLastError())
			{
				printf(" \"%s\"\n", office365.GetLastError());
			}
		}
		printf("Exiting... (press enter)\n");
		getchar();
		exit(ret_code);
		return 0;
}

