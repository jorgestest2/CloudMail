#
# Cloud Mail 2022 Python Edition - Demo Application
#
# Copyright (c) 2023 /n software inc. - All rights reserved. - www.nsoftware.com
#

import sys
import string
from cloudmail import *

input = sys.hexversion<0x03000000 and raw_input or input

cloudmailer1 = CloudMailer()

def fireSSLServerAuthentication(e):
  e.accept = True

try:
  cloudmailer1.on_ssl_server_authentication = fireSSLServerAuthentication

  print("Which mail service provider would you like to use to send your message?")
  print(" [0] - Amazon SES")
  print(" [1] - GMail")
  print(" [2] - Office 365")

  cloudmailer1.set_service_provider(int(input()))

  # Prompt for authentication information.
  if (cloudmailer1.get_service_provider() == 0):
    cloudmailer1.set_account_amazon_access_key(input("Enter your Amazon access key:  "))
    cloudmailer1.set_account_amazon_secret_key(input("Enter your Amazon secret key:  "))
  elif (cloudmailer1.get_service_provider() == 1):
    cloudmailer1.set_o_auth_server_auth_url("https://accounts.google.com/o/oauth2/auth")
    cloudmailer1.set_o_auth_server_token_url("https://accounts.google.com/o/oauth2/token")
    cloudmailer1.set_o_auth_client_id(input("Enter your OAuth client ID:  "))
    cloudmailer1.set_o_auth_client_secret(input("Enter your OAuth client secret:  "))
    cloudmailer1.set_o_auth_authorization_scope(input("Enter desired authorization scopes (leave a space between each one if providing multiple):  "))
    print("Authenticating... ", end = "")
    cloudmailer1.authorize()
    print(" done.")
  elif (cloudmailer1.get_service_provider() == 2):
    cloudmailer1.set_o_auth_server_auth_url("https://login.microsoftonline.com/common/oauth2/v2.0/authorize")
    cloudmailer1.set_o_auth_server_token_url("https://login.microsoftonline.com/common/oauth2/v2.0/token")
    cloudmailer1.set_o_auth_client_id(input("Enter your OAuth client ID:  "))
    cloudmailer1.set_o_auth_client_secret(input("Enter your OAuth client secret:  "))
    cloudmailer1.set_o_auth_authorization_scope(input("Enter desired authorization scopes (leave a space between each one if providing multiple):  "))
    print("Authenticating... ", end = "")
    cloudmailer1.authorize()
    print(" done.")

  # Prompt for message information.
  cloudmailer1.set_send_to(input("Provide email address(es) to receive message (leave a comma between each one if providing multiple):  "))
  cloudmailer1.set_from(input("Provide email address that is sending message:  "))
  cloudmailer1.set_subject(input("Provide message subject (optional):  "))
  cloudmailer1.set_message_html(input("Provide HTML message text:  "))
  print("Would you like to add an attachment?")
  print(" [0] - No")
  print(" [1] - Yes")
  attachmentPrompt = (int)(input())
  if (attachmentPrompt == 1):
    attachmentPath = input("Provide full path of attachment to add:  ")
    if attachmentPath != "":
      cloudmailer1.add_attachment(attachmentPath)

  # Send email.
  print("Sending message... ", end = "")
  cloudmailer1.send()
  print(" sent.")
except ValueError as e:
  print("Must supply a valid number.")
except Exception as e:
  print(e)

