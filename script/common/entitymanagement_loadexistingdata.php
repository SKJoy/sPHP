<?php
namespace sPHP;

#region Load existing data
if($EntityID){ // Record exists
	$InputRecord = $Table["{$Entity}"]->Get("{$Entity}ID = {$EntityID}")[0]; // Get existing record
	if(!isset($_POST["btnSubmit"]))foreach($InputRecord as $Key=>$Value)$_POST[$Key] = $Value; // Load existing data
}

// Load option data from intermediate table
foreach(($InputIntermediateEntityList = ListToArray($InputIntermediateEntityList)) as $OptionEntity)if(isset($Table[$IntermediateEntity = "{$Entity}{$OptionEntity}"])){
	$OptionRecordset = $Table[$IntermediateEntity]->Get("{$Table[$IntermediateEntity]->Alias()}.{$Entity}ID = {$EntityID} AND {$Entity}{$OptionEntity}IsActive = 1", null, null, null, null, null, false);
	if(is_array($OptionRecordset))foreach($OptionRecordset as $Option)$_POST["{$OptionEntity}ID_{$Option["{$OptionEntity}ID"]}"] = $Option["{$OptionEntity}ID"];
}
#endregion Load existing data
?>