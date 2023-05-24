/*
 * Cloud Mail 2022 Java Edition- Demo Application
 *
 * Copyright (c) 2023 /n software inc. - All rights reserved. - www.nsoftware.com
 *
 */

import java.io.*;

import cloudmail.*;

import java.nio.charset.StandardCharsets;

public class gmail extends ConsoleDemo {
  static Gmail gmail1 = new Gmail();
  static String command; // user's command
  static String[] argument; // arguments following command
  static boolean isListingEmails = false;


  public static void main(String[] args) {
    try{
      gmail1.getOAuth().setClientId("922417066392-f9fomlr35hffi9gkq5te61nr47rtk00q.apps.googleusercontent.com");
      gmail1.getOAuth().setClientSecret("o8tCrbErRHjAh42whuvXGW0R");
      gmail1.getOAuth().setServerAuthURL("https://accounts.google.com/o/oauth2/auth");
      gmail1.getOAuth().setServerTokenURL("https://accounts.google.com/o/oauth2/token");
      gmail1.getOAuth().setAuthorizationScope("https://mail.google.com/");
      gmail1.authorize();

      gmail1.addGmailEventListener(new DefaultGmailEventListener() {
        public void messageInfo(GmailMessageInfoEvent e) {
          if(isListingEmails) {
            System.out.println(e.id);
          }
        }
      });
      DisplayMenu();
      while(true) {
        command = prompt("gmail", ">");
        argument = null;
        if(command.length() == 0)
          command = "?";
        
        argument = command.split("\\s");
        if (argument.length == 0)
          continue;

        switch (argument[0].charAt(0)) {
          case 'l':
            isListingEmails = true;
            gmail1.config("ResponseType=4");
            gmail1.listMessages("", "");
            break;
          case 'v':
            isListingEmails = false;
            gmail1.config("ResponseType=0");
            gmail1.fetchMessageRaw(argument[1].trim());
            String s = new String(gmail1.getMessage(), StandardCharsets.UTF_8);
            System.out.println(s);
            break;
          case 'c':
            messageCompose();
            break;
          case '?':
            DisplayMenu();
            break;
          case 'q':
            System.exit(0);
          default: // allow user to enter only the number of the message they
            // want to view
            try {
              isListingEmails = false;
              gmail1.config("ResponseType=0");
              gmail1.fetchMessageRaw(command);
            } catch (NumberFormatException e) {
              System.out.println("Bad command / Not implemented in demo.");
            }
        }
      }
    } catch (Exception e) {
      System.out.println(e.getMessage());
    }
  }

  public static void DisplayMenu() {
    System.out.println("Gmail commands");
    System.out.println("l                   list next 100 messages");
    System.out.println("v <message id>      view the content of selected message");
    System.out.println("c                   compose a message");
    System.out.println("?                   display options");
    System.out.println("q                   quit");
  }

  public static void messageCompose() {
    try{
      String sendTo = prompt("Send To", ":");
      if (!sendTo.isEmpty())
        gmail1.setMessageTo(sendTo);

      String cc = prompt("CC", ":");
      if (!sendTo.isEmpty())
        gmail1.setMessageCc(cc);


      String bcc = prompt("BCC", ":");
      if (!sendTo.isEmpty())
        gmail1.setMessageBcc(bcc);

      String subject = prompt("Subject", ":");
      if (!sendTo.isEmpty())
        gmail1.setMessageSubject(subject);

      String messageBodyType = prompt("Message Body Type (text/html)", ":");
      while(!messageBodyType.equalsIgnoreCase("text") && !messageBodyType.equalsIgnoreCase("html")) {
        System.out.println("Please enter either text or html as a value!");
        messageBodyType = input();
      }
      gmail1.setMessageBodyContentType(messageBodyType);

      String content = prompt("Message Content", ":");
      if (!sendTo.isEmpty())
        gmail1.setMessageBodyContent(content);

      String attachment = prompt("Attachments(semicolon separated)", ":");
      if (!sendTo.isEmpty() && attachment.length() > 0) {
        String[] array = attachment.split(";");
        boolean flag = true;
        for(int i = 0; i < array.length; i++) {
          File file = new File(array[i].trim());
          if(!file.exists()) {
            flag = false;
            break;
          }
        }
        if(flag)
          gmail1.setMessageAttachments(attachment);
        else
          System.out.println("Attachments invalid. Sending no attachments!");
        
      }
      gmail1.sendMail();
      System.out.println("Message sent!");

    } catch(Exception e) {
      displayError(e);
    }

  }
}

class ConsoleDemo {
  private static BufferedReader bf = new BufferedReader(new InputStreamReader(System.in));

  static String input() {
    try {
      return bf.readLine();
    } catch (IOException ioe) {
      return "";
    }
  }
  static char read() {
    return input().charAt(0);
  }

  static String prompt(String label) {
    return prompt(label, ":");
  }
  static String prompt(String label, String punctuation) {
    System.out.print(label + punctuation + " ");
    return input();
  }

  static String prompt(String label, String punctuation, String defaultVal)
  {
	System.out.print(label + " [" + defaultVal + "] " + punctuation + " ");
	String response = input();
	if(response.equals(""))
		return defaultVal;
	else
		return response;
  }

  static char ask(String label) {
    return ask(label, "?");
  }
  static char ask(String label, String punctuation) {
    return ask(label, punctuation, "(y/n)");
  }
  static char ask(String label, String punctuation, String answers) {
    System.out.print(label + punctuation + " " + answers + " ");
    return Character.toLowerCase(read());
  }

  static void displayError(Exception e) {
    System.out.print("Error");
    if (e instanceof CloudMailException) {
      System.out.print(" (" + ((CloudMailException) e).getCode() + ")");
    }
    System.out.println(": " + e.getMessage());
    e.printStackTrace();
  }
}




