<?php
/*
    Name:           LoadClass
    Purpose:        Dynamic object class loader
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Created:  		May 30, 2018 05:30 PM
    Modified:  		June 17, 2018 03:36 AM
*/

namespace sPHP;

spl_autoload_register(function($Class){
	if(file_exists($FrameworkClassFile = __DIR__ . "/library/object/" . strtolower(substr(str_replace("\\", "/", $Class), strlen(__NAMESPACE__) + 1)) . ".php")){
		require $FrameworkClassFile; // Load class script from framework
	}
	else{ // Framework class script doesn't exist
		if(file_exists($ApplicationClassFile = pathinfo($_SERVER["SCRIPT_FILENAME"])["dirname"] . "/library/object/" . strtolower(str_replace("\\", "/", pathinfo($Class)["basename"])) . ".php")){
			require $ApplicationClassFile; // Load class script from application root
		}
		else{ // Application class script doesn't exist
			if(file_exists($ApplicationClassFileNested = pathinfo($_SERVER["SCRIPT_FILENAME"])["dirname"] . "/library/object/" . strtolower(str_replace("\\", "/", $Class)) . ".php")){
				require $ApplicationClassFileNested; // Load class script from application with namespace nested path
			}
			else{ // Class script not found anywhere
				trigger_error("Script for Class '{$Class}' not found!", E_USER_ERROR);
			}
		}
	}

	//if(!class_exists($Class))trigger_error("Class '{$Class}' not found!", E_USER_ERROR);
}, true);
?>