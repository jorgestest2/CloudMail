/*
 * Cloud Mail 2022 C++ Edition - Demo Application
 *
 * Copyright (c) 2023 /n software inc. - All rights reserved. - www.nsoftware.com
 *
 */

#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include "../../include/cloudmail.h"

#define LINE_LEN 80
#define MESSAGE_LEN 1024

int main(int argc, char **argv)
{
	AmazonSES amazon_ses;
	char command[LINE_LEN];     // user's command

  // Welcome Message
  printf("Welcome to the Cloud Mail AmazonSES demo.\n");
  printf("Follow the prompts to send an email using the component.\n");
  printf("------------------------------------------------------------\n");

	printf("Access Key: ");
	fgets(command, LINE_LEN, stdin);
	command[strlen(command)-1] = '\0';
	amazon_ses.SetAccessKey(command);

	printf("Secret Key: ");
	fgets(command, LINE_LEN, stdin);
	command[strlen(command)-1] = '\0';
	amazon_ses.SetSecretKey(command);
	
	printf("From: ");
	fgets(command, LINE_LEN, stdin);
	command[strlen(command)-1] = '\0';
	amazon_ses.SetFrom(command);

	printf("To: ");
	fgets(command, LINE_LEN, stdin);
	command[strlen(command)-1] = '\0';
	amazon_ses.SetSendTo(command);

	printf("Subject: ");
	fgets(command, LINE_LEN, stdin);
	command[strlen(command)-1] = '\0';
	amazon_ses.SetSubject(command);

	printf("Enter the message. To end the message, enter \".\" on a single line by itself.\n");
	printf("Message:\n");

	char message[MESSAGE_LEN];
	message[0] = '\0';
	while(fgets(command, LINE_LEN, stdin))
	{
		command[strlen(command)-1] = '\0';
		strcat(message, command);
		if (strcmp(command, ".") == 0)
			break;
	}

	amazon_ses.SetMessageHTML(message);

	//if you want to add attachment:
	printf("Enter file names to attach. (press enter to stop adding attachments) ...\n");
	printf("File Name: ");
	while (fgets(command, LINE_LEN, stdin))
	{
		command[strlen(command)-1] = '\0';
		if (strlen(command) > 0)
		{
			amazon_ses.AddAttachment(command);
			printf("File Name: ");
		}
		else
			break;
	}

	printf("Sending message ...\n");
	int ret_code = amazon_ses.Send();

	if (ret_code)     // Got an error.  The user is done.
	{
		printf( "Error: %d", ret_code );
		if (amazon_ses.GetLastError())
		{
			printf( " \"%s\"\n", amazon_ses.GetLastError() );
		}
	}
	else
	{
		printf("Message sent successfully\n");
	}

	fprintf(stderr, "\npress <return> to continue...\n");
	getchar();
	exit(ret_code);
	return 0;

}








