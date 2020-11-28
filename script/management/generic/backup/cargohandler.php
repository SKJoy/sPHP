<?php
namespace sPHP;

#region General section
$Entity = "CargoHandler";
$LowercaseEntity = strtolower($Entity);
$EntityName = $Table[$Entity]->FormalName();
$LowercaseEntityName = strtolower($Table[$Entity]->FormalName());
$EntityUploadPath = "{$Environment->UploadPath()}{$LowercaseEntity}/";
$EntityID = isset($_POST["{$Entity}ID"]) && (($_POST["{$Entity}ID"] = is_array($_POST["{$Entity}ID"]) ? $_POST["{$Entity}ID"] : intval($_POST["{$Entity}ID"])) || is_array($_POST["{$Entity}ID"])) ? $_POST["{$Entity}ID"] : 0;
#endregion General section

#region List section
$ListSearchSQL[] = "1 = 1"; // Custom fixed search condition
$ListSearchSQL[] = SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Name") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null;
$ListSearchSQL[] = SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Email") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null;
$ListSearchSQL[] = SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Phone") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null;
$ListSearchSQL[] = SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Contact") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null;
$ListSearchSQL[] = SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "UserID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null;
$ListSearchSQL[] = SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}IsActive") . "", SetVariable($Column, "")) !== "" ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null;

$ListOrderBy = "{$Entity}Name";
$ListOrder = "ASC";
$ListRecordsPerPage = $Configuration["DatagridRowsPerPage"];

$ListColumn = [ // Columns to display
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Name") . "", "{$Caption}"),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Email") . "", "{$Caption}", FIELD_TYPE_EMAIL),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Phone") . "", "{$Caption}", FIELD_TYPE_PHONE),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Contact") . "", "{$Caption}"),
	new HTML\UI\Datagrid\Column("" . ($Caption = "User") . "LookupCaption", "{$Caption}"),
	new HTML\UI\Datagrid\Column("{$Entity}Is" . ($Caption = "Active") . "", "{$Caption}", FIELD_TYPE_BOOLEANICON),
];

$ListAction = [ // Action
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}edit.png", null, $Application->URL($_POST["_Script"], "btnInput")),
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}delete.png", null, $Application->URL($_POST["_Script"], "btnDelete"), null, "return confirm('Are you sure to remove the information?');"),
];

$ListBaseURL = $Environment->URL();
$ListIconURL = $Environment->IconURL();

$ListSearchInterface = "
	" . HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Name") . "", 200), "{$Caption}", null, null) . "
	" . HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Email") . "", 200), "{$Caption}", null, true) . "
	" . HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Phone") . "", 200), "{$Caption}", null, true) . "
	" . HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Contact") . "", 200), "{$Caption}", null, true) . "
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
	new HTTP\InputValidation("{$Entity}Name", true),
	new HTTP\InputValidation("{$Entity}Email", null, VALIDATION_TYPE_EMAIL),
	new HTTP\InputValidation("{$Entity}Phone", null, VALIDATION_TYPE_PHONE),
	new HTTP\InputValidation("UserID", true, VALIDATION_TYPE_INTEGER),
];

$InputDataVerificationSQL = "
	(
			" . ($Column = "{$Entity}Name") . " = '{$Database->Escape($_POST["{$Column}"])}'
		/*AND	{$Table["{$Entity}"]->Alias()}." . ($Column = "UserID") . " = " . intval($_POST["{$Column}"]) . "*/
	)
	AND	{$Entity}ID <> {$EntityID}
";

$InputThumbnailColumnList = "x{$Entity}Picture, x{$Entity}Avatar";
$InputThumbnailMaximumDimension = 48;
$InputDefaultValueFromSearchFieldList = "UserID, xSomethingElseID";

require __DIR__ . "/../../common/entitymanagement_loadexistingdata.php"; // Load existing data

#region Custom code
$UserInput = new HTML\UI\Input("{$Entity}" . ($Caption = "User") . "", $Configuration["InputFullWidth"], null, null);
#endregion Custom code

$InputInterface = "
	" . HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Name") . "", $Configuration["InputWidth"], null, true), "{$Caption}", null, null, $Configuration["FieldCaptionWidth"]) . "
	" . HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Contact") . "", $Configuration["InputInlineWidth"], null, null), "{$Caption}", null, true, $Configuration["FieldCaptionInlineWidth"]) . "
	" . HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Email") . "", $Configuration["InputWidth"], null, null, INPUT_TYPE_EMAIL), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "
	" . HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Phone") . "", $Configuration["InputInlineWidth"], null, null, INPUT_TYPE_PHONE), "{$Caption}", null, true, $Configuration["FieldCaptionInlineWidth"]) . "
	" . HTML\UI\Field($UserInput->HTML(), "User", true, null, $Configuration["FieldCaptionWidth"]) . "
	" . HTML\UI\Field(HTML\UI\RadioGroup("{$Entity}Is" . ($Caption = "Active") . "", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")]), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "
	" . HTML\UI\Input("btn" . ($Caption = "Input") . "", $Configuration["InputWidth"], null, true, INPUT_TYPE_HIDDEN) . "
	" . HTML\UI\Input("{$Entity}" . ($Caption = "ID") . "", $Configuration["InputWidth"], $EntityID, true, INPUT_TYPE_HIDDEN) . "

	<script>
		sJS.DHTML.Autofill.Make('{$UserInput->ID()}', 3, '{$Application->URL("API/V1/AJAXEntity", "Entity=" . ($OptionEntity = "User") . "&Column={$OptionEntity}NameFirst,{$OptionEntity}NameMiddle,{$OptionEntity}NameLast,{$OptionEntity}PhoneMobile,{$OptionEntity}PhoneHome,{$OptionEntity}PhoneWork,{$OptionEntity}PhoneOther,{$OptionEntity}AddressStreet,{$OptionEntity}AddressCity,{$OptionEntity}AddressState,{$OptionEntity}AddressZIP")}', '{$OptionEntity}ID', '{$OptionEntity}ID', '{$OptionEntity}LookupCaption');
	</script>
";
#endregion Input section

#region Import section
$ImportField = [
	new Database\Field("{$Entity}" . ($Field = "Name") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Contact") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Email") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Phone") . "", "{$Field}"),
	new Database\Field("" . ($Field = "User") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("{$Entity}Is" . ($Field = "Active") . "", "{$Field}"),
];
#endregion Import section

#region Delete section
#endregion Delete section

#region Export section
#endregion Export section

require __DIR__ . "/../../common/entitymanagement.php"; // Generic management process
?>