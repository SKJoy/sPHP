<?php
namespace sPHP;

#region Entity management common configuration
$EM = new EntityManagement($Table[$Entity = "Carton"]);

$EM->ImportField([
	new Database\Field("{$Entity}" . ($Field = "Name") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Length") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Breadth") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Height") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "LengthMeasure") . "ID", "{$Field}", null, $Table["Measure"], "{$Field}Name"),
	new Database\Field("{$Entity}" . ($Field = "Weight") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "WeightCapacity") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "WeightMeasure") . "ID", "{$Field}", null, $Table["Measure"], "{$Field}Name"),
	new Database\Field("" . ($Field = "Department") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("{$Entity}Is" . ($Field = "Active") . "", "{$Field}"),
]);

$EM->InputValidation([
	new HTTP\InputValidation("{$Entity}Name", true),
	new HTTP\InputValidation("{$Entity}Length", true, VALIDATION_TYPE_POSITIVE),
	new HTTP\InputValidation("{$Entity}Breadth", true, VALIDATION_TYPE_POSITIVE),
	new HTTP\InputValidation("{$Entity}Height", true, VALIDATION_TYPE_POSITIVE),
	new HTTP\InputValidation("{$Entity}LengthMeasureID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("{$Entity}" . ($Caption = "Weight") . "", true, VALIDATION_TYPE_NONNEGATIVE, "{$Caption}"),
	new HTTP\InputValidation("{$Entity}" . ($Caption = "Weight") . "Capacity", true, VALIDATION_TYPE_NONNEGATIVE, "{$Caption} capacity"),
	new HTTP\InputValidation("{$Entity}WeightMeasureID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("DepartmentID", true, VALIDATION_TYPE_INTEGER),
]);

$EM->ValidateInput(function($Entity, $Database, $Table, $PrimaryKey, $ID){
	$Result = true;

	if($Table->Get( // Check for duplicate values for UNIQUE columns
		"
			(
					" . ($Column = "{$Entity}Name") . " = '{$Database->Escape($_POST["{$Column}"])}'
				AND	{$Table->Alias()}." . ($Column = "DepartmentID") . " = " . intval($_POST["{$Column}"]) . "
			)
			AND	{$PrimaryKey} <> {$ID}
		"
	, null, null, null, null, null, null))$Result = "Same carton with same department exists!";

	return $Result;
});

$EM->ThumbnailColumn("x{$Entity}Picture");

$EM->BeforeInput(function($Entity, $Record){
	//$_POST[$Field = "{$Entity}PasswordHash"] = strlen($_POST["{$Entity}Password"]) ? md5($_POST["{$Entity}Password"]) : (is_null($Record) ? null : $Record["{$Field}"]);

	return true;
});

$EM->AfterInput(function($EntityName, $AffectedRecord, $Record, $Table){

	return true;
});

$EM->IntermediateEntity("xCategory, xEvent");
$EM->DefaultFromSearchColumn("{$Entity}LengthMeasureID, {$Entity}WeightMeasureID, DepartmentID");

$EM->ListColumn([
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Name") . "", "{$Caption}"),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Length") . "", "{$Caption}", FIELD_TYPE_NUMBER),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Breadth") . "", "{$Caption}", FIELD_TYPE_NUMBER),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Height") . "", "{$Caption}", FIELD_TYPE_NUMBER),
	new HTML\UI\Datagrid\Column("{$Entity}Length" . ($Caption = "Measure") . "Name", "L {$Caption}", null, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Weight") . "", "{$Caption}", FIELD_TYPE_NUMBER),
	new HTML\UI\Datagrid\Column("{$Entity}Weight" . ($Caption = "Capacity") . "", "W {$Caption}", FIELD_TYPE_NUMBER),
	new HTML\UI\Datagrid\Column("{$Entity}Weight" . ($Caption = "Measure") . "Name", "W {$Caption}", null, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("" . ($Caption = "Department") . "Name", "{$Caption}", null, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("" . ($Caption = "Organization") . "Name", "{$Caption}", null, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("" . ($Caption = "Enterprise") . "LookupCaption", "{$Caption}", null, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("{$Entity}Is" . ($Caption = "Active") . "", "{$Caption}", FIELD_TYPE_BOOLEANICON),
]);

$EM->Action([
	//new HTML\UI\Datagrid\Action("{$Environment->IconURL()}" . strtolower($ActionEntity = "{$Entity}Property") . ".png", null, $Application->URL("Management/Generic/{$ActionEntity}"), null, null, null, "{$ActionEntity}"),
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

	$EM->InputUIHTML([
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Name") . "", $EM->InputFullWidth(), null, true), "{$Caption}", null, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Length") . "", 100, 1, true, INPUT_TYPE_NUMBER, null, null, null, null, 0.001, 0.001), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Breadth") . "", 100, 1, true, INPUT_TYPE_NUMBER, null, null, null, null, 0.001, 0.001), "{$Caption}", null, true, null),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Height") . "", 100, 1, true, INPUT_TYPE_NUMBER, null, null, null, null, 0.001, 0.001), "{$Caption}", null, true, null),
		HTML\UI\Field(HTML\UI\Select("{$Entity}Length" . ($Caption = "Measure") . "ID", $Table[$OptionEntity = "Measure"]->Get("{$Table["{$OptionEntity}"]->Alias()}T.{$OptionEntity}TypeIdentifier = 'LENGTH' AND {$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", null, true, null),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Weight") . "", 100, 1, true, INPUT_TYPE_NUMBER, null, null, null, null, 0.001, 0), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Weight" . ($Caption = "Capacity") . "", 100, 1, true, INPUT_TYPE_NUMBER, null, null, null, null, 0.001, 0), "{$Caption}", null, true, null),
		HTML\UI\Field(HTML\UI\Select("{$Entity}Weight" . ($Caption = "Measure") . "ID", $Table[$OptionEntity = "Measure"]->Get("{$Table["{$OptionEntity}"]->Alias()}T.{$OptionEntity}TypeIdentifier = 'WEIGHT' AND {$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", null, true, null),
		HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Department") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\RadioGroup("{$Entity}Is" . ($Caption = "Active") . "", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")]), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
	]);

	print $EM->InputHTML();
}

#region List
$EM->SearchSQL([
	"1 = 1", // Custom fixed search condition
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Name") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}LengthMeasureID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}WeightMeasureID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "DepartmentID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}IsActive") . "", SetVariable($Column, "")) !== "" ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
]);

$EM->SearchUIHTML([
	HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Name") . "", 200), "{$Caption}", null, null),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Length") . "MeasureID", $Table[$OptionEntity = "Measure"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption} measure", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Weight") . "MeasureID", $Table[$OptionEntity = "Measure"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption} measure", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}" . ($Caption = "Department") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}Is" . ($Caption = "Active") . "", [new Option(), new Option(0, "No"), new Option(1, "Yes")]), "{$Caption}", null, true),
]);

print $EM->ListHTML();
#region List
?>