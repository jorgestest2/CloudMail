#
# Cloud Mail 2022 Python Edition - Demo Application
#
# Copyright (c) 2023 /n software inc. - All rights reserved. - www.nsoftware.com
#

import sys
import string
from cloudmail import *

input = sys.hexversion<0x03000000 and raw_input or input

import traceback

gmail1 = GMail()
command = ""
arguments = []
isListingEmails = False

def fire_message_info(e):
  if isListingEmails:
    print(e.id)

def display_error(e):
  print("Error", end = '')
  if isinstance(e, CloudMailError):
    print(" (" + str(e.code) + ")", end = '')
  print(": " + str(e.message))
  traceback.print_exc()

def display_menu():
  print("GMail Commands:")
  print("l                   list next 100 messages")
  print("v <message id>      view the content of selected message")
  print("c                   compose a message")
  print("?                   display options")
  print("q                   quit")

def message_compose():
  try:
    sendTo = input("Send To: ")
    if len(sendTo) != 0:
      gmail1.set_message_to(sendTo)

    cc = input("CC: ")
    if len(cc) != 0:
      gmail1.set_message_cc(cc)

    bcc = input("BCC: ")
    if len(bcc) != 0:
      gmail1.set_message_bcc(bcc)

    subject = input("Subject: ")
    if len(subject) != 0:
      gmail1.set_message_subject(subject)

    messageBodyType = input("Message Body Type (text/html): ")
    while messageBodyType.casefold() != "text" and messageBodyType.casefold() != "html":
      print("Please enter either text or html as a value!")
      messageBodyType = input("Message Body Type (text/html): ")
    gmail1.set_message_body_content_type(messageBodyType)

    content = input("Message Content: ")
    if len(sendTo) != 0:
      gmail1.set_message_body_content(content)

    attachment = input("Attachments (semicolon separated): ")
    if len(sendTo) != 0 and len(attachment) > 0:
      attachmentList = attachment.split(";")
      for attachmentItem in attachmentList:
        if os.path.isfile(attachmentItem.strip()) is False:
          print("Attachments invalid.  Sending no attachments!")
          break # skips subsequent else clause
      else:
        gmail1.set_message_attachments(attachment)

    gmail1.send_mail()
    print("Message sent!")
  except Exception as e:
    display_error(e)

try:
  gmail1.set_o_auth_client_id("922417066392-f9fomlr35hffi9gkq5te61nr47rtk00q.apps.googleusercontent.com")
  gmail1.set_o_auth_client_secret("o8tCrbErRHjAh42whuvXGW0R")
  gmail1.set_o_auth_server_auth_url("https://accounts.google.com/o/oauth2/auth")
  gmail1.set_o_auth_server_token_url("https://accounts.google.com/o/oauth2/token")
  gmail1.set_o_auth_authorization_scope("https://mail.google.com/")
  gmail1.authorize()

  gmail1.on_message_info = fire_message_info

  display_menu()

  while (True):
    command = input("gmail> ")
    argument = None
    if len(command) == 0:
      command = "?"

    argument = command.split()
    if len(argument) == 0:
      continue

    if argument[0][0] == 'l':
      isListingEmails = True
      gmail1.config("ResponseType = 4")
      gmail1.list_messages("", "")
    elif argument[0][0] == 'v':
      isListingEmails = False
      gmail1.config("ResponseType = 0")
      gmail1.fetch_message_raw(argument[1].strip())
      print(gmail1.get_message())
    elif argument[0][0] == 'c':
      message_compose()
    elif argument[0][0] == '?':
      display_menu()
    elif argument[0][0] == 'q':
      sys.exit(0)

except Exception as e:
  display_error(e)



