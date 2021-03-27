<?php
namespace sPHP;

$APIResponse["Documentation"]["API"] = [
    "Description" => [
        "Ping the system for reponse. Also works as an echo reponse.",
    ],
    "Argument" => [
        "Message" => ["Type" => "String", "Required" => false, "Default" => null, "Description" => "Message to echo", "Note" => null, "Method" => "GET | POST", ], 
    ],
	"Note" => [
		//"Application user needs to be an Administrator to use this API"
	], 
];

// Set argument POST variables
foreach(explode(", ", "Message") as $Argument)if(!isset($_POST[$Argument]))$_POST[$Argument] = null;

// Validate required POST variables
//foreach(explode(", ", "Type, Host") as $Argument)if(!strlen($_POST[$Argument]))$APIResponse["Error"][] = ["Code" => 200, "Description" => "Argument missing: {$Argument}", ];

#region Set default value
/*
if(!$_POST["Type"])$_POST["Type"] = $DTB->Type();
if(!$_POST["Host"])$_POST["Host"] = $DTB->Host();
if(!$_POST["User"])$_POST["User"] = $DTB->User();
if(!$_POST["Password"])$_POST["Password"] = $DTB->Password();
if(!$_POST["Timezone"])$_POST["Timezone"] = $DTB->Timezone();
if(!$_POST["Sleeping"])$_POST["Sleeping"] = "TRUE";
*/
#endregion Set default value

#region Validate data type
if(is_array($_POST[$Argument = "Message"]))$APIResponse["Error"][] = ["Code" => 200, "Description" => "Not a string: {$Argument}", ];
//$_POST["Sleeping"] = $_POST["Sleeping"] == "FALSE" ? false : true;
#endregion Validate data type

#region Logic validation
//if($USR->UserGroupIdentifierHighest() != "ADMINISTRATOR")$APIResponse["Error"][] = ["Code" => 200, "Description" => "Access denied", ];
#endregion Logic validation

if(!count($APIResponse["Error"])){ // Process API request, no apparent error detected with arguments
	$APIResponse["Response"]["Time"] = date("r");
	if($_POST["Message"])$APIResponse["Response"]["Message"] = $_POST["Message"];
}
?>