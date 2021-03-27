<?php
namespace sPHP;

$APIResponse["Documentation"]["API"] = [
    "Description" => [
        "List database process",
    ],
    "Argument" => [
        "Type" => ["Type" => "String", "Required" => false, "Description" => "Type of the database; MSACCESS | MSSQL | MYSQL", "Default" => "Application configuration", "Example" => "MySQL", ],
        "Host" => ["Type" => "String", "Required" => false, "Description" => "Database host to connect to", "Default" => "Application configuration", "Example" => "LocalHost", ],
        "User" => ["Type" => "String", "Required" => false, "Description" => "Username to connect with", "Default" => null, "Example" => "root", ],
        "Password" => ["Type" => "String", "Required" => false, "Description" => "Password for the connecting user", "Default" => null, "Example" => "My#DB%Pass", ],
        "Timezone" => ["Type" => "String", "Required" => false, "Description" => "Timezone to use for date/time values", "Default" => "Application configuration", "Example" => "+06:00", ],
        "Sleeping" => ["Type" => "Boolean", "Required" => false, "Description" => "Include sleeping process; TRUE | FALSE", "Default" => "TRUE", "Example" => "FALSE", ],
    ],
	"Note" => [
		"Application user needs to be an Administrator to use this API"
	], 
];

// Set argument POST variables
foreach(explode(", ", "Type, Host, User, Password, Timezone, Sleeping") as $Argument)if(!isset($_POST[$Argument]))$_POST[$Argument] = null;

// Validate required POST variables
//foreach(explode(", ", "Type, Host") as $Argument)if(!strlen($_POST[$Argument]))$APIResponse["Error"][] = ["Code" => 200, "Description" => "Argument missing: {$Argument}", ];

#region Set default value
if(!$_POST["Type"])$_POST["Type"] = $DTB->Type();
if(!$_POST["Host"])$_POST["Host"] = $DTB->Host();
if(!$_POST["User"])$_POST["User"] = $DTB->User();
if(!$_POST["Password"])$_POST["Password"] = $DTB->Password();
if(!$_POST["Timezone"])$_POST["Timezone"] = $DTB->Timezone();
if(!$_POST["Sleeping"])$_POST["Sleeping"] = "TRUE";
#endregion Set default value

#region Validate data type
if(!in_array($_POST[$Argument = "Type"], ["MSACCESS", "MSSQL", "MYSQL"]))$APIResponse["Error"][] = ["Code" => 200, "Description" => "Argument value mismatch: {$Argument}", ];
$_POST["Sleeping"] = $_POST["Sleeping"] == "FALSE" ? false : true;
#endregion Validate data type

#region Logic validation
if($USR->UserGroupIdentifierHighest() != "ADMINISTRATOR")$APIResponse["Error"][] = ["Code" => 200, "Description" => "Access denied", ];
#endregion Logic validation

if(!count($APIResponse["Error"])){ // Process API request, no apparent error detected with arguments
	$DatabaseConnection = new Database($_POST["Type"], $_POST["Host"], $_POST["User"], $_POST["Password"], null, null, null, $_POST["Timezone"], null, null, null);
	$DatabaseConnection->Connect();
	$ThisProcessSignature = md5(microtime(true));
	$SQL = "SHOW FULL PROCESSLIST # THIS IS ME {$USR->Name()} from {$_SERVER["REMOTE_ADDR"]} at " . date("Y-m-d H:i:s") . " with {$ThisProcessSignature}";
	$Recordset = $DatabaseConnection->Query($SQL); //print "<pre>{$SQL}</pre>"; exit;

	if(is_array($Recordset)){
		if($_POST["Sleeping"] && !in_array($DatabaseConnection->Type(), [DATABASE_TYPE_MYSQL, ]))$APIResponse["Warning"][] = "Sleeping process is not detected for this database type";
		$Result = [];

		foreach($Recordset[0] as $Process){
			if($Process["Info"] != $SQL){
				if(in_array($DatabaseConnection->Type(), [DATABASE_TYPE_MYSQL, ])){
					if($_POST["Sleeping"] || $Process["Command"] != "Sleep")$Result[] = $Process;
				}
				else{
					$Result[] = $Process;
				}
			}
		}

		$APIResponse["Response"]["Process"] = $Result;
	}
	else{
		$APIResponse["Error"][] = ["Code" => 200, "Description" => "Invalid response from database engine", ];		
	}
}
?>