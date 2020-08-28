<?php
namespace sPHP;

#region Entity management common configuration
$EM = new EntityManagement($Table[$Entity = "ShipmentConsignmentExportProductCarton"]);

$EM->ImportField([
	new Database\Field("" . ($Field = "ShipmentConsignmentExportProduct") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("" . ($Field = "Carton") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("{$Entity}" . ($Field = "Quantity") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "QuantitySuffix") . "", "{$Field}"),
	new Database\Field("{$Entity}Is" . ($Field = "Active") . "", "{$Field}"),
]);

$EM->InputValidation([
	new HTTP\InputValidation("ShipmentConsignmentExportProductID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("CartonID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("{$Entity}Quantity", true, VALIDATION_TYPE_INTEGER),
]);

$EM->ValidateInput(function($Entity, $Database, $Table, $PrimaryKey, $ID){
	$Result = true;

	if($Table->Get( // Check for duplicate values for UNIQUE columns
		"
			(
					/*" . ($Column = "{$Entity}Quantity") . " = '{$Database->Escape($_POST["{$Column}"])}'*/
					{$Table->Alias()}." . ($Column = "ShipmentConsignmentExportProductID") . " = " . intval($_POST["{$Column}"]) . "
				AND	{$Table->Alias()}." . ($Column = "CartonID") . " = " . intval($_POST["{$Column}"]) . "
			)
			AND	{$PrimaryKey} <> {$ID}
			AND	0 = 1
		"
	, null, null, null, null, null, null))$Result = "Same product in same carton exists!";

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
$EM->DefaultFromSearchColumn("ShipmentConsignmentExportProductID, CartonID, ShipmentID");

$EM->ListColumn([
	new HTML\UI\Datagrid\Column("" . ($Caption = "Product") . "Name", "{$Caption}", null, null),
	new HTML\UI\Datagrid\Column("" . ($Caption = "Carton") . "Name", "{$Caption}"),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Quantity") . "", "{$Caption}", FIELD_TYPE_NUMBER),
	new HTML\UI\Datagrid\Column("Carton" . ($Caption = "Length") . "", "{$Caption}", FIELD_TYPE_NUMBER, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("Carton" . ($Caption = "Breadth") . "", "{$Caption}", FIELD_TYPE_NUMBER, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("Carton" . ($Caption = "Height") . "", "{$Caption}", FIELD_TYPE_NUMBER, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("Carton" . ($Caption = "L") . "engthMeasureCode", "{$Caption}M", null, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "CBM") . "", "{$Caption}", FIELD_TYPE_NUMBER),
	new HTML\UI\Datagrid\Column("Carton" . ($Caption = "Weight") . "", "{$Caption}", FIELD_TYPE_NUMBER, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("Carton" . ($Caption = "W") . "eightMeasureCode", "{$Caption}M", null, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("{$Entity}Quantity" . ($Caption = "Suffix") . "", "{$Caption}", FIELD_TYPE_NUMBER),
	//new HTML\UI\Datagrid\Column("" . ($Caption = "Carton") . "LookupCaption", "{$Caption}"),
	new HTML\UI\Datagrid\Column("" . ($Caption = "Shipment") . "Name", "{$Caption}"),
	//new HTML\UI\Datagrid\Column("" . ($Caption = "Consignment") . "Name", "{$Caption}"),
	//new HTML\UI\Datagrid\Column("" . ($Caption = "Export") . "Name", "{$Caption}"),
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

$EM->OrderBy("CartonName");
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
//var_dump($_POST);
	$EM->InputUIHTML([
		HTML\UI\Field(HTML\UI\Select("ShipmentConsignmentExport" . ($Caption = "Product") . "ID", $Table[$OptionEntity = "ShipmentConsignmentExport{$Caption}"]->Get(" " . (isset($_POST["ShipmentID"]) && $_POST["ShipmentID"] ? "S.ShipmentID = " . intval($_POST["ShipmentID"]) . " AND " : null) . "{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption", null, null, null), "{$Caption}", null, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Carton") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Quantity") . "", 100, null, true, INPUT_TYPE_NUMBER), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Quantity" . ($Caption = "Suffix") . "", $EM->InputWidth(), null, null, null), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\RadioGroup("{$Entity}Is" . ($Caption = "Active") . "", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")]), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
	]);

	print $EM->InputHTML();
}

#region List
$EM->SearchSQL([
	"1 = 1", // Custom fixed search condition
	//SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Name") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "ShipmentConsignmentExportProductID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "CartonID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}IsActive") . "", SetVariable($Column, "")) !== "" ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "ShipmentID") . "", SetVariable($Column)) ? "S.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
]);

$EM->SearchUIHTML([
	//HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Name") . "", 200), "{$Caption}", null, null),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}ShipmentConsignmentExport" . ($Caption = "Product") . "ID", $Table[$OptionEntity = "ShipmentConsignmentExport{$Caption}"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", null, null),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}" . ($Caption = "Carton") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}Is" . ($Caption = "Active") . "", [new Option(), new Option(0, "No"), new Option(1, "Yes")]), "{$Caption}", null, true),
	"<input type=\"hidden\" name=\"{$Configuration["SearchInputPrefix"]}ShipmentID\" value=\"{$_POST["{$Configuration["SearchInputPrefix"]}ShipmentID"]}\">"
]);

print $EM->ListHTML();
#region List
?>