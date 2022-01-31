<?php
namespace sPHP;

foreach(array_filter(explode(",", str_replace(" ", "", "Email, NameFirst, NameMiddle, NameLast"))) as $Argument)$Data[$Argument] = SetVariable($Argument);
print $API->Profile($Data);






//$_POST["Data"] = "{\"NameFirst\":\"John\", \"NameLast\":\"Doe\"}";
//$_POST["Data"] = "{\"NameFirst\":\"Broken\", \"NameLast\":\"Arrow\"}";
//$_POST["Data"] = "{\"NameFirst\":\"Shahriar\", \"NameLast\":\"Kabir\"}";
//$Response = $APIUser->Profile(SetVariable("Data")); DebugDump($Response); DebugDump($APIUser->Profile());

//DebugDump($APIUser->SignOut()); DebugDump($APIUser->Profile());

//DebugDump($APIUser->SetPassword("Qwerty@123"));
?>