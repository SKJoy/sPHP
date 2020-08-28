<?php
namespace sPHP;

#region General section
$Entity = "Project";
$LowercaseEntity = strtolower($Entity);
$EntityName = $Table[$Entity]->FormalName();
$LowercaseEntityName = strtolower($Table[$Entity]->FormalName());
$EntityUploadPath = "{$Environment->UploadPath()}{$LowercaseEntity}/";
$EntityID = isset($_POST["{$Entity}ID"]) && (($_POST["{$Entity}ID"] = is_array($_POST["{$Entity}ID"]) ? $_POST["{$Entity}ID"] : intval($_POST["{$Entity}ID"])) || is_array($_POST["{$Entity}ID"])) ? $_POST["{$Entity}ID"] : 0;
#endregion General section

#region List section
$ListSearchSQL[] = "1 = 1"; // Custom fixed search condition
$ListSearchSQL[] = SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Name") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null;
$ListSearchSQL[] = SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Description") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null;
$ListSearchSQL[] = SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "PlatformID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null;
$ListSearchSQL[] = SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}IsActive") . "", SetVariable($Column, "")) !== "" ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null;

$ListOrderBy = "{$Entity}Name";
$ListOrder = "ASC";
$ListRecordsPerPage = $Configuration["DatagridRowsPerPage"];

$ListColumn = [ // Columns to display
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Icon") . "URL", "{$Caption}", FIELD_TYPE_ICONURL),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Name") . "", "{$Caption}"),
	new HTML\UI\Datagrid\Column("" . ($Caption = "Platform") . "Name", "{$Caption}", null, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("" . ($Caption = "Category") . "Name", "{$Caption}"),
	new HTML\UI\Datagrid\Column("" . ($Caption = "Event") . "Name", "{$Caption}"),
	new HTML\UI\Datagrid\Column("{$Entity}Is" . ($Caption = "Active") . "", "{$Caption}", FIELD_TYPE_BOOLEANICON),
];

$ListAction = [ // Action
	//new HTML\UI\Datagrid\Action("{$Environment->IconURL()}{$LowercaseEntity}property.png", null, $Application->URL("Management/Generic/{$Entity}Property")),
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}edit.png", null, $Application->URL($_POST["_Script"], "btnInput")),
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}delete.png", null, $Application->URL($_POST["_Script"], "btnDelete"), null, "return confirm('Are you sure to remove the information?');"),
];

$ListBaseURL = $Environment->URL();
$ListIconURL = $Environment->IconURL();

$ListSearchInterface = "
	" . HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Name") . "", 200), "{$Caption}", null, null) . "
	" . HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Description") . "", 200), "{$Caption}", null, true) . "
	" . HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}" . ($Caption = "Platform") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", null, true) . "
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
$InputIntermediateEntityList = "Category, Event";

$InputFieldValidation = [
	new HTTP\InputValidation("{$Entity}Name", true),
	new HTTP\InputValidation("PlatformID", true, VALIDATION_TYPE_INTEGER),
];

$InputDataVerificationSQL = "
	(
			" . ($Column = "{$Entity}Name") . " = '{$Database->Escape($_POST["{$Column}"])}'
		AND	{$Table["{$Entity}"]->Alias()}." . ($Column = "PlatformID") . " = " . intval($_POST["{$Column}"]) . "
	)
	AND	{$Entity}ID <> {$EntityID}
";

$InputThumbnailColumnList = "x{$Entity}Picture, x{$Entity}Avatar";
$InputThumbnailMaximumDimension = 48;
$InputDefaultValueFromSearchFieldList = "PlatformID, xSomethingElseID";

require __DIR__ . "/../../common/entitymanagement_loadexistingdata.php"; // Load existing data

$InputInterface = "
	" . HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Name") . "", $Configuration["InputFullWidth"], null, true), "{$Caption}", null, null, $Configuration["FieldCaptionWidth"]) . "
	" . HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Platform") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "
	" . HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Icon") . "", $Configuration["InputFullWidth"], "{$Environment->UploadURL()}{$LowercaseEntity}/" . (isset($_POST["{$Entity}{$Caption}"]) ? $_POST["{$Entity}{$Caption}"] : null) . "", null, INPUT_TYPE_FILE), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "
	" . HTML\UI\Field(HTML\UI\Textarea("{$Entity}" . ($Caption = "Summary") . "", $Configuration["InputFullWidth"], $Configuration["TextareaHeight"]), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"], null, null, null, "Optional") . "
	" . HTML\UI\Field(HTML\UI\Textarea("{$Entity}" . ($Caption = "Description") . "", $Configuration["InputFullWidth"], $Configuration["TextareaHeight"]), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"], null, null, null, "Optional") . "
	" . HTML\UI\Field(HTML\UI\CheckboxGroup("" . ($Caption = "Category") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption", null, "{$OptionEntity}ID"), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"], $Configuration["FieldContentFullWidth"]) . "
	" . HTML\UI\Field(HTML\UI\CheckboxGroup("" . ($Caption = "Event") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}ID IN (SELECT PE.{$OptionEntity}ID FROM sphp_{$LowercaseEntity}event AS PE WHERE PE.{$Entity}ID = {$EntityID}) OR ({$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}DateEnd >= CURDATE() AND {$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1)"), null, "{$OptionEntity}LookupCaption", null, "{$OptionEntity}ID"), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"], $Configuration["FieldContentFullWidth"]) . "
	" . HTML\UI\Field(HTML\UI\RadioGroup("{$Entity}Is" . ($Caption = "Active") . "", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")]), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "
	" . HTML\UI\Input("btn" . ($Caption = "Input") . "", $Configuration["InputWidth"], null, true, INPUT_TYPE_HIDDEN) . "
	" . HTML\UI\Input("{$Entity}" . ($Caption = "ID") . "", $Configuration["InputWidth"], $EntityID, true, INPUT_TYPE_HIDDEN) . "
";
#endregion Input section

#region Import section
$ImportField = [
	new Database\Field("{$Entity}" . ($Field = "Name") . "", "{$Field}"),
	new Database\Field("" . ($Field = "Platform") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("{$Entity}Is" . ($Field = "Active") . "", "{$Field}"),
];
#endregion Import section

#region Delete section
#endregion Delete section

#region Export section
#endregion Export section

require __DIR__ . "/../../common/entitymanagement.php"; // Generic management process
?>