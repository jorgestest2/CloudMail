/*
 * Cloud Mail 2022 .NET Edition - Demo Application
 * Copyright (c) 2023 /n software inc. - All rights reserved. - www.nsoftware.com
 */

using System.Collections.Generic;
using System;
using System.Threading.Tasks;
using nsoftware.async.CloudMail;

class cloudmailerDemo
{
  private static Cloudmailer cloudmailer;

  private static void cloudmailer_OnSSLServerAuthentication(object sender, CloudmailerSSLServerAuthenticationEventArgs e)
  {
    if (e.Accept) return;
    Console.Write("Server provided the following certificate:\nIssuer: " + e.CertIssuer + "\nSubject: " + e.CertSubject + "\n");
    Console.Write("The following problems have been determined for this certificate: " + e.Status + "\n");
    Console.Write("Would you like to continue anyways? [y/n] ");
    if (Console.Read() == 'y') e.Accept = true;
  }

  static async Task Main(string[] args)
  {
    cloudmailer = new Cloudmailer();

    try
    {
      cloudmailer.OnSSLServerAuthentication += cloudmailer_OnSSLServerAuthentication;

      Console.WriteLine("Which mail service provider would you like to use to send your message?");
      Console.WriteLine("   [0] - Amazon SES");
      Console.WriteLine("   [1] - GMail");
      Console.WriteLine("   [2] - Office 365");

      int servicePrompt = int.Parse(Console.ReadLine());

      // Prompt for authentication information.
      if (servicePrompt == 0)
      {
        cloudmailer.ServiceProvider = CloudmailerServiceProviders.spAmazonSES;

        Console.Write("Enter your Amazon access key:  ");
        cloudmailer.Account.AmazonAccessKey = Console.ReadLine();

        Console.Write("Enter your Amazon secret key:  ");
        cloudmailer.Account.AmazonSecretKey = Console.ReadLine();
      }
      else if (servicePrompt == 1)
      {
        cloudmailer.ServiceProvider = CloudmailerServiceProviders.spGMail;
        cloudmailer.OAuth.ServerAuthURL = "https://accounts.google.com/o/oauth2/auth";
        cloudmailer.OAuth.ServerTokenURL = "https://accounts.google.com/o/oauth2/token";

        Console.Write("Enter your OAuth client ID:  ");
        cloudmailer.OAuth.ClientId = Console.ReadLine();

        Console.Write("Enter your OAuth client secret:  ");
        cloudmailer.OAuth.ClientSecret = Console.ReadLine();

        Console.Write("Enter desired authorization scopes (leave a space between each one if providing multiple):  ");
        cloudmailer.OAuth.AuthorizationScope = Console.ReadLine();

        Console.Write("Authenticating... ");
        await cloudmailer.Authorize();
        Console.WriteLine(" done.");
      }
      else if (servicePrompt == 2)
      {
        cloudmailer.ServiceProvider = CloudmailerServiceProviders.spOffice365;
        cloudmailer.OAuth.ServerAuthURL = "https://login.microsoftonline.com/common/oauth2/v2.0/authorize";
        cloudmailer.OAuth.ServerTokenURL = "https://login.microsoftonline.com/common/oauth2/v2.0/token";

        Console.Write("Enter your OAuth client ID:  ");
        cloudmailer.OAuth.ClientId = Console.ReadLine();

        Console.Write("Enter your OAuth client secret:  ");
        cloudmailer.OAuth.ClientSecret = Console.ReadLine();

        Console.Write("Enter desired authorization scopes (leave a space between each one if providing multiple):  ");
        cloudmailer.OAuth.AuthorizationScope = Console.ReadLine();

        Console.Write("Authenticating... ");
        await cloudmailer.Authorize();
        Console.WriteLine(" done.");
      }
      else
      {
        throw new Exception("\nInvalid mail service provider.\n");
      }

      // Prompt for message information.
      Console.Write("Provide email address(es) to receive message (leave a comma between each one if providing multiple):  ");
      cloudmailer.SendTo = Console.ReadLine();

      Console.Write("Provide email address that is sending message:  ");
      cloudmailer.From = Console.ReadLine();

      Console.Write("Provide message subject (optional):  ");
      cloudmailer.Subject = Console.ReadLine();

      Console.Write("Provide HTML message text:  ");
      cloudmailer.MessageHTML = Console.ReadLine();

      Console.WriteLine("Would you like to add an attachment?");
      Console.WriteLine("   [0] - No");
      Console.WriteLine("   [1] - Yes");
      int attachmentPrompt = int.Parse(Console.ReadLine());
      if (attachmentPrompt == 1)
      {
        Console.Write("Provide full path of attachment to add: ");
        string attachmentPath = Console.ReadLine();
        if (!string.IsNullOrEmpty(attachmentPath)) await cloudmailer.AddAttachment(attachmentPath);
      }

      // Send email.
      Console.Write("Sending message... ");
      await cloudmailer.Send();
      Console.WriteLine(" sent.");
    }
    catch (Exception e)
    {
      Console.WriteLine(e.Message);
    }

    Console.WriteLine("\nPress any key to exit...");
    Console.ReadKey();
  }
}





class ConsoleDemo
{
  public static Dictionary<string, string> ParseArgs(string[] args)
  {
    Dictionary<string, string> dict = new Dictionary<string, string>();

    for (int i = 0; i < args.Length; i++)
    {
      // If it starts with a "/" check the next argument.
      // If the next argument does NOT start with a "/" then this is paired, and the next argument is the value.
      // Otherwise, the next argument starts with a "/" and the current argument is a switch.

      // If it doesn't start with a "/" then it's not paired and we assume it's a standalone argument.

      if (args[i].StartsWith("/"))
      {
        // Either a paired argument or a switch.
        if (i + 1 < args.Length & !args[i + 1].StartsWith("/"))
        {
          // Paired argument.
          dict.Add(args[i].TrimStart('/'), args[i + 1]);
          // Skip the value in the next iteration.
          i++;
        }
        else
        {
          // Switch, no value.
          dict.Add(args[i].TrimStart('/'), "");
        }
      }
      else
      {
        // Standalone argument. The argument is the value, use the index as a key.
        dict.Add(i.ToString(), args[i]);
      }
    }
    return dict;
  }
}