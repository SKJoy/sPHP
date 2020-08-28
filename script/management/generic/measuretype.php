<?php
namespace sPHP;

#region General section
$Entity = "MeasureType";
$LowercaseEntity = strtolower($Entity);
$EntityName = $Table[$Entity]->FormalName();
$LowercaseEntityName = strtolower($Table[$Entity]->FormalName());
$EntityUploadPath = "{$Environment->UploadPath()}{$LowercaseEntity}/";
$EntityID = isset($_POST["{$Entity}ID"]) && (($_POST["{$Entity}ID"] = is_array($_POST["{$Entity}ID"]) ? $_POST["{$Entity}ID"] : intval($_POST["{$Entity}ID"])) || is_array($_POST["{$Entity}ID"])) ? $_POST["{$Entity}ID"] : 0;
#endregion General section

#region List section
$ListSearchSQL[] = "1 = 1"; // Custom fixed search condition
$ListSearchSQL[] = SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Name") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null;
$ListSearchSQL[] = SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Identifier") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null;
$ListSearchSQL[] = SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}IsActive") . "", SetVariable($Column, "")) !== "" ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null;

$ListOrderBy = "{$Entity}Name";
$ListOrder = "ASC";
$ListRecordsPerPage = $Configuration["DatagridRowsPerPage"];

$ListColumn = [ // Columns to display
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Name") . "", "{$Caption}"),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Identifier") . "", "{$Caption}"),
	new HTML\UI\Datagrid\Column("{$Entity}Is" . ($Caption = "Active") . "", "{$Caption}", FIELD_TYPE_BOOLEANICON),
];

$ListAction = [ // Action
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}measure.png", null, $Application->URL("Management/Generic/measure")),
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}edit.png", null, $Application->URL($_POST["_Script"], "btnInput")),
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}delete.png", null, $Application->URL($_POST["_Script"], "btnDelete"), null, "return confirm('Are you sure to remove the information?');"),
];

$ListBaseURL = $Environment->URL();
$ListIconURL = $Environment->IconURL();

$ListSearchInterface = "
	" . HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Name") . "", 200), "{$Caption}", null, null) . "
	" . HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Identifier") . "", 200), "{$Caption}", null, null) . "
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
	new HTTP\InputValidation("{$Entity}Name", true),
	new HTTP\InputValidation("{$Entity}Identifier", true),
];

$InputDataVerificationSQL = "
	(
			" . ($Column = "{$Entity}Name") . " = '{$Database->Escape($_POST["{$Column}"])}'
		OR	" . ($Column = "{$Entity}Identifier") . " = '{$Database->Escape($_POST["{$Column}"])}'
	)
	AND	{$Entity}ID <> {$EntityID}
";

$InputThumbnailColumnList = "x{$Entity}Picture, x{$Entity}Avatar";
$InputThumbnailMaximumDimension = 48;
$InputDefaultValueFromSearchFieldList = "xPlatformID, xSomethingElseID";

require __DIR__ . "/../../common/entitymanagement_loadexistingdata.php"; // Load existing data

$InputInterface = "
	" . HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Name") . "", $Configuration["InputWidth"], null, true), "{$Caption}", null, null, $Configuration["FieldCaptionWidth"]) . "
	" . HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Identifier") . "", $Configuration["InputInlineWidth"], null, true), "{$Caption}", null, true, $Configuration["FieldCaptionInlineWidth"]) . "
	" . HTML\UI\Field(HTML\UI\RadioGroup("{$Entity}Is" . ($Caption = "Active") . "", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")]), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "
	" . HTML\UI\Input("btn" . ($Caption = "Input") . "", $Configuration["InputWidth"], null, true, INPUT_TYPE_HIDDEN) . "
	" . HTML\UI\Input("{$Entity}" . ($Caption = "ID") . "", $Configuration["InputWidth"], $EntityID, true, INPUT_TYPE_HIDDEN) . "
";
#endregion Input section

#region Import section
$ImportField = [
	new Database\Field("{$Entity}" . ($Field = "Name") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Identifier") . "", "{$Field}"),
	new Database\Field("{$Entity}Is" . ($Field = "Active") . "", "{$Field}"),
];
#endregion Import section

#region Delete section
#endregion Delete section

#region Export section
#endregion Export section

require __DIR__ . "/../../common/entitymanagement.php"; // Generic management process
?>