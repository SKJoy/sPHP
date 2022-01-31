<?php
namespace sPHP;

require __DIR__ . "/../common/languagebyhtmlcode.php";
foreach($Language as $ThisLanguageIndex => $ThisLanguage)$LanguageOption[] = new Option($ThisLanguageIndex, "{$ThisLanguage->Name()}" . ($ThisLanguage->NativelyName() != $ThisLanguage->Name() ? "  |  {$ThisLanguage->NativelyName()}" : null) . "");

if(isset($_POST["btnSubmit"])){
	$APP->Session()->Language($Language[$_POST["LanguageHTMLCode"]]);
	$TRM->Redirect($_POST["_Referer"], "Language set to '{$APP->Language()->Name()}'.");
}

print HTML\UI\Form(
	$APP->URL($_POST["_Script"]), 
	implode("", [
		HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Language") . "HTMLCode", $LanguageOption, null, null, null, null, $APP->Language()->HTMLCode()), "{$Caption}", null, null), 
	]), 
	"Set", // Submit button caption
	null, 
	"Set language", // Title
	null, // Header
	"Select your desired language above", // Footer
	null, // Status
	null, 
	null, // Hide reset button
	null, // Button row content HTML
	null
);
?>