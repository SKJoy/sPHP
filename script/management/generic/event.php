<?php
namespace sPHP;

#region Entity management common configuration
$EM = new EntityManagement($Table[$Entity = "Event"]);

$EM->ImportField([
	new Database\Field("{$Entity}" . ($Field = "Name") . "", "{$Field}"),
	//new Database\Field("" . ($Field = "Platform") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("{$Entity}Is" . ($Field = "Active") . "", "{$Field}"),
]);

$EM->InputValidation([
	new HTTP\InputValidation("{$Entity}Name", true),
	new HTTP\InputValidation("{$Entity}DateBegin", true),
	new HTTP\InputValidation("{$Entity}DateEnd", true),
	//new HTTP\InputValidation("PlatformID", true, VALIDATION_TYPE_INTEGER),
]);

$EM->ValidateInput(function($Entity, $Database, $Table, $PrimaryKey, $ID){
	$Result = true;

	if($Table->Get( // Check for duplicate values for UNIQUE columns
		"
			(
					" . ($Column = "{$Entity}Name") . " = '{$Database->Escape($_POST["{$Column}"])}'
				AND	" . ($Column = "{$Entity}DateBegin") . " = '{$Database->Escape($_POST["{$Column}"])}'
				/*AND	{$Table->Alias()}." . ($Column = "PlatformID") . " = " . intval($_POST["{$Column}"]) . "*/
			)
			AND	{$PrimaryKey} <> {$ID}
		"
	, null, null, null, null, null, null))$Result = "Same " . strtolower($Table->FormalName()) . " on same date exists!";

	return $Result;
});

$EM->ThumbnailColumn("{$Entity}Picture, x{$Entity}Banner");

$EM->BeforeInput(function($Entity, $Record){
	//$_POST[$Field = "{$Entity}PasswordHash"] = strlen($_POST["{$Entity}Password"]) ? md5($_POST["{$Entity}Password"]) : (is_null($Record) ? null : $Record["{$Field}"]);

	return true;
});

$EM->AfterInput(function($EntityName, $AffectedRecord, $Record, $Table){

	return true;
});

$EM->IntermediateEntity("xCategory, xEvent");
$EM->DefaultFromSearchColumn("xPlatformID");

$EM->ListColumn([
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "PictureThumbnail") . "URL", "{$Caption}", FIELD_TYPE_PICTURE),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Name") . "", "{$Caption}"),
	new HTML\UI\Datagrid\Column("{$Entity}Date" . ($Caption = "Begin") . "", "{$Caption}", FIELD_TYPE_SHORTDATE, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("{$Entity}Date" . ($Caption = "End") . "", "{$Caption}", FIELD_TYPE_SHORTDATE, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("{$Entity}SubscriptionDate" . ($Caption = "Begin") . "", "S {$Caption}", FIELD_TYPE_SHORTDATE, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("{$Entity}SubscriptionDate" . ($Caption = "End") . "", "S {$Caption}", FIELD_TYPE_SHORTDATE, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Subs") . "criptionCount", "{$Caption}", FIELD_TYPE_NUMBER, null),
	new HTML\UI\Datagrid\Column("{$Entity}Is" . ($Caption = "Active") . "", "{$Caption}", FIELD_TYPE_BOOLEANICON),
]);

$EM->Action([
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}" . strtolower($ActionEntity = "{$Entity}Subscription") . ".png", null, $Application->URL("Management/Generic/{$ActionEntity}"), null, null, null, "{$ActionEntity}"),
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}edit.png", null, $Application->URL($_POST["_Script"], "btnInput"), null, null, null, "Edit"),
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}delete.png", null, $Application->URL($_POST["_Script"], "btnDelete"), null, "return confirm('Are you sure to remove the information?');", null, "Delete"),
]);

$EM->BatchActionHTML([
	HTML\UI\Button("<img src=\"{$Environment->IconURL()}search.png\" alt=\"Search\" class=\"Icon\">Search", BUTTON_TYPE_SUBMIT, "btnSearch", true),
	HTML\UI\Button("<img src=\"{$Environment->IconURL()}add.png\" alt=\"Add new\" class=\"Icon\">Add new", BUTTON_TYPE_SUBMIT, "btnInput", true),
	HTML\UI\Button("<img src=\"{$Environment->IconURL()}delete.png\" alt=\"Remove\" class=\"Icon\">Remove", BUTTON_TYPE_SUBMIT, "btnDelete", true, "return confirm('Are you sure to remove the information?');"),
	HTML\UI\Button("<img src=\"{$Environment->IconURL()}export.png\" alt=\"Export\" class=\"Icon\">Export", BUTTON_TYPE_SUBMIT, "btnExport", true),
	HTML\UI\Button("<img src=\"{$Environment->IconURL()}import.png\" alt=\"Import\" class=\"Icon\">Import", BUTTON_TYPE_SUBMIT, "btnImport", true),
]);

$EM->OrderBy("{$Entity}Name");
$EM->Order("ASC");
$EM->URL($Application->URL($_POST["_Script"]));
$EM->IconURL($Environment->IconURL());
$EM->EncryptionKey($Application->EncryptionKey());
$EM->FieldCaptionWidth($Configuration["FieldCaptionWidth"]);
$EM->FieldCaptionInlineWidth($Configuration["FieldCaptionInlineWidth"]);
$EM->FieldContentFullWidth($Configuration["FieldContentFullWidth"]);
$EM->InputWidth($Configuration["InputWidth"]);
$EM->InputInlineWidth($Configuration["InputInlineWidth"]);
$EM->InputFullWidth($Configuration["InputFullWidth"]);
$EM->InputDateWidth($Configuration["InputDateWidth"]);
$EM->TempPath($Environment->TempPath());
$EM->SearchInputPrefix($Configuration["SearchInputPrefix"]);
$EM->UploadPath($Environment->UploadPath());
$EM->ThumbnailMaximumDimension(48);
$EM->RecordsPerPage($Configuration["DatagridRowsPerPage"]);
$EM->BaseURL($Environment->URL()); // ???????????
#endregion Entity management common configuration

if(isset($_POST["btnExport"])){
	if(isset($_POST["btnSubmit"])){
		$EM->Export();
		$Terminal->Redirect($_POST["_Referer"]);
	}

	print $EM->ExportHTML();
}

if(isset($_POST["btnImport"])){
	if(isset($_POST["btnSubmit"])){
		$EM->Import();
		$Terminal->Redirect($_POST["_Referer"]);
	}

	print $EM->ImportHTML();
}

if(isset($_POST["btnDelete"])){
	$EM->Delete();
	$Terminal->Redirect($_SERVER["HTTP_REFERER"]);
}

if(isset($_POST["btnInput"])){
	if(isset($_POST["btnSubmit"])){
		if($EM->Input())$Terminal->Redirect($_POST["_Referer"]); // Redirect to previous location
	}

	$EM->LoadExistingData();

	#region Custom code
	$DescriptionInput = new HTML\UI\Textarea("{$Entity}" . ($Caption = "Description") . "", $EM->InputFullWidth(), $Configuration["TextareaHeight"], null, null);
	$InstructionInput = new HTML\UI\Textarea("{$Entity}" . ($Caption = "Instruction") . "", $EM->InputFullWidth(), $Configuration["TextareaHeight"], null, null);
	#endregion Custom code

	$EM->InputUIHTML([
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Name") . "", $EM->InputFullWidth(), null, true), "{$Caption}", null, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Date" . ($Caption = "Begin") . "", $EM->InputDateWidth(), null, true, INPUT_TYPE_DATE), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Date" . ($Caption = "End") . "", $EM->InputDateWidth(), null, true, INPUT_TYPE_DATE), "{$Caption}", null, true, null),
		//HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Platform") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Picture") . "", $EM->InputFullWidth(), "{$Environment->UploadURL()}{$EM->LowercaseEntityName()}/" . (isset($_POST["{$Entity}{$Caption}"]) ? $_POST["{$Entity}{$Caption}"] : null) . "", null, INPUT_TYPE_FILE), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Banner") . "", $EM->InputFullWidth(), "{$Environment->UploadURL()}{$EM->LowercaseEntityName()}/" . (isset($_POST["{$Entity}{$Caption}"]) ? $_POST["{$Entity}{$Caption}"] : null) . "", null, INPUT_TYPE_FILE), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field($DescriptionInput->HTML(), "Description", true, null, $EM->FieldCaptionWidth(), null, null, null, "Optional"),
		HTML\UI\Field($InstructionInput->HTML(), "Instruction", true, null, $EM->FieldCaptionWidth(), null, null, null, "Optional"),
		HTML\UI\Field(HTML\UI\Input("{$Entity}SubscriptionDate" . ($Caption = "Begin") . "", $EM->InputDateWidth(), null, true, INPUT_TYPE_DATE), "Subscription: {$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}SubscriptionDate" . ($Caption = "End") . "", $EM->InputDateWidth(), null, true, INPUT_TYPE_DATE), "{$Caption}", null, true, null),
		HTML\UI\Field(HTML\UI\RadioGroup("{$Entity}Is" . ($Caption = "Active") . "", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")]), "{$Caption}", true, null, $EM->FieldCaptionWidth()),

		"
			<script>
				sJS.MakeTinyMCETextarea('{$DescriptionInput->ID()}');
				sJS.MakeTinyMCETextarea('{$InstructionInput->ID()}');
			</script>
		",
	]);

	print $EM->InputHTML();
}

#region List
$EM->SearchSQL([
	"1 = 1", // Custom fixed search condition
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Name") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Description") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Instruction") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null,
	//SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "PlatformID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}IsActive") . "", SetVariable($Column, "")) !== "" ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
]);

$EM->SearchUIHTML([
	HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Name") . "", 200), "{$Caption}", null, null),
	HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Description") . "", 200), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Instruction") . "", 200), "{$Caption}", null, true),
	//HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}" . ($Caption = "Platform") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}Is" . ($Caption = "Active") . "", [new Option(), new Option(0, "No"), new Option(1, "Yes")]), "{$Caption}", null, true),
]);

print $EM->ListHTML();
#region List
?>