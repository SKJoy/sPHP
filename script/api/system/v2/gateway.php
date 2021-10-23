<?php
namespace sPHP;

$APISubject = SetVariable("_Subject", "Demo");

if($APISubject == "Demo"){
	$API = new API\V2\Demo();
}
elseif($APISubject == "User"){
	$API = new API\V2\User();
}
else{

}

$API->OutputFormat = API\V2::OUTPUT_FORMAT_JSON;
$TRM->DocumentType(DOCUMENT_TYPE_JSON);

require __DIR__ . "/" . strtolower($APISubject) . "/" . strtolower(SetVariable("_Action", "Action")) . ".php";
?>