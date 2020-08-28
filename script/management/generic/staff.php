<?php
namespace sPHP;

#region General section
$Entity = "Staff";
$LowercaseEntity = strtolower($Entity);
$EntityName = $Table[$Entity]->FormalName();
$LowercaseEntityName = strtolower($Table[$Entity]->FormalName());
$EntityUploadPath = "{$Environment->UploadPath()}{$LowercaseEntity}/";
$EntityID = isset($_POST["{$Entity}ID"]) && (($_POST["{$Entity}ID"] = is_array($_POST["{$Entity}ID"]) ? $_POST["{$Entity}ID"] : intval($_POST["{$Entity}ID"])) || is_array($_POST["{$Entity}ID"])) ? $_POST["{$Entity}ID"] : 0;
#endregion General section

#region List section
$ListSearchSQL[] = "1 = 1"; // Custom fixed search condition
$ListSearchSQL[] = SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Code") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null;
$ListSearchSQL[] = SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Email") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null;
$ListSearchSQL[] = SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Phone") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null;
$ListSearchSQL[] = SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "EnterpriseID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null;
$ListSearchSQL[] = SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "UserID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null;
$ListSearchSQL[] = SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}IsActive") . "", SetVariable($Column, "")) !== "" ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null;

$ListOrderBy = "{$Entity}Code";
$ListOrder = "ASC";
$ListRecordsPerPage = $Configuration["DatagridRowsPerPage"];

$ListColumn = [ // Columns to display
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Picture") . "ThumbnailURL", "{$Caption}", FIELD_TYPE_PICTUREURL),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Code") . "", "{$Caption}"),
	new HTML\UI\Datagrid\Column("{$Entity}User" . ($Caption = "Email") . "", "{$Caption}", FIELD_TYPE_EMAIL),
	new HTML\UI\Datagrid\Column("{$Entity}User" . ($Caption = "Phone") . "", "{$Caption}", FIELD_TYPE_PHONE),
	new HTML\UI\Datagrid\Column("" . ($Caption = "Enterprise") . "LookupCaption", "{$Caption}", null, null),
	new HTML\UI\Datagrid\Column("" . ($Caption = "User") . "LookupCaption", "{$Caption}", null, null),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Join") . "Date", "{$Caption}ed on", FIELD_TYPE_SHORTDATE),
	new HTML\UI\Datagrid\Column("{$Entity}Is" . ($Caption = "Active") . "", "{$Caption}", FIELD_TYPE_BOOLEANICON),
];

$ListAction = [ // Action
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}{$LowercaseEntity}post.png", null, $Application->URL("Management/Generic/{$Entity}Post")),
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}edit.png", null, $Application->URL($_POST["_Script"], "btnInput")),
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}delete.png", null, $Application->URL($_POST["_Script"], "btnDelete"), null, "return confirm('Are you sure to remove the information?');"),
];

$ListBaseURL = $Environment->URL();
$ListIconURL = $Environment->IconURL();

$ListSearchInterface = "
	" . HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Code") . "", 200), "{$Caption}", null, null) . "
	" . HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Email") . "", 200), "{$Caption}", null, true) . "
	" . HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Phone") . "", 200), "{$Caption}", null, true) . "
	" . HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}" . ($Caption = "Enterprise") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", null, true) . "
	" . HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}" . ($Caption = "User") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", null, true) . "
	" . HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}Is" . ($Caption = "Active") . "", [new Option(), new Option(0, "No"), new Option(1, "Yes")]), "{$Caption}", null, true) . "
";

$ListBatchActionInterface = "
	" . HTML\UI\Button("<img src=\"{$Environment->IconURL()}search.png\" alt=\"Search\" class=\"Icon\">Search", BUTTON_TYPE_SUBMIT, "btnSearch", true) . "
	" . HTML\UI\Button("<img src=\"{$Environment->IconURL()}add.png\" alt=\"Add new\" class=\"Icon\">Add new", BUTTON_TYPE_SUBMIT, "btnInput", true) . "
	" . HTML\UI\Button("<img src=\"{$Environment->IconURL()}delete.png\" alt=\"Remove\" class=\"Icon\">Remove", BUTTON_TYPE_SUBMIT, "btnDelete", true, "return confirm('Are you sure to remove the information?');") . "
	" . HTML\UI\Button("<img src=\"{$Environment->IconURL()}export.png\" alt=\"Export\" class=\"Icon\">Export", BUTTON_TYPE_SUBMIT, "btnExport", true) . "
	" . HTML\UI\Button("<img src=\"{$Environment->IconURL()}import.png\" alt=\"Import\" class=\"Icon\">Import", BUTTON_TYPE_SUBMIT, "btnImport", true) . "
";
#endregion List section

#region Input section
$InputIsPermissionErrorMessage = ""; // Set a conditional error message to prevent unauthorized input
$InputIntermediateEntityList = "xCategory, xEvent";

$InputFieldValidation = [
	new HTTP\InputValidation("{$Entity}Code", true),
	new HTTP\InputValidation("{$Entity}Email", null, VALIDATION_TYPE_EMAIL),
	new HTTP\InputValidation("{$Entity}Phone", null, VALIDATION_TYPE_PHONE),
	new HTTP\InputValidation("EnterpriseID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("UserID", true, VALIDATION_TYPE_INTEGER),
];

$InputDataVerificationSQL = "
	(
			" . ($Column = "{$Entity}Code") . " = '{$Database->Escape($_POST["{$Column}"])}'
		AND	{$Table["{$Entity}"]->Alias()}." . ($Column = "EnterpriseID") . " = " . intval($_POST["{$Column}"]) . "
	)
	AND	{$Entity}ID <> {$EntityID}
";

$InputThumbnailColumnList = "{$Entity}Picture, x{$Entity}Avatar";
$InputThumbnailMaximumDimension = 48;
$InputDefaultValueFromSearchFieldList = "UserID, xSomethingElseID";

require __DIR__ . "/../../common/entitymanagement_loadexistingdata.php"; // Load existing data

$InputInterface = "
	" . HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Code") . "", $Configuration["InputWidth"], null, true), "{$Caption}", null, null, $Configuration["FieldCaptionWidth"]) . "
	" . HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Join") . "Date", $Configuration["InputDateWidth"], null, null, INPUT_TYPE_DATE), "{$Caption}ing date", null, true, $Configuration["FieldCaptionInlineWidth"]) . "
	" . HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Enterprise") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "
	" . HTML\UI\Field(HTML\UI\Select("" . ($Caption = "User") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "
	" . HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Email") . "", $Configuration["InputWidth"], null, true, INPUT_TYPE_EMAIL), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "
	" . HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Phone") . "", $Configuration["InputInlineWidth"], null, true, INPUT_TYPE_PHONE), "{$Caption}", null, true, $Configuration["FieldCaptionInlineWidth"]) . "
	" . HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Picture") . "", $Configuration["InputWidth"], "{$Environment->UploadURL()}{$LowercaseEntity}/" . (isset($_POST["{$Entity}{$Caption}"]) ? $_POST["{$Entity}{$Caption}"] : null) . "", null, INPUT_TYPE_FILE), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "
	" . HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Signature") . "", $Configuration["InputInlineWidth"], "{$Environment->UploadURL()}{$LowercaseEntity}/" . (isset($_POST["{$Entity}{$Caption}"]) ? $_POST["{$Entity}{$Caption}"] : null) . "", null, INPUT_TYPE_FILE), "{$Caption}", null, true, $Configuration["FieldCaptionInlineWidth"]) . "
	" . HTML\UI\Field(HTML\UI\RadioGroup("{$Entity}Is" . ($Caption = "Active") . "", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")]), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "
	" . HTML\UI\Input("btn" . ($Caption = "Input") . "", $Configuration["InputWidth"], null, true, INPUT_TYPE_HIDDEN) . "
	" . HTML\UI\Input("{$Entity}" . ($Caption = "ID") . "", $Configuration["InputWidth"], $EntityID, true, INPUT_TYPE_HIDDEN) . "
";
#endregion Input section

#region Import section
$ImportField = [
	new Database\Field("{$Entity}" . ($Field = "Code") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Join") . "Date", "{$Field}"),
	new Database\Field("" . ($Field = "Enterprise") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("" . ($Field = "User") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("{$Entity}" . ($Field = "Email") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Phone") . "", "{$Field}"),
	new Database\Field("{$Entity}Is" . ($Field = "Active") . "", "{$Field}"),
];
#endregion Import section

#region Delete section
#endregion Delete section

#region Export section
#endregion Export section

require __DIR__ . "/../../common/entitymanagement.php"; // Generic management process
?>