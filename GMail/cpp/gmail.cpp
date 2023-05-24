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

class MyGMAIL : public GMail
{
	bool isListingEmails;
public:

	MyGMAIL()
	{
		isListingEmails = false;
	}

	virtual int FireMessageInfo(GMailMessageInfoEventParams* e)
	{
		if (isListingEmails) {
			printf("%s\n", e->Id);
		}
		return 0;
	}

	virtual int FireSSLServerAuthentication(GMailSSLServerAuthenticationEventParams* e)
	{
		if (e->Accept) return 0;
		printf("Server provided the following certificate:\nIssuer: %s\nSubject: %s\n",
			e->CertIssuer, e->CertSubject);
		printf("The following problems have been determined for this certificate: %s\n", e->Status);
		printf("Would you like to continue anyways? [y/n] ");
		if (getchar() == 'y') e->Accept = true;
		else exit(0);
		return 0;
	}
	
	void setIsListingEmais(bool value) {
		isListingEmails = value;
	}

};

int main(int argc, char * argv[])
{
		MyGMAIL gmail;

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

		gmail.SetOAuthClientId("922417066392-f9fomlr35hffi9gkq5te61nr47rtk00q.apps.googleusercontent.com");
		gmail.SetOAuthClientSecret("o8tCrbErRHjAh42whuvXGW0R");
		gmail.SetOAuthServerAuthURL("https://accounts.google.com/o/oauth2/auth");
		gmail.SetOAuthServerTokenURL("https://accounts.google.com/o/oauth2/token");
		gmail.SetOAuthAuthorizationScope("https://mail.google.com/");

		ret_code = gmail.Authorize();

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

				printf("Gmail Commands\n");
				printf("l                   list next 100 messages\n");
				printf("v <message id>      view the content of a message id\n");
				printf("c                   compose and send a message\n");
				printf("?                   display options\n");
				printf("q                   quit\n");

			}

			else if (!strcmp(command, "l"))
			{
				gmail.setIsListingEmais(true);
				gmail.Config("ResponseType=4");
				ret_code = gmail.ListMessages("", "");
			}

			else if (!strcmp(command, "v"))
			{
				argument = strtok(NULL, " \t\n");
				int messageNum = atoi(argument);
				gmail.setIsListingEmais(false);
				gmail.Config("ResponseType=1");
				ret_code = gmail.FetchMessageRaw(argument);

				char* theMessage;
				int messageLength;

				gmail.GetMessageA(theMessage, messageLength);
				printf("%s\n", theMessage);
			}

			else if (!strcmp(command, "c"))
			{
				printf("Send To: ");
				char sendTo[LINE_LEN];
				fgets(sendTo, LINE_LEN, stdin);
				sendTo[strcspn(sendTo, "\n")] = 0;
				gmail.SetMessageTo(sendTo);

				printf("cc: ");
				char cc[LINE_LEN];
				fgets(cc, LINE_LEN, stdin);
				cc[strcspn(cc, "\n")] = 0;
				gmail.SetMessageCc(cc);

				printf("bcc: ");
				char bcc[LINE_LEN];
				fgets(bcc, LINE_LEN, stdin);
				bcc[strcspn(bcc, "\n")] = 0;
				gmail.SetMessageBcc(bcc);

				printf("Subject: ");
				char subject[LINE_LEN];
				fgets(subject, LINE_LEN, stdin);
				subject[strcspn(subject, "\n")] = 0;
				gmail.SetMessageSubject(subject);

				printf("Message Body Type(text or html): ");
				char messageBodyType[LINE_LEN];
				fgets(messageBodyType, LINE_LEN, stdin);
				messageBodyType[strcspn(messageBodyType, "\n")] = 0;
         		while (strcmp(messageBodyType, "text") != 0 && strcmp(messageBodyType, "html") != 0) {
					printf("Type either text or html: ");
					fgets(messageBodyType, LINE_LEN, stdin);
					messageBodyType[strcspn(messageBodyType, "\n")] = 0;
				}
				gmail.SetMessageBodyContentType(messageBodyType);

				printf("Message Content: ");
				char messageContent[LINE_LEN];
				fgets(messageContent, LINE_LEN, stdin);
				messageContent[strcspn(messageContent, "\n")] = 0;
				gmail.SetMessageBodyContent(messageContent);

				printf("Attachments(semicolon separated): ");
				char attachments[LINE_LEN];
				fgets(attachments, LINE_LEN, stdin);
				gmail.SetMessageAttachments(attachments);
				ret_code = gmail.SendMail();

				if (!ret_code) {
					printf("Message sent!\n");
				}

			}

			else if (!strcmp(command, "q"))
			{
				exit(0);
			}

			else
			{
				printf("Bad command / Not implemented in demo.\n");
			} // end of command checking

			ret_code = gmail.GetLastErrorCode();

			if (ret_code)     // Got an error.  The user is done.
			{
				printf("\nError: %d", ret_code);
				if (gmail.GetLastError())
				{
					printf(" \"%s\"\n", gmail.GetLastError());
				}
			}
			ret_code = 0;   // flush out error
		}  // end of main while loop

	done:
		if (ret_code)     // Got an error.  The user is done.
		{
			printf("\nError: %d", ret_code);
			if (gmail.GetLastError())
			{
				printf(" \"%s\"\n", gmail.GetLastError());
			}
		}
		printf("Exiting... (press enter)\n");
		getchar();
		exit(ret_code);
		return 0;
}

