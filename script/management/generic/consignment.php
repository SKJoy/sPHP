<?php
namespace sPHP;

#region Entity management common configuration
$EM = new EntityManagement($Table[$Entity = "Consignment"]);

$EM->ImportField([
	new Database\Field("{$Entity}" . ($Field = "Name") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "SendDate") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "ArrivalDate") . "", "{$Field}"),
	new Database\Field("" . ($Field = "Address") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("" . ($Field = "Export") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("{$Entity}Is" . ($Field = "Active") . "", "{$Field}"),
]);

$EM->InputValidation([
	new HTTP\InputValidation("{$Entity}Name", true),
	new HTTP\InputValidation("AddressID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("ExportID", true, VALIDATION_TYPE_INTEGER),
]);

$EM->ValidateInput(function($Entity, $Database, $Table, $PrimaryKey, $ID){
	$Result = true;

	if($Table->Get( // Check for duplicate values for UNIQUE columns
		"
			(
					" . ($Column = "{$Entity}Name") . " = '{$Database->Escape($_POST["{$Column}"])}'
				AND	{$Table->Alias()}." . ($Column = "ExportID") . " = " . intval($_POST["{$Column}"]) . "
			)
			AND	{$PrimaryKey} <> {$ID}
		"
	, null, null, null, null, null, null))$Result = "Same consignment in same export exists!";

	return $Result;
});

$EM->ThumbnailColumn("x{$Entity}Picture");

$EM->BeforeInput(function($Entity, $Record){
	//$_POST[$Field = "{$Entity}PasswordHash"] = strlen($_POST["{$Entity}Password"]) ? md5($_POST["{$Entity}Password"]) : (is_null($Record) ? null : $Record["{$Field}"]);

	return true;
});

$EM->IntermediateEntity("Category, Event");
$EM->DefaultFromSearchColumn("AddressID, ExportID");

$EM->ListColumn([
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Name") . "", "{$Caption}"),
	new HTML\UI\Datagrid\Column("" . ($Caption = "Export") . "LookupCaption", "{$Caption}", null, null),
	new HTML\UI\Datagrid\Column("" . ($Caption = "Address") . "LookupCaption", "{$Caption}", null, null),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Sen") . "dDate", "{$Caption}t", FIELD_TYPE_SHORTDATE),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Arrival") . "Date", "{$Caption}", FIELD_TYPE_SHORTDATE),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "S") . "hipmentCount", "{$Caption}", FIELD_TYPE_NUMBER),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "P") . "roductCount", "{$Caption}", FIELD_TYPE_NUMBER),
	new HTML\UI\Datagrid\Column("{$Entity}Is" . ($Caption = "Active") . "", "{$Caption}", FIELD_TYPE_BOOLEANICON),
]);

$EM->Action([
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}" . strtolower($ActionEntity = "Shipment") . ".png", null, $Application->URL("Management/Generic/{$ActionEntity}"), null, null, null, "{$ActionEntity}"),
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}" . strtolower($ActionEntity = "{$Entity}ExportProduct") . ".png", null, $Application->URL("Management/Generic/{$ActionEntity}"), null, null, null, "Product"),
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

$EM->OrderBy("ExportInitiationDate DESC, ExportName ASC, {$Entity}SendDate");
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

	$EM->InputUIHTML([
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Name") . "", $EM->InputFullWidth(), null, true), "{$Caption}", null, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Send") . "Date", $EM->InputDateWidth(), null, true, INPUT_TYPE_DATE), "Date: {$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Arrival") . "Date", $EM->InputDateWidth(), null, null, INPUT_TYPE_DATE), "{$Caption}", null, true, null),
		HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Address") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Export") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\RadioGroup("{$Entity}Is" . ($Caption = "Active") . "", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")]), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
	]);

	print $EM->InputHTML();
}

#region List
$EM->SearchSQL([
	"1 = 1", // Custom fixed search condition
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Name") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "AddressID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "ExportID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}IsActive") . "", SetVariable($Column, "")) !== "" ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
]);

$EM->SearchUIHTML([
	HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Name") . "", 200), "{$Caption}", null, null),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}" . ($Caption = "Export") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}" . ($Caption = "Address") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}Is" . ($Caption = "Active") . "", [new Option(), new Option(0, "No"), new Option(1, "Yes")]), "{$Caption}", null, true),
]);

print $EM->ListHTML();
#region List
?>