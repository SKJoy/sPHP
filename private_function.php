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

function ___ExecuteApplicationScript($Application, $Template, $Configuration){
	// Make useful objects accessible through shortcut local variables
	$APP = $Application;
	$DTB = $Database = $Application->Database();
	$ENV = $Environment = $Application->Terminal()->Environment();
	$SSN = $Session = $Application->Session();
	$UTL = $Utility = $Environment->Utility();
	$DBG = $Debug = $Utility->Debug();
	$TRM = $Terminal = $Application->Terminal();
	$TPL = $Template;
	$CFG = $Configuration;
	$TBL = $Table = $Configuration["DatabaseTable"];
	$USR = $User = $Session->User();

	// Execute script
	require "{$Environment->Path()}system/pre.php";

	// Use a call back function in pre.php to allow the developer to check the script to be executed or alter it
	if(($AllowedScript = AllowScriptAccess($_POST["_Script"], $Application)) !== true)$_POST["_Script"] = $AllowedScript;

	$DebugCheckpointID = $Debug->StartCheckpoint("script/{$_POST["_Script"]}");

	if(file_exists($ScriptToExecute = "{$Environment->ScriptPath()}{$_POST["_Script"]}.php")){
		require $ScriptToExecute; // Execute script from application
	}
	else{ // Application script not found
		if(($Application->UseSystemScript() || in_array($_POST["_Script"], [
			// Below system scripts are always allowed to be executed
			"user/signin",
			"user/signinaction",
			"user/signout",
			"utility/settings/input",
			"utility/settings/action"
		])) && file_exists($ScriptToExecute = "{$Environment->SystemPath()}script/{$_POST["_Script"]}.php")){
			require $ScriptToExecute; // Execute script from framework
		}
		else{ // Framwork script not found
			if(file_exists($ScriptToExecute = "{$Environment->ScriptPath()}error/http/404.php")){
				require $ScriptToExecute; // Execute error script from application
			}
			else{ // Application error script not found
				if($Application->UseSystemScript() && file_exists($ScriptToExecute = "{$Environment->SystemPath()}script/error/http/404.php")){
					require $ScriptToExecute; // Execute error script from framework
				}
				else{ // Framework error script not found
					trigger_error("Requested script '{$_POST["_Script"]}' not found!", E_USER_ERROR); // Trigger generic error
				}
			}
		}
	}

	require "{$Environment->Path()}system/post.php";

	$Debug->StopCheckpoint($DebugCheckpointID);
	
	//$Application->Terminal()->Flush(); // Required to set the contents in order of header and main
	$Application->Terminal()->Suspended(false); // Automatically calls the Flush method of Terminal

	if($Application->Terminal()->DocumentType() == DOCUMENT_TYPE_HTML && !isset($_POST["_MainContentOnly"])){ // Execute header & footer if not instructed to suppress
		if(!isset($_POST["_NoHeader"])){ // Header
			$Application->Terminal()->Mode(OUTPUT_BUFFER_MODE_HEADER); // Change buffer to header mode
			$DebugCheckpointID = $Debug->StartCheckpoint("template/header.php");

			// This is put here to exclusively use for Terminals that are real browsing devices
			require __DIR__ . "/include/useruserdevice_add.php"; // Register user device

			require "{$Environment->Path()}template/header.php"; // Execute header script
			$Debug->StopCheckpoint($DebugCheckpointID);
			$Application->Terminal()->Flush(); // Required to set the contents in order of header and main
		}

		if(!isset($_POST["_NoFooter"])){ // Footer
			$Application->Terminal()->Mode(OUTPUT_BUFFER_MODE_MAIN); // Change back buffer to main mode
			$DebugCheckpointID = $Debug->StartCheckpoint("template/footer.php");
			require "{$Environment->Path()}template/footer.php"; // Execute footer script

			// This is put here to exclusively use for Terminals that are real browsing devices
			require __DIR__ . "/include/useruserdevicenotification_fetch.php"; // Fetch UserUserDeviceNotification
			
			$Debug->StopCheckpoint($DebugCheckpointID);
		}
	}

	return true;
}

function ___ExecuteTemplateView($Script, $Variable, $Application, $Configuration){
	// Make useful objects accessible through shortcut local variables
	$VAR = $Variable;
	$APP = $Application;
	$DTB = $Database = $Application->Database();
	$Environment = $ENV = $Application->Terminal()->Environment();
	$Session = $SSN = $Application->Session();
	$Utility = $UTL = $Environment->Utility();
	$Debug = $DBG = $Utility->Debug();
	$Terminal = $TRM = $Application->Terminal();
	$CFG = $Configuration;
	$USR = $User = $Session->User();

	$DebugCheckpointID = $Debug->StartCheckpoint("template/view/{$Script}");
	require $Script; // Execute script
	$Debug->StopCheckpoint($DebugCheckpointID);

	return true;
}
?>