<?php $sendBuffer = TRUE; ob_start(); ?>
<html>
<head>
<title>Cloud Mail 2022 Demos - GMail</title>
<link rel="stylesheet" type="text/css" href="stylesheet.css">
<meta name="description" content="Cloud Mail 2022 Demos - GMail">
</head>

<body>

<div id="content">
<h1>Cloud Mail - Demo Pages</h1>
<h2>GMail</h2>
<p>Demonstrates the use of the Gmail component.</p>
<a href="default.php">[Other Demos]</a>
<hr/>

<?php
require_once('../include/cloudmail_gmail.php');
require_once('../include/cloudmail_const.php');

?>

<?php
	session_start();
?>

<head>
<style>
.loginScreen {
  float: left;
  text-align: left;
  width: 100%;
}
.container {
  display: table;
  width: 100%;
}
.composeEmail {
  float: left;
  text-align: center;
  width: 20%;
}
.viewLeftColumn {
  display: table-cell;
  float: left;
  text-align: center;
  width: 20%;
}
.viewRightColumn {
  display: table-cell;
  float: right;
  padding-left: 50pt;
  text-align: left;
  width: 75%;
}
</style>
</head>

<?php
	$gmail = new CloudMail_GMail();
	$loggedIn = isset($_SESSION["loggedIn"]) ? $_SESSION["loggedIn"] : false;
	$mode = isset($_SESSION["mode"]) ? $_SESSION["mode"] : "View";
	$pageNumber = isset($_SESSION["pageNumber"]) ? $_SESSION["pageNumber"] : 0;

	// Setup the ReturnURL.
	$pageURL = $_SERVER["HTTPS"] === "ON" ? "https://" : "http://";
	$pageURL .= $_SERVER["HTTP_HOST"];
	$pageURL .= $_SERVER["PHP_SELF"];
?>

<?php
	if (!$loggedIn) {
		$exception = '';
		try {
			// Login process started.
			if (isset($_POST["loginButton"]) && empty($_GET)) {
				// Set component information and redirect to authorization URL.
				$gmail->setOAuthClientProfile(1);
				$gmail->doConfig("OAuthUsePKCE = false");
				if (isset($_POST["clientIDInput"])) {
					$gmail->setOAuthClientId($_POST["clientIDInput"]);
					$_SESSION["clientIDInput"] = $_POST["clientIDInput"];
				}
				if (isset($_POST["clientSecretInput"])) {
					$gmail->setOAuthClientSecret($_POST["clientSecretInput"]);
					$_SESSION["clientSecretInput"] = $_POST["clientSecretInput"];
				}
				if (isset($_POST["serverAuthURLInput"])) {
					$gmail->setOAuthServerAuthURL($_POST["serverAuthURLInput"]);
					$_SESSION["serverAuthURLInput"] = $_POST["serverAuthURLInput"];
				}
				if (isset($_POST["serverTokenURLInput"])) {
					$gmail->setOAuthServerTokenURL($_POST["serverTokenURLInput"]);
					$_SESSION["serverTokenURLInput"] = $_POST["serverTokenURLInput"];
				}
				if (isset($_POST["authScopeInput"])) {
					$gmail->setOAuthAuthorizationScope($_POST["authScopeInput"]);
					$_SESSION["authScopeInput"] = $_POST["authScopeInput"];
				}
				$gmail->setOAuthReturnURL($pageURL);
				header("Location: " . $gmail->getOAuthWebAuthURL());
			}
			else if (isset($_GET["code"])) {
				// Redirected from authorization page.  Get authorization string and complete login process.
				$gmail->setOAuthClientProfile(1);
				$gmail->doConfig("OAuthUsePKCE = false");
				$gmail->setOAuthClientId($_SESSION["clientIDInput"]);
				$gmail->setOAuthClientSecret($_SESSION["clientSecretInput"]);
				$gmail->setOAuthServerAuthURL($_SESSION["serverAuthURLInput"]);
				$gmail->setOAuthServerTokenURL($_SESSION["serverTokenURLInput"]);
				$gmail->setOAuthAuthorizationScope($_SESSION["authScopeInput"]);
				$gmail->setOAuthReturnURL($pageURL);
				$gmail->setOAuthAuthorizationCode($_GET["code"]);
				$gmail->doAuthorize();

				$loggedIn = true;
				$_SESSION["loggedIn"] = $loggedIn;
				$_SESSION["authString"] = $gmail->getOAuthAccessToken();
				header("Location: " . $_SERVER['REQUEST_URI']);
			}
		}
		catch (Exception $e) {
			echo "<font color='red'>" . $e->getMessage() . "</font><br/><br/>";
		}
		finally {
			// Prompt the user to provide their OAuth information.
			echo "<div class='loginScreen'>";
			echo "<form method='post'>";
			echo "<label for='clientIDInput'>Client ID: &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</label>";
			if (isset($_SESSION["clientIDInput"])) {
				echo "<input type='text' id='clientIDInput' name='clientIDInput' size='60' value='" . $_SESSION["clientIDInput"] . "'>";
			}
			else {
				echo "<input type='text' id='clientIDInput' name='clientIDInput' size='60'>";
			}
			echo "&emsp;Obtain and set your Client ID and Client Secret, which can both be found in the <a href='https://code.google.com/apis/console#access'>API Console</a>.<br/>";
			echo "<br/><label for='clientSecretInput'>Client Secret: &emsp;&emsp;&emsp;&emsp;&ensp;</label>";
			if (isset($_SESSION["clientSecretInput"])) {
				echo "<input type='text' id='clientSecretInput' name='clientSecretInput' size='60' value='" . $_SESSION["clientSecretInput"] . "'><br/>";
			}
			else {
				echo "<input type='text' id='clientSecretInput' name='clientSecretInput' size='60'><br/>";
			}
			echo "<br/><label for='serverAuthURLInput'>Server Auth URL: &emsp;&emsp;&ensp;</label>";
			if (isset($_SESSION["serverAuthURLInput"])) {
				echo "<input type='text' id='serverAuthURLInput' name='serverAuthURLInput' size='60' value='" . $_SESSION["serverAuthURLInput"] . "'>";
			}
			else {
				echo "<input type='text' id='serverAuthURLInput' name='serverAuthURLInput' size='60'>";
			}
			echo "&emsp;Enter the Server Auth URL, Server Token URL, and Authorization Scope, which would all be specified in the Google Cloud project pertaining to the Client ID/Secret above.<br/>";
			echo "<br/><label for='serverTokenURLInput'>Server Token URL: &emsp;&emsp;</label>";
			if (isset($_SESSION["serverTokenURLInput"])) {
				echo "<input type='text' id='serverTokenURLInput' name='serverTokenURLInput' size='60' value='" . $_SESSION["serverTokenURLInput"] . "'><br/>";
			}
			else {
				echo "<input type='text' id='serverTokenURLInput' name='serverTokenURLInput' size='60''><br/>";
			}
			echo "<br/><label for='authScopeInput'>Authorization Scope: &emsp;&ensp;</label>";
			if (isset($_SESSION["authScopeInput"])) {
				echo "<input type='text' id='authScopeInput' name='authScopeInput' size='60' value='" . $_SESSION["authScopeInput"] . "'><br/>";
			}
			else {
				echo "<input type='text' id='authScopeInput' name='authScopeInput' size='60'><br/>";
			}
			echo "<br/><input type='submit' id='loginButton' name='loginButton' value='  Login  '><br/><br/>";
			echo "<font color='red'>" . $exception . "</font>";
			echo "</form>";
			echo "</div>";
		}
	}
?>

<?php
	try {
		// Mode button pressed.
		if (isset($_POST["modeButton"])) {
			isset($_SESSION["mode"]) ? $_SESSION["mode"] === "View" ? $_SESSION["mode"] = "Compose" : $_SESSION["mode"] = "View" : $_SESSION["mode"] = "Compose";
			$mode = $mode === "View" ? "Compose" : "View";
		}
		// Logout button pressed.
		else if (isset($_POST["logoutButton"])) {
			$gmail->doReset();
			$loggedIn = false;
			$_SESSION["loggedIn"] = $loggedIn;
			$_SESSION["nextPageToken"] = "";
			$_SESSION["currentPageToken"] = "";
			$_SESSION["pageNumber"] = 0;
			$_SESSION["rawMessageInput"] = "";
			$_SESSION["sendToInput"] = "";
			$_SESSION["ccInput"] = "";
			$_SESSION["bccInput"] = "";
			$_SESSION["subjectInput"] = "";
			$_SESSION["bodyTextArea"] = "";
			$_SESSION["attachmentArea"] = "";
			header("Location: " . $pageURL);
		}
	}
	catch (Exception $e) {
		echo "<font color='red'>" . $e->getMessage() . "</font><br/><br/>";
	}
?>

<form method="post">
	<input type="submit" id="logoutButton" name="logoutButton" value="  Logout  "
		<?php
			if (!$loggedIn) {
				echo "hidden";
			}
		?>
	>
	&ensp;
	<input type="submit" id="resetButton" name="resetButton" value="  Reset  "
		<?php
			if (!$loggedIn) {
				echo "hidden";
			}
		?>
	>
	&ensp;
	<input type="submit" id="modeButton" name="modeButton"
		<?php
			if (!$loggedIn) {
				echo "hidden";
			}
			else {
				echo $mode === "View" ? "value='  Compose Email  '" : "value='  View Messages  '";
			}
		?>
	>
</form>
</div>

<?php
	if ($loggedIn) {
		// Reauthenticate via session variable if necessary.
		if (empty($gmail->getOAuthAccessToken())) {
			if (isset($_SESSION["authString"])) {
				$gmail->setOAuthAccessToken($_SESSION["authString"]);
				$gmail->setOAuthClientProfile(1);
				$gmail->doConfig("OAuthUsePKCE = false");
				$gmail->setOAuthClientId($_SESSION["clientIDInput"]);
				$gmail->setOAuthClientSecret($_SESSION["clientSecretInput"]);
				$gmail->setOAuthServerAuthURL($_SESSION["serverAuthURLInput"]);
				$gmail->setOAuthServerTokenURL($_SESSION["serverTokenURLInput"]);
				$gmail->setOAuthAuthorizationScope($_SESSION["authScopeInput"]);
			}
		}
		echo "<div class='container'>";
		// Composing messages.
		if ($mode === "Compose") {
			echo "<div class='composeEmail'>";
			$status = "";
			try {
				// Send button pressed.
				if (isset($_POST["sendButton"])) {
					if (isset($_POST["sendToInput"])) {
						$gmail->setMessageTo($_POST["sendToInput"]);
						$_SESSION["sendToInput"] = $_POST["sendToInput"];
					}
					if (isset($_POST["ccInput"])) {
						$gmail->setMessageCc($_POST["ccInput"]);
						$_SESSION["ccInput"] = $_POST["ccInput"];
					}
					if (isset($_POST["bccInput"])) {
						$gmail->setMessageBcc($_POST["bccInput"]);
						$_SESSION["bccInput"] = $_POST["bccInput"];
					}
					if (isset($_POST["subjectInput"])) {
						$gmail->setMessageSubject($_POST["subjectInput"]);
						$_SESSION["subjectInput"] = $_POST["subjectInput"];
					}
					$gmail->setMessageBodyContentType($_POST["typeSelect"]);
					if (isset($_POST["bodyTextArea"])) {
						$gmail->setMessageBodyContent($_POST["bodyTextArea"]);
						$_SESSION["bodyTextArea"] = $_POST["bodyTextArea"];
					}
					if (isset($_POST["attachmentArea"])) {
						$gmail->setMessageAttachments($_POST["attachmentArea"]);
						$_SESSION["attachmentArea"] = $_POST["attachmentArea"];
					}
					$gmail->doSendMail();
				}
				// Reset button pressed.
				else if (isset($_POST["resetButton"])) {
					$_SESSION["sendToInput"] = "";
					$_SESSION["ccInput"] = "";
					$_SESSION["bccInput"] = "";
					$_SESSION["subjectInput"] = "";
					$_SESSION["bodyTextArea"] = "";
					$_SESSION["attachmentArea"] = "";
				}
			}
			catch (Exception $e) {
				$status = $e->getMessage();
			}
			finally {
				echo "<form method='post'>";
				echo "<label for='sendToInput'>Send To: &emsp;&ensp;</label>";
				if (isset($_POST["sendToInput"]) && !isset($_POST["resetButton"])) {
					echo "<input type='text' id='sendToInput' name='sendToInput' placeholder='example@gmail.com' value='" . $_POST["sendToInput"] . "'><br/>";
				}
				else if (isset($_SESSION["sendToInput"]) && !isset($_POST["resetButton"])) {
					echo "<input type='text' id='sendToInput' name='sendToInput' placeholder='example@gmail.com' value='" . $_SESSION["sendToInput"] . "'><br/>";
				}
				else {
					echo "<input type='text' id='sendToInput' name='sendToInput' placeholder='example@gmail.com'><br/>";
				}
				echo "<br/><label for='ccInput'>CC: &emsp;&emsp;&emsp;&ensp;</label>";
				if (isset($_POST["ccInput"]) && !isset($_POST["resetButton"])) {
					echo "<input type='text' id='ccInput' name='ccInput' placeholder='example@gmail.com' value='" . $_POST["ccInput"] . "'><br/>";
				}
				else if (isset($_SESSION["ccInput"]) && !isset($_POST["resetButton"])) {
					echo "<input type='text' id='ccInput' name='ccInput' placeholder='example@gmail.com' value='" . $_SESSION["ccInput"] . "'><br/>";
				}
				else {
					echo "<input type='text' id='ccInput' name='ccInput' placeholder='example@gmail.com'><br/>";
				}
				echo "<br/><label for='bccInput'>BCC: &emsp;&emsp;&emsp;</label>";
				if (isset($_POST["bccInput"]) && !isset($_POST["resetButton"])) {
					echo "<input type='text' id='bccInput' name='bccInput' placeholder='example@gmail.com' value='" . $_POST["bccInput"] . "'><br/>";
				}
				else if (isset($_SESSION["bccInput"]) && !isset($_POST["resetButton"])) {
					echo "<input type='text' id='bccInput' name='bccInput' placeholder='example@gmail.com' value='" . $_SESSION["bccInput"] . "'><br/>";
				}
				else {
					echo "<input type='text' id='bccInput' name='bccInput' placeholder='example@gmail.com'><br/>";
				}
				echo "<br/><label for='subjectInput'>Subject: &emsp;&emsp;</label>";
				if (isset($_POST["subjectInput"]) && !isset($_POST["resetButton"])) {
					echo "<input type='text' id='subjectInput' name='subjectInput' placeholder='example@gmail.com' value='" . $_POST["subjectInput"] . "'><br/>";
				}
				else if (isset($_SESSION["subjectInput"]) && !isset($_POST["resetButton"])) {
					echo "<input type='text' id='subjectInput' name='subjectInput' placeholder='example@gmail.com' value='" . $_SESSION["subjectInput"] . "'><br/>";
				}
				else {
					echo "<input type='text' id='subjectInput' name='subjectInput' placeholder='example@gmail.com'><br/>";
				}
				echo "<br /<label>Type: &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</label>";
				echo "<select name='typeSelect' name='typeSelect'>";
				echo "<option value='text'>Text</option>";
				echo "<option value='html'>HTML</option>";
				echo "</select><br/>";
				echo "<style>textarea { vertical-align: middle }</style>";
				echo "<br/><label for='bodyTextArea'>Body: &emsp;&emsp;&emsp;</label>";
				if (isset($_POST["bodyTextArea"]) && !isset($_POST["resetButton"])) {
					echo "<textarea id='bodyTextArea' name='bodyTextArea' rows='6' columns='50' placeholder='Message goes here.'>" . $_POST["bodyTextArea"] . "</textarea><br/>";
				}
				else if (isset($_SESSION["bodyTextArea"]) && !isset($_POST["resetButton"])) {
					echo "<textarea id='bodyTextArea' name='bodyTextArea' rows='6' columns='50' placeholder='Message goes here.'>" . $_SESSION["bodyTextArea"] . "</textarea><br/>";
				}
				else {
					echo "<textarea id='bodyTextArea' name='bodyTextArea' rows='6' columns='50' placeholder='Message goes here.'></textarea><br/>";
				}
				echo "<br/><label for='attachmentArea'>Attachments: </label>";
				if (isset($_POST["attachmentArea"]) && !isset($_POST["resetButton"])) {
					echo "<textarea id='attachmentArea' name='attachmentArea' rows='6' columns='50' placeholder='Attachments go here.\nMust include complete file path(s).\nFor multiple attachments, separate files with semicolons (no spaces).'>" . $_POST["attachmentArea"] . "</textarea><br/>";
				}
				else if (isset($_SESSION["attachmentArea"]) && !isset($_POST["resetButton"])) {
					echo "<textarea id='attachmentArea' name='attachmentArea' rows='6' columns='50' placeholder='Attachments go here.\nMust include complete file path(s).\nFor multiple attachments, separate files with semicolons (no spaces).'>" . $_SESSION["attachmentArea"] . "</textarea><br/>";
				}
				else {
					echo "<textarea id='attachmentArea' name='attachmentArea' rows='6' columns='50' placeholder='Attachments go here.\nMust include complete file path(s).\nFor multiple attachments, separate files with semicolons (no spaces).'></textarea><br/>";
				}
				echo "<br/><input type='submit' name='sendButton' value='  Send Email  '><br/>";
				echo "</form>";
				echo "<font color='red'>" . $status . "</font>";
			}
			echo "</div>";
		}
		// Viewing messages (default mode).
		else {
			echo "<div class='viewLeftColumn'>";
			try {
				// Restore page token according to user action.
				// Next page button pressed.
				if (isset($_POST["nextPageButton"])) {
					if (isset($_SESSION["nextPageToken"])) {
						$gmail->setNextPageToken($_SESSION["nextPageToken"]);
					}
				}
				// Some other button except the next page button and reset button pressed.
				else if (!empty($_POST) && !isset($_POST["resetButton"])) {
					if (isset($_SESSION["currentPageToken"])) {
						$gmail->setNextPageToken($_SESSION["currentPageToken"]);
					}
				}
				$gmail->doConfig("ResponseType = 4");
				$gmail->doListMessages("", "");
				// Update page and token information according to user action.
				if (empty($_POST)) {
					if (isset($_SESSION["nextPageToken"])) {
						$_SESSION["currentPageToken"] = $_SESSION["nextPageToken"];
					}
					$_SESSION["nextPageToken"] = $gmail->getNextPageToken();
				}
				// Next page button pressed.
				else if (isset($_POST["nextPageButton"])) {
					$pageNumber++;
					$_SESSION["pageNumber"] = $pageNumber;
					if (isset($_SESSION["nextPageToken"])) {
						$_SESSION["currentPageToken"] = $_SESSION["nextPageToken"];
					}
					$_SESSION["nextPageToken"] = $gmail->getNextPageToken();
				}
				// Reset button pressed.
				else if (isset($_POST["resetButton"])) {
					$pageNumber = 0;
					$_SESSION["pageNumber"] = $pageNumber;
					$_SESSION["currentPageToken"] = "";
					$_SESSION["nextPageToken"] = $gmail->getNextPageToken();
				}
				// Retrieve message IDs.
				echo "<h3>Message ID (" . $pageNumber * 100 . " - " . ($pageNumber * 100 + $gmail->getMessageInfoCount()) . ")";
				echo "<br/><form method='post'>";
				echo "<input type='submit' name='nextPageButton' value='  Next Page  '";
				if (empty($gmail->getNextPageToken())) {
					echo "hidden";
				}
				echo ">";
				echo "</form></h3>";
				for ($index = 0; $index < $gmail->getMessageInfoCount(); $index++) {
					echo $gmail->getMessageInfoId($index) . "<br/>";
				}
			}
			catch (Exception $e) {
				echo "<h3>Message ID</h3>";
				echo "<font color='red'>" . $e->getMessage() . "</font><br/><br/>";
			}
			echo "</div>";
			echo "<div class='viewRightColumn'>";
			$exception = "";
			try {
				// View button pressed.
				if (isset($_POST["rawMessageViewButton"])) {
					$gmail->doFetchMessageRaw($_POST["rawMessageInput"]);
					$_SESSION["rawMessageInput"] = $_POST["rawMessageInput"];
				}
				// Reset or clear buttons pressed.
				else if (isset($_POST["resetButton"]) || isset($_POST["clearButton"])) {
					$_SESSION["rawMessageInput"] = "";
				}
				// Some other button pressed, so check for session variable to repopulate raw message content.
				else if (isset($_SESSION["rawMessageInput"])) {
					$gmail->doFetchMessageRaw($_SESSION["rawMessageInput"]);
				}
			}
			catch (Exception $e) {
				$exception = $e->getMessage();
			}
			finally {
				// Show raw message content if requested and valid.
				echo "<h3>Raw Message";
				echo "<form method='post'>";
				echo "<input type='submit' name='rawMessageViewButton' value='  View  '>";
				echo "&ensp;";
				if (isset($_POST["rawMessageInput"]) && !isset($_POST["clearButton"]) && !isset($_POST["resetButton"])) {
					echo "<input type='text' name='rawMessageInput' placeholder='Message ID to View' value=" . $_POST["rawMessageInput"] . ">";
				}
				else if (isset($_SESSION["rawMessageInput"]) && !isset($_POST["clearButton"]) && !isset($_POST["resetButton"])) {
					echo "<input type='text' name='rawMessageInput' placeholder='Message ID to View' value=" . $_SESSION["rawMessageInput"] . ">";
				}
				else {
					echo "<input type='text' name='rawMessageInput' placeholder='Message ID to View'>";
				}
				echo "&ensp;";
				echo "<input type='submit' name='clearButton' value='  Clear  '>";
				echo "</form></h3>";
				echo empty($exception) ? $gmail->getMessage() == -1 ? "Choose a message to view." : $gmail->getMessage() : "<font color='red'>" . $exception . "</font><br/><br/>";
			}
			echo "</div>";
		}
		echo "</div>";
	}
?>
</div>


<br/>
<br/>
<br/>
<hr/>
NOTE: These pages are simple demos, and by no means complete applications.  They
are intended to illustrate the usage of the Cloud Mail objects in a simple,
straightforward way.  What we are hoping to demonstrate is how simple it is to
program with our components.  If you want to know more about them, or if you have
questions, please visit <a href="http://www.nsoftware.com/?demopg-EMPHA" target="_blank">www.nsoftware.com</a> or
contact our technical <a href="http://www.nsoftware.com/support/">support</a>.
<br/>
<br/>
Copyright (c) 2023 /n software inc. - All rights reserved.
<br/>
<br/>
</div>

<div id="footer">
<center>
Cloud Mail 2022 - Copyright (c) 2023 /n software inc. - All rights reserved. - For more information, please visit our website at <a href="http://www.nsoftware.com/?demopg-EMPHA" target="_blank">www.nsoftware.com</a>.
</center>
</div>

</body>
</html>

<?php if ($sendBuffer) ob_end_flush(); else ob_end_clean(); ?>
