<?php
/*
    Name:           Function private
    Purpose:        Framework specific private function library
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Created:  		May 30, 2018 05:30 PM
    Modified:  		June 17, 2018 03:36 AM
*/

namespace sPHP;

function ___LoadConfiguration($Application){
    // Make useful objects accessible through shortcut local variables
    $Environment = $ENV = $Application->Terminal()->Environment();
    $Utility = $UTL = $Environment->Utility();
	//$Debug = $DBG = $Utility->Debug();

    // Execute configuration script
	require "{$Environment->Path()}system/configuration.php";

    return $Configuration;
}

function ___ExecuteApplicationScript($APP, $TPL, $CFG){
	// Make useful objects accessible through shortcut local variables
	#region Update globalized resources
	global $sPHP;

	\sPHP::$Application = \sPHP::$APP = $sPHP["Application"] = $sPHP["APP"] = $APP;
	\sPHP::$Configuration = \sPHP::$CFG = $sPHP["Configuration"] = $sPHP["CFG"] = $CFG;
	$ENV = \sPHP::$Environment = \sPHP::$ENV = $sPHP["Environment"] = $sPHP["ENV"] = $APP->Terminal()->Environment();
	$TRM = \sPHP::$Terminal = \sPHP::$TRM = $sPHP["Terminal"] = $sPHP["TRM"] = $APP->Terminal();
	$SSN = \sPHP::$Session = \sPHP::$SSN = $sPHP["Session"] = $sPHP["SSN"] = $APP->Session();
	$USR = \sPHP::$User = \sPHP::$USR = $sPHP["User"] = $sPHP["USR"] = $SSN->User();
	$UTL = \sPHP::$Utility = \sPHP::$UTL = $sPHP["Utility"] = $sPHP["UTL"] = $ENV->Utility();
	$DBG = \sPHP::$Debug = \sPHP::$DBG = $sPHP["Debug"] = $sPHP["DBG"] = $UTL->Debug();
	\sPHP::$Template = \sPHP::$TPL = $sPHP["Template"] = $sPHP["TPL"] = $TPL;
	$DTB = \sPHP::$Database = \sPHP::$DTB = $sPHP["Database"] = $sPHP["DTB"] = $APP->Database();
	$TBL = \sPHP::$Table = \sPHP::$TBL = $sPHP["Table"] = $sPHP["TBL"] = $CFG["DatabaseTable"];

	if(!isset($CFG["LegacySupport_sPHP_LongLocalVariable"]) || $CFG["LegacySupport_sPHP_LongLocalVariable"]){ // Legacy support
		$Application = $APP;
		$Configuration = $CFG;
		$Environment = $ENV;
		$Terminal = $TRM;
		$Session = $SSN;
		$User = $USR;
		$Utility = $UTL;
		$Debug = $DBG;
		$Template = $TPL;
		$Database = $DTB;
		$Table = $TBL;
	}
	#endregion Update globalized resources

	#region Execute script
	require "{$ENV->Path()}system/pre.php";

	$_POST["_Script"] = strtolower($_POST["_Script"]);
	$DebugCheckpointID = $DBG->StartCheckpoint("script/{$_POST["_Script"]}");

	if(file_exists($ScriptToExecute = "{$ENV->ScriptPath()}{$_POST["_Script"]}.php")){
		require $ScriptToExecute; // Execute script from application
	}
	else{ // Application script not found
		if(($APP->UseSystemScript() || in_array($_POST["_Script"], [
			// Below system scripts are always allowed to be executed
			"user/signin",
			"user/signinaction",
			"user/password/reset",
			"user/signout",
			//"utility/settings/input",
			//"utility/settings/action"
		])) && file_exists($ScriptToExecute = "{$ENV->SystemPath()}script/{$_POST["_Script"]}.php")){
			require $ScriptToExecute; // Execute script from framework
		}
		else{ // Framwork script not found
			if(file_exists($ScriptToExecute = "{$ENV->ScriptPath()}error/http/404.php")){
				require $ScriptToExecute; // Execute error script from application
			}
			else{ // Application error script not found
				if($APP->UseSystemScript() && file_exists($ScriptToExecute = "{$ENV->SystemPath()}script/error/http/404.php")){
					require $ScriptToExecute; // Execute error script from framework
				}
				else{ // Framework error script not found
					trigger_error("Requested script '{$_POST["_Script"]}' not found!", E_USER_ERROR); // Trigger generic error
				}
			}
		}
	}

	$DBG->StopCheckpoint($DebugCheckpointID);

	require "{$ENV->Path()}system/post.php";
	#endregion Execute script
	
	//$APP->Terminal()->Flush(); // Required to set the contents in order of header and main
	$APP->Terminal()->Suspended(false); // Automatically calls the Flush method of Terminal

	if($APP->Terminal()->DocumentType() == DOCUMENT_TYPE_HTML && !isset($_POST["_MainContentOnly"])){ // Execute header & footer if not instructed to suppress
		if(!isset($_POST["_NoHeader"])){ // Header
			$APP->Terminal()->Mode(OUTPUT_BUFFER_MODE_HEADER); // Change buffer to header mode
			$DebugCheckpointID = $DBG->StartCheckpoint("template/header.php");

			// This is put here to exclusively use for Terminals that are real browsing devices
			// Do not waste system resource if User device notificatin is not required
			if($APP->UserDeviceNotification())require __DIR__ . "/include/useruserdevice_add.php"; // Register user device

			require "{$ENV->Path()}template/header.php"; // Execute header script
			$DBG->StopCheckpoint($DebugCheckpointID);
			$APP->Terminal()->Flush(); // Required to set the contents in order of header and main
		}

		if(!isset($_POST["_NoFooter"])){ // Footer
			$APP->Terminal()->Mode(OUTPUT_BUFFER_MODE_MAIN); // Change back buffer to main mode
			$DebugCheckpointID = $DBG->StartCheckpoint("template/footer.php");
			require "{$ENV->Path()}template/footer.php"; // Execute footer script

			// This is put here to exclusively use for Terminals that are real browsing devices
			// Do not waste system resource if User device notificatin is not required
			if($APP->UserDeviceNotification())require __DIR__ . "/include/useruserdevicenotification_fetch.php"; // Fetch UserUserDeviceNotification
			
			$DBG->StopCheckpoint($DebugCheckpointID);
		}
	}

	return true;
}

function ___ExecuteTemplateView($Script, $VAR, $APP, $CFG){
	// Make useful objects accessible through shortcut local variables
	#region Update globalized resources
	global $sPHP;
	
	\sPHP::$Variable = \sPHP::$VAR = $sPHP["Variable"] = $sPHP["VAR"] = $VAR;
	$ENV = \sPHP::$Environment;
	$TRM = \sPHP::$Terminal;
	$SSN = \sPHP::$Session;
	$USR = \sPHP::$User;
	$UTL = \sPHP::$Utility;
	$DBG = \sPHP::$Debug;
	$DTB = \sPHP::$Database;
	$TBL = \sPHP::$Table;

	if(!isset($CFG["LegacySupport_sPHP_LongLocalVariable"]) || $CFG["LegacySupport_sPHP_LongLocalVariable"]){ // Legacy support
		$Variable = $VAR;
		$Application = $APP;
		$Configuration = $CFG;
		$Environment = $ENV;
		$Terminal = $TRM;
		$Session = $SSN;
		$User = $USR;
		$Utility = $UTL;
		$Debug = $DBG;
		$Database = $DTB;
		$Table = $TBL;
	}
	#endregion Update globalized resources

	$DebugCheckpointID = $DBG->StartCheckpoint("template/view/{$Script}");
	require $Script; // Execute script
	$DBG->StopCheckpoint($DebugCheckpointID);

	return true;
}
?>