<?php
namespace sPHP;

$Data = [ // Construct Data argument from POST arguments
	($Argument = "Email") => SetVariable($Argument), 
	($Argument = "NameFirst") => SetVariable($Argument), 
	($Argument = "NameMiddle") => SetVariable($Argument), 
	($Argument = "NameLast") => SetVariable($Argument), 
];

print $API->Profile($Data);






//$_POST["Data"] = "{\"NameFirst\":\"John\", \"NameLast\":\"Doe\"}";
//$_POST["Data"] = "{\"NameFirst\":\"Broken\", \"NameLast\":\"Arrow\"}";
//$_POST["Data"] = "{\"NameFirst\":\"Shahriar\", \"NameLast\":\"Kabir\"}";
//$Response = $APIUser->Profile(SetVariable("Data")); DebugDump($Response); DebugDump($APIUser->Profile());

//DebugDump($APIUser->SignOut()); DebugDump($APIUser->Profile());

//DebugDump($APIUser->SetPassword("Qwerty@123"));
?>