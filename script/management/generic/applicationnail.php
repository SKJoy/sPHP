<?php
namespace sPHP;

#region Entity management common configuration
$EM = new EntityManagement($Table[$Entity = "ApplicationNail"]);

$EM->ImportField([
	new Database\Field("" . ($Field = "User") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}ID"),
	new Database\Field("{$Entity}" . ($Field = "Time") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Subject") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Introduction") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "IntroductionPicture") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Content") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Conclusion") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "ConclusionPicture") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Reason") . "", "{$Field}"),
	new Database\Field("{$Entity}Is" . ($Field = "Active") . "", "{$Field}"),
]);

$EM->InputValidation([
	new HTTP\InputValidation("UserID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("{$Entity}Time", true),
	new HTTP\InputValidation("{$Entity}Subject", true),
	new HTTP\InputValidation("{$Entity}Introduction", null),
	new HTTP\InputValidation("{$Entity}IntroductionPicture", null),
	new HTTP\InputValidation("{$Entity}Content", null),
	new HTTP\InputValidation("{$Entity}ConclusionPicture", null),
	new HTTP\InputValidation("{$Entity}Conclusion", null),
	new HTTP\InputValidation("{$Entity}Reason", null),
	new HTTP\InputValidation("{$Entity}IsActive", null, VALIDATION_TYPE_INTEGER),
]);

$EM->ValidateInput(function($Entity, $Database, $Table, $PrimaryKey, $ID){
	$Result = true;

	if($Table->Get( // Check for duplicate values for UNIQUE columns
		"
			(
					AN." . ($Column = "UserID") . " = " . intval($_POST["{$Column}"]) . "
				AND	" . ($Column = "{$Entity}Time") . " = '{$Database->Escape($_POST["{$Column}"])}'
				AND	" . ($Column = "{$Entity}Subject") . " = '{$Database->Escape($_POST["{$Column}"])}'
			)
			AND	{$PrimaryKey} <> {$ID}
		"
	, null, null, null, null, null, null))$Result = "Same user with same time and subject for the same " . strtolower($Table->FormalName()) . " exists!";

	return $Result;
});

$EM->ThumbnailColumn("x{$Entity}Picture");

$EM->BeforeInput(function($Entity, $Record){
	//$_POST[$Field = "{$Entity}PasswordHash"] = strlen($_POST["{$Entity}Password"]) ? md5($_POST["{$Entity}Password"]) : (is_null($Record) ? null : $Record["{$Field}"]);

	return true;
});

$EM->IntermediateEntity("xCategory, xEvent");
$EM->DefaultFromSearchColumn("xTerminalID, x{$Entity}FromAddressID");

$EM->ListColumn([
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Subject") . "", "{$Caption}"),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Content") . "LookupCaption", "{$Caption}"),
	new HTML\UI\Datagrid\Column("" . ($Caption = "User") . "LookupCaption", "{$Caption}", null),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Time") . "", "{$Caption}", FIELD_TYPE_DATETIME),
	new HTML\UI\Datagrid\Column("{$Entity}Is" . ($Caption = "Active") . "", "{$Caption}", FIELD_TYPE_BOOLEANICON),
]);

$EM->Action([
	//new HTML\UI\Datagrid\Action("{$Environment->IconURL()}{$Entity}" . strtolower($ActionEntity = "CommercialInvoice") . ".png", null, $Application->URL("{$Entity}/{$ActionEntity}"), "_blank", null, null, "Commercial invoice"),
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

$EM->OrderBy("{$Entity}Time");
$EM->Order("DESC");
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
$EM->InputTimeWidth($Configuration["InputTimeWidth"]);
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
	$EM->Delete("UserID = {$User->ID()} OR {$User->UserGroupIdentifierHighest()} = 'ADMINISTRATOR'");
	$Terminal->Redirect($_SERVER["HTTP_REFERER"]);
}

if(isset($_POST["btnInput"])){
	if(isset($_POST["btnSubmit"])){
		#region Custom code
		if($User->UserGroupIdentifierHighest() != "ADMINISTRATOR")$_POST["UserID"] = $User->ID();
		$_POST["{$Entity}Time"] = "{$_POST["{$Entity}TimeDate"]} {$_POST["{$Entity}TimeTime"]}:00";
		#endregion Custom code

		if($EM->Input())$Terminal->Redirect("{$_POST["_Referer"]}&SucceededAction=Input"); // Redirect to previous location
	}

	$EM->LoadExistingData();

	#region Custom code
	if($EM->EntityID()){ // Existing record
		$ApplicationNailBoardTime = strtotime($_POST["{$Entity}Time"]);
		$_POST["{$Entity}TimeDate"] = date("Y-m-d", $ApplicationNailBoardTime);
		$_POST["{$Entity}TimeTime"] = date("H:i", $ApplicationNailBoardTime);
	}
	else{ // New record
		$_POST["UserID"] = $User->ID();
		$_POST["UserLookupCaption"] = "{$User->Name()} [{$User->Email()}]";
	}
	#endregion Custom code

	$EM->InputUIHTML([
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Subject") . "", $EM->InputFullWidth(), null, true), "{$Caption}", null, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Textarea("{$Entity}" . ($Caption = "Introduction") . "", $EM->InputFullWidth(), $Configuration["TextareaHeight"], null, null, null, null, null, "{$Entity}Introduction"), "{$Caption}", true, null, $EM->FieldCaptionWidth(), null, null, null, "Optional"),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Introduction") . "Picture", $EM->InputFullWidth(), "{$Environment->UploadURL()}{$EM->LowercaseEntityName()}/" . (isset($_POST["{$Entity}{$Caption}Picture"]) ? $_POST["{$Entity}{$Caption}Picture"] : null) . "", null, INPUT_TYPE_FILE), "{$Caption} picture", true, null, $EM->FieldCaptionWidth(), null, null, null, "Optional"),
		HTML\UI\Field(HTML\UI\Textarea("{$Entity}" . ($Caption = "Content") . "", $EM->InputFullWidth(), $Configuration["TextareaHeight"], null, null, null, null, null, "{$Entity}Content"), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Conclusion") . "Picture", $EM->InputFullWidth(), "{$Environment->UploadURL()}{$EM->LowercaseEntityName()}/" . (isset($_POST["{$Entity}{$Caption}Picture"]) ? $_POST["{$Entity}{$Caption}Picture"] : null) . "", null, INPUT_TYPE_FILE), "{$Caption} picture", true, null, $EM->FieldCaptionWidth(), null, null, null, "Optional"),
		HTML\UI\Field(HTML\UI\Textarea("{$Entity}" . ($Caption = "Conclusion") . "", $EM->InputFullWidth(), $Configuration["TextareaHeight"], null, null, null, null, null, "{$Entity}Conclusion"), "{$Caption}", true, null, $EM->FieldCaptionWidth(), null, null, null, "Optional"),
		HTML\UI\Field(HTML\UI\Textarea("{$Entity}" . ($Caption = "Reason") . "", $EM->InputFullWidth(), $Configuration["TextareaHeight"], null, null, null, null, null, "{$Entity}Reason"), "{$Caption}", true, null, $EM->FieldCaptionWidth(), null, null, null, "Optional"),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Reason") . "Picture", $EM->InputFullWidth(), "{$Environment->UploadURL()}{$EM->LowercaseEntityName()}/" . (isset($_POST["{$Entity}{$Caption}Picture"]) ? $_POST["{$Entity}{$Caption}Picture"] : null) . "", null, INPUT_TYPE_FILE), "{$Caption} picture", true, null, $EM->FieldCaptionWidth(), null, null, null, "Optional"),

		HTML\UI\Field(
			HTML\UI\Input("{$Entity}" . ($Caption = "Time") . "Date", null, date("Y-m-d"), true, INPUT_TYPE_DATE, null) .
			HTML\UI\Input("{$Entity}" . ($Caption = "Time") . "Time", $EM->InputTimeWidth(), date("H:i"), true, INPUT_TYPE_TIME, null),
		"{$Caption}", true, null, $EM->FieldCaptionWidth()),

		$User->UserGroupIdentifierHighest() == "ADMINISTRATOR" ? HTML\UI\Field(UserSelectHTML($Application, $EM->InputFullWidth(), null, null, null), "Author", true, null, $EM->FieldCaptionWidth(), null, null, null, null) : null,
		HTML\UI\Field(HTML\UI\RadioGroup("{$Entity}Is" . ($Caption = "Active") . "", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")]), "{$Caption}", true, null, $EM->FieldCaptionWidth()),

		"
			<script>
				sJS.MakeTinyMCETextarea('{$Entity}Introduction');
				sJS.MakeTinyMCETextarea('{$Entity}Content');
				sJS.MakeTinyMCETextarea('{$Entity}Conclusion');
				sJS.MakeTinyMCETextarea('{$Entity}Reason');
			</script>
		",
	]);

	print $EM->InputHTML();
}

#region List
$EM->SearchSQL([
	"1 = 1", // Custom fixed search condition
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}") . "", SetVariable($Column)) ? "({$Table["{$Entity}"]->Alias()}.{$Column}Subject LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%' OR {$Table["{$Entity}"]->Alias()}.{$Column}Introduction LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%' OR {$Table["{$Entity}"]->Alias()}.{$Column}Content LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%' OR {$Table["{$Entity}"]->Alias()}.{$Column}Conclusion LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%')" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "UserID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}IsActive") . "", SetVariable($Column, "")) !== "" ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
]);

#region Custom code
//$_POST[$Field = "{$Configuration["SearchInputPrefix"]}{$Entity}Content"] = strip_tags($_POST["{$Field}"]);
#endregion Custom code

$EM->SearchUIHTML([
	HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "") . "", 200), "{$Caption}", null, null),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}Is" . ($Caption = "Active") . "", [new Option(), new Option(0, "No"), new Option(1, "Yes")]), "{$Caption}", null, true),
]);

print "{$EM->ListHTML()}";
if(SetVariable("SucceededAction") == "Input")print HTML\UI\Toast("{$Table["{$Entity}"]->FormalName()} input successful.");
#region List
?>

<style>
	.Datagrid > .Content > .Grid > tbody > tr > td:nth-child(5){white-space: nowrap;}
</style>