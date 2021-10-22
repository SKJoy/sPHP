<?php
namespace sPHP;

$APIUser = new API\V2\User();
$APIUser->OutputFormat = API\V2::OUTPUT_FORMAT_JSON;

$Data = array_filter([
	($Argument = "Email") => SetVariable($Argument), 
	($Argument = "NameFirst") => SetVariable($Argument), 
	($Argument = "NameMiddle") => SetVariable($Argument), 
	($Argument = "NameLast") => SetVariable($Argument), 
]); 

if(!count($Data))$Data = null; //DebugDump($Data);

//DebugDump($APIUser->Profile($Data));
$TRM->DocumentType(DOCUMENT_TYPE_JSON);
print $APIUser->Profile($Data);






//$_POST["Data"] = "{\"NameFirst\":\"John\", \"NameLast\":\"Doe\"}";
//$_POST["Data"] = "{\"NameFirst\":\"Broken\", \"NameLast\":\"Arrow\"}";
//$_POST["Data"] = "{\"NameFirst\":\"Shahriar\", \"NameLast\":\"Kabir\"}";
//$Response = $APIUser->Profile(SetVariable("Data")); DebugDump($Response); DebugDump($APIUser->Profile());

//DebugDump($APIUser->SignOut()); DebugDump($APIUser->Profile());

//DebugDump($APIUser->SetPassword("Qwerty@123"));
?>