<?php
namespace sPHP;

$ProcessTimeStart = microtime(true);
require __DIR__ . "/constant.php";

$APIResponse = [
	"Error" => [ // Error node array should contain one or more errors as needed; empty node means no error
		/*[
			"Code" => 0,
			"Description" => null,
		],*/ 
	],
	"Warning" => [ // Warning node array should contain one or more warnings as needed; empty node means no warning
		/*[
			"Code" => 0,
			"Description" => null,
		],*/
	],
	"User" => array_merge([
		"ID" => intval($User->ID()),
		"GroupIdentifierHighest" => $User->UserGroupIdentifierHighest(),
        "Name" => $User->Name(), 
	], isset($_POST["_Debug"]) ? [
		"Email" => $User->Email(),
		"Group" => $User->UserGroup(),
		"GroupIdentifier" => $User->UserGroupIdentifier(),
	] : []),
	"Response" =>	[], // Response array should contain the actual data to deliver
	"Documentation" => [
		"API" => [
			"Argument" => [ // Argument array to contain the documentation and usage as needed
				//"Argument name" => ["Type" => "Data type", "Required" => false, "Default" => "Default value", "Description" => "Argument and usage description", "Note" => "Argument note", "Method" => "GET | POST", ], 
			],
		],
	],
	"Diagnostics" => [
		"Client" => [
            "IP" => $_SERVER["REMOTE_ADDR"], 
            "UserAgent" => $_SERVER["HTTP_USER_AGENT"], 
        ],
	],
];

// Append GET and POST variables to the Diagnostics node for Debug mode
if(isset($_POST["_Debug"]))$APIResponse["Diagnostics"]["Argument"] = ["GET" => $_GET, "POST" => $_POST, ];

$Application->DocumentType(DOCUMENT_TYPE_JSON); // Set document type (response) to JSON
?>