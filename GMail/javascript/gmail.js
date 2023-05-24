/*
 * Cloud Mail 2022 JavaScript Edition - Demo Application
 *
 * Copyright (c) 2023 /n software inc. - All rights reserved. - www.nsoftware.com
 *
 */
 
const readline = require("readline");
const cloudmail = require("@nsoftware/cloudmail");

if(!cloudmail) {
  console.error("Cannot find cloudmail.");
  process.exit(1);
}
let rl = readline.createInterface({
  input: process.stdin,
  output: process.stdout
});

let gmail1 = new cloudmail.gmail();
let isListingEmails = false;

main();

async function main() {
  // Set up event handlers.
  gmail1.on("MessageInfo", (e) => {
    if (isListingEmails)
      console.log(e.id);
  });

  // Authorize the client.
  gmail1.getOAuth().setClientId("922417066392-f9fomlr35hffi9gkq5te61nr47rtk00q.apps.googleusercontent.com");
  gmail1.getOAuth().setClientSecret("o8tCrbErRHjAh42whuvXGW0R");
  gmail1.getOAuth().setServerAuthURL("https://accounts.google.com/o/oauth2/auth");
  gmail1.getOAuth().setServerTokenURL("https://accounts.google.com/o/oauth2/token");
  gmail1.getOAuth().setAuthorizationScope("https://mail.google.com/");
  gmail1.authorize();

  // Prompt and wait for user command input.
  console.log("\nWelcome to the Cloud Mail GMail Demo.");
  console.log("Follow the prompts to view and send emails using the component.");
  console.log("------------------------------------------------------------\n");
  displayMenu();
  menuPrompt();
  
  // Handle incoming commands.
  rl.on("line", async (line) => {
    // Handle follow-up prompts.
    switch (lastPrompt) {
      case "chooseMessage":
        try {
          isListingEmails = false;
          gmail1.config("ResponseType = 0");
          await gmail1.fetchMessageRaw(line.trim());
          console.log(gmail1.getMessage());
          lastPrompt = "";
        } catch(e) {
          displayError(e);
        }
      break;
      case "sendTo":
        if (line !== "") {
          gmail1.setMessageTo(line.trim());
          prompt("cc", "CC", ":", "");
        } else {
          console.log("\nMust specify at least one email recipient.");
          lastPrompt = "";
        }
      break;
      case "cc":
        gmail1.setMessageCc(line.trim());
        prompt("bcc", "BCC", ":", "");
      break;
      case "bcc":
        gmail1.setMessageBcc(line.trim());
        prompt("subject", "Subject", ":", "");
      break;
      case "subject":
        gmail1.setMessageSubject(line.trim());
        prompt("messageBodyType", "Message Body Type (text/html)", ":", "");
      break;
      case "messageBodyType":
        if (line.toLowerCase() === "text" || line.toLowerCase() === "html") {
          gmail1.setMessageBodyContentType(line);
          prompt("content", "Message Content", ":", "");
        } else {
          console.log("\nPlease enter either text or html as a value.");
          prompt("messageBodyType", "MessageBodyType (text/html)", ":", "");
        }
      break;
      case "content":
        gmail1.setMessageBodyContent(line);
        prompt("attachment", "Add Attachment(s) (separated by semicolons)", ":", "");
      break;
      case "attachment":
        gmail1.setMessageAttachments(line.trim());
        console.log("\nSending email...");
        await gmail1.sendMail();
        console.log("\nEmail sent!");
        lastPrompt = "";
      break;
    }

    // Handle menu commands.
    switch (line) {
      case "l":
        isListingEmails = true;
        gmail1.config("ResponseType = 4");
        await gmail1.listMessages("", "");
        menuPrompt();
      break;
      case "v":
        prompt("chooseMessage", "Message to view", ":", "");
      break;
      case "c":
        prompt("sendTo", "Send to", ":", "");
      break;
      case "?":
        displayMenu();
        menuPrompt();
      break;
      case "q":
        process.exit(0);
      default:
        if (lastPrompt === "") {
          displayMenu();
          menuPrompt();
        }
      break;
    }
  });
}

function displayError(e) {
  process.stdout.write("Error");
  if (e instanceof cloudmail.gmail) {
    console.error(` (${e.code})`);
  }
  console.error(`: ${e.message}`);
  console.trace();
  process.exit(1);
}

function displayMenu() {
  console.log("\nGMail Commands:");
  console.log("l                   list next 100 messages");
  console.log("v                   view the content of choice message");
  console.log("c                   compose a message");
  console.log("?                   display menu options");
  console.log("q                   quit\n");
}

function menuPrompt() {
  process.stdout.write("gmail> ");
  lastPrompt = "";
}



function prompt(promptName, label, punctuation, defaultVal)
{
  lastPrompt = promptName;
  lastDefault = defaultVal;
  process.stdout.write(`${label} [${defaultVal}] ${punctuation} `);
}
