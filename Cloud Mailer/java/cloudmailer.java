/*
 * Cloud Mail 2022 Java Edition- Demo Application
 *
 * Copyright (c) 2023 /n software inc. - All rights reserved. - www.nsoftware.com
 *
 */

import java.io.*;

import cloudmail.*;

public class cloudmailer extends ConsoleDemo {
  static Cloudmailer cloudmailer1 = new Cloudmailer();

  public static void main(String[] args) {
    try {
      cloudmailer1.addCloudmailerEventListener(new DefaultCloudmailerEventListener() {
        public void SSLServerAuthentication(CloudmailerSSLServerAuthenticationEvent e) {
          e.accept = true;
        }
      });

      int servicePrompt = Character.getNumericValue(ask("Which mail service provider would you like to use to send your message", "?", "\n  [0] - Amazon SES\n  [1] - GMail\n  [2] - Office365\n"));

      // Prompt for authentication information.
      if (servicePrompt == 0) {
        cloudmailer1.setServiceProvider(0);
        cloudmailer1.getAccount().setAmazonAccessKey(prompt("Enter your Amazon access key", ":"));
        cloudmailer1.getAccount().setAmazonSecretKey(prompt("Enter your Amazon secret key", ":"));
      } else if (servicePrompt == 1) {
        cloudmailer1.setServiceProvider(1);
        cloudmailer1.getOAuth().setServerAuthURL("https://accounts.google.com/o/oauth2/auth");
        cloudmailer1.getOAuth().setServerTokenURL("https://accounts.google.com/o/oauth2/token");
        cloudmailer1.getOAuth().setClientId(prompt("Enter your OAuth client ID", ":"));
        cloudmailer1.getOAuth().setClientSecret(prompt("Enter your OAuth client secret", ":"));
        cloudmailer1.getOAuth().setAuthorizationScope(prompt("Enter desired authorization scopes (leave a space between each one if providing multiple)", ":"));
        System.out.print("Authenticating... ");
        cloudmailer1.authorize();
        System.out.println(" done.");
      } else if (servicePrompt == 2) {
        cloudmailer1.setServiceProvider(2);
        cloudmailer1.getOAuth().setServerAuthURL("https://login.microsoftonline.com/common/oauth2/v2.0/authorize");
        cloudmailer1.getOAuth().setServerTokenURL("https://login.microsoftonline.com/common/oauth2/v2.0/token");
        cloudmailer1.getOAuth().setClientId(prompt("Enter your OAuth client ID", ":"));
        cloudmailer1.getOAuth().setClientSecret(prompt("Enter your OAuth client secret", ":"));
        cloudmailer1.getOAuth().setAuthorizationScope(prompt("Enter desired authorization scopes (leave a space between each one if providing multiple)", ":"));
        System.out.print("Authenticating... ");
        cloudmailer1.authorize();
        System.out.println(" done.");
      } else {
        throw new Exception("Invalid mail service provider.");
      }

      // Prompt for message information.
      cloudmailer1.setSendTo(prompt("Provide email address(es) to receive message (leave a comma between each one if providing multiple)", ":"));
      cloudmailer1.setFrom(prompt("Provide email address that is sending message", ":"));
      cloudmailer1.setSubject(prompt("Provide message subject (optional)", ":"));
      cloudmailer1.setMessageHTML(prompt("Provide HTML message text", ":"));
      int attachmentPrompt = Character.getNumericValue(ask("Would you like to add an attachment", "?", "\n  [0] - No\n  [1] - Yes\n"));
      if (attachmentPrompt == 1) {
        String attachmentPath = prompt("Provide full path of attachment to add", ":");
        if (!attachmentPath.isEmpty()) {
          cloudmailer1.addAttachment(attachmentPath);
        }
      }

      // Send email.
      System.out.print("Sending message... ");
      cloudmailer1.send();
      System.out.println(" sent.");
    } catch (Exception e) {
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




