/*
 * Cloud Mail 2022 Java Edition- Demo Application
 *
 * Copyright (c) 2023 /n software inc. - All rights reserved. - www.nsoftware.com
 *
 */

import java.io.*;

import cloudmail.*;
import java.io.*;

public class amazonses extends ConsoleDemo {
  public static void main(String[] args) {
    Amazonses amazonses = new Amazonses();
    try {
	  amazonses.setAccessKey(prompt("AccessKey"));
      amazonses.setSecretKey(prompt("SecretKey"));
      
	  amazonses.setFrom(prompt("From"));
      amazonses.setSendTo(prompt("To"));
      amazonses.setSubject(prompt("Subject"));

      System.out.println("Enter the message. To end the message, enter \".\" on a single line by itself.");
      System.out.println("Message:");
      String message = "";

      String buffer = input();
      while (buffer.compareTo(".") != 0) {
        message += buffer + "\r\n";
        buffer = input();
      }

      amazonses.setMessageHTML(message);

      //if you want to add attachment:
      System.out.println("Enter file names to attach, or press enter to skip...");
      buffer = prompt("  File Name");
      while (buffer.length() > 0) {
        amazonses.addAttachment(buffer);
        buffer = prompt("  File Name");
      }

      System.out.println("Sending message ...");
      amazonses.send();

      System.out.println("Message sent successfully");
      System.exit(0);
    } catch (CloudMailException ipwe) {
      System.out.println(ipwe.getMessage());
      System.exit(ipwe.getCode());
    } catch (Exception e) {
      System.out.println(e.getMessage());
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




