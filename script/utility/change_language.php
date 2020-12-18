<?php
namespace sPHP;

require __DIR__ . "/../common/languagebyhtmlcode.php";
foreach($Language as $ThisLanguageIndex => $ThisLanguage)$LanguageOption[] = new Option($ThisLanguageIndex, "{$ThisLanguage->Name()}" . ($ThisLanguage->NativelyName() != $ThisLanguage->Name() ? "  |  {$ThisLanguage->NativelyName()}" : null) . "");

if(isset($_POST["btnSubmit"])){
	$Application->Session()->Language($Language[$_POST["LanguageHTMLCode"]]);
	$Terminal->Redirect($_POST["_Referer"], "Language set to '{$Application->Language()->Name()}'.");
}

print HTML\UI\Form(
	$Application->URL($_POST["_Script"]), 
	implode(null, [
		HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Language") . "HTMLCode", $LanguageOption, null, null, null, null, $Application->Language()->HTMLCode()), "{$Caption}", null, null), 
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