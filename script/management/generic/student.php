<?php
namespace sPHP;

#region Entity management common configuration
$EM = new EntityManagement($Table[$Entity = "Student"]);

$EM->ImportField([
	new Database\Field("{$Entity}" . ($Field = "Name") . "", "{$Field}"),
	new Database\Field("" . ($Field = "{$Entity}Batch") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("{$Entity}Is" . ($Field = "Active") . "", "{$Field}"),
]);

$EM->InputValidation([
	new HTTP\InputValidation("{$Entity}BatchID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("{$Entity}Roll", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("UserID", true, VALIDATION_TYPE_INTEGER),
]);

$EM->ValidateInput(function($Entity, $Database, $Table, $PrimaryKey, $ID){
	$Result = true;

	if($Table->Get( // Check for duplicate values for UNIQUE columns
		"
			(
					{$Table->Alias()}." . ($Column = "{$Entity}BatchID") . " = " . intval($_POST["{$Column}"]) . "
				AND	{$Table->Alias()}." . ($Column = "{$Entity}Roll") . " = " . intval($_POST["{$Column}"]) . "
			)
			AND	{$PrimaryKey} <> {$ID}
		"
	, null, null, null, null, null, null))$Result = "Same " . strtolower($Table->FormalName()) . " roll in same batch exists!";

	return $Result;
});

$EM->ThumbnailColumn("{$Entity}Picture");

$EM->BeforeInput(function($Entity, $Record){
	//$_POST[$Field = "{$Entity}PasswordHash"] = strlen($_POST["{$Entity}Password"]) ? md5($_POST["{$Entity}Password"]) : (is_null($Record) ? null : $Record["{$Field}"]);

	return true;
});

$EM->AfterInput(function($EntityName, $AffectedRecord, $Record, $Table){

	return true;
});

$EM->IntermediateEntity("xCategory, xEvent");
$EM->DefaultFromSearchColumn("{$Entity}BatchID");

$EM->ListColumn([
	new HTML\UI\Datagrid\Column("" . ($Caption = "{$Entity}") . "PictureThumbnailURL", "{$Caption}", FIELD_TYPE_PICTURE),
	new HTML\UI\Datagrid\Column("User" . ($Caption = "Name") . "", "{$Caption}", null, null),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Batch") . "NameNumberSuffix", "{$Caption}", null, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("{$Entity}Batch" . ($Caption = "Intake") . "Date", "{$Caption}", FIELD_TYPE_SHORTDATE, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("{$Entity}Batch" . ($Caption = "Passout") . "Date", "{$Caption}", FIELD_TYPE_SHORTDATE, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("User" . ($Caption = "Email") . "", "{$Caption}", FIELD_TYPE_EMAIL, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("User" . ($Caption = "Phone") . "", "{$Caption}", FIELD_TYPE_PHONE, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Roll") . "", "{$Caption}", null, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("{$Entity}Is{$Entity}Batch" . ($Caption = "R") . "epresentative", "{$Caption}", FIELD_TYPE_BOOLEANICON),
	new HTML\UI\Datagrid\Column("User" . ($Caption = "S") . "ignUpIsActivated", "{$Caption}A", FIELD_TYPE_BOOLEANICON),
	new HTML\UI\Datagrid\Column("UserIs" . ($Caption = "Active") . "", "U {$Caption}", FIELD_TYPE_BOOLEANICON),
	new HTML\UI\Datagrid\Column("{$Entity}Is" . ($Caption = "Active") . "", "{$Caption}", FIELD_TYPE_BOOLEANICON),
]);

$EM->Action([
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}{$Table[$Entity]->Name()}" . strtolower($ActionEntity = "Identity") . ".png", null, $Application->URL("{$Entity}/Confirm{$ActionEntity}"), null, null, null, "Confirm " . strtolower($ActionEntity = "Identity") . " and send email"),
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

$EM->OrderBy("{$Entity}Roll");
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
	$UserInput = new HTML\UI\Input("User", $EM->InputFullWidth(), null, null);
	#endregion Custom code

	$EM->InputUIHTML([
		HTML\UI\Field(HTML\UI\Select("{$Entity}" . ($Caption = "Batch") . "ID", $Table[$OptionEntity = "{$Entity}{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", null, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Roll") . "", 75, null, true), "{$Caption}", null, true, null),
		HTML\UI\Field(HTML\UI\RadioGroup("{$Entity}Is{$Entity}Batch" . ($Caption = "Representative") . "", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")]), "{$Caption}", null, true, null),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Picture") . "", $EM->InputFullWidth(), "{$Environment->UploadURL()}{$EM->LowercaseEntityName()}/" . (isset($_POST["{$Entity}{$Caption}"]) ? $_POST["{$Entity}{$Caption}"] : null) . "", null, INPUT_TYPE_FILE), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field($UserInput->HTML(), "User", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\RadioGroup("{$Entity}Is" . ($Caption = "Active") . "", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")]), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		"
			<script>
				sJS.DHTML.Autofill.Make('{$UserInput->ID()}', 3, '{$Application->URL("API/V1/AJAXEntity", "Entity=" . ($OptionEntity = "User") . "&Column=U.UserEmail,U.UserNameFirst,U.UserNameMiddle,U.UserNameLast,U.UserPhoneMobile,U.UserPhoneHome,U.UserPhoneWork,U.UserPhoneOther")}', '{$OptionEntity}ID', null, '" . ($CaptionKey = "{$OptionEntity}LookupCaption") . "', null, '" . (($OptionEntityID = isset($_POST["{$OptionEntity}ID"]) ? $_POST["{$OptionEntity}ID"] : null) ? $Table["{$OptionEntity}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}ID = {$_POST["{$OptionEntity}ID"]}")[0]["{$CaptionKey}"] : null) . "', {$OptionEntityID});
			</script>
		",
	]);

	print $EM->InputHTML();
}

#region List
$EM->SearchSQL([
	"1 = 1", // Custom fixed search condition
	//SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Name") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}BatchID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Is{$Entity}BatchRepresentative") . "", SetVariable($Column, "")) !== "" ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "UserSignUpIsActivated") . "", SetVariable($Column, "")) !== "" ? "U.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "UserIsActive") . "", SetVariable($Column, "")) !== "" ? "U.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}IsActive") . "", SetVariable($Column, "")) !== "" ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
]);

$EM->SearchUIHTML([
	//HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Name") . "", 200), "{$Caption}", null, null),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Batch") . "ID", $Table[$OptionEntity = "{$Entity}{$Caption}"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}Is{$Entity}Batch" . ($Caption = "Representative") . "", [new Option(), new Option(0, "No"), new Option(1, "Yes")]), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}User" . ($Caption = "Sign") . "UpIsActivated", [new Option(), new Option(0, "No"), new Option(1, "Yes")]), "{$Caption} up activated", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}" . ($Caption = "User") . "IsActive", [new Option(), new Option(0, "No"), new Option(1, "Yes")]), "{$Caption} active", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}Is" . ($Caption = "Active") . "", [new Option(), new Option(0, "No"), new Option(1, "Yes")]), "{$Caption}", null, true),
]);

print $EM->ListHTML();
#region List
?>