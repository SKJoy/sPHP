<?php
namespace sPHP;

#region Entity management common configuration
$EM = new EntityManagement($Table[$Entity = "ApplicationChangeLog"]);

$EM->ImportField([
	new Database\Field("{$Entity}" . ($Field = "Time") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Title") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Description") . "", "{$Field}"),
	new Database\Field("{$Entity}Is" . ($Field = "Active") . "", "{$Field}"),
]);

$EM->InputValidation([
	new HTTP\InputValidation("{$Entity}Time", true),
	new HTTP\InputValidation("{$Entity}Title", true),
	new HTTP\InputValidation("{$Entity}Description", null),
	new HTTP\InputValidation("{$Entity}IsActive", null, VALIDATION_TYPE_INTEGER),
]);

$EM->ValidateInput(function($Entity, $Database, $Table, $PrimaryKey, $ID){
	$Result = true;

	if($Table->Get( // Check for duplicate values for UNIQUE columns
		"
			(
					" . ($Column = "{$Entity}Time") . " = '{$Database->Escape($_POST["{$Column}"])}'
				AND	" . ($Column = "{$Entity}Title") . " = '{$Database->Escape($_POST["{$Column}"])}'
			)
			AND	{$PrimaryKey} <> {$ID}
		"
	, null, null, null, null, null, null))$Result = "Same time and title for the same " . strtolower($Table->FormalName()) . " exists!";

	return $Result;
});

$EM->ThumbnailColumn("x{$Entity}Picture");

$EM->BeforeInput(function($Entity, $Record){
	//$_POST[$Field = "{$Entity}PasswordHash"] = strlen($_POST["{$Entity}Password"]) ? md5($_POST["{$Entity}Password"]) : (is_null($Record) ? null : $Record["{$Field}"]);

	return true;
});

$EM->IntermediateEntity("Category, Event");
$EM->DefaultFromSearchColumn("TerminalID, {$Entity}FromAddressID, {$Entity}ToAddressID, TransportRouteTypeID, CargoHandlerID");

$EM->ListColumn([
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Time") . "", "{$Caption}", FIELD_TYPE_DATETIME),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Title") . "", "{$Caption}"),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Description") . "", "{$Caption}"),
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
	$EM->Delete();
	$Terminal->Redirect($_SERVER["HTTP_REFERER"]);
}

if(isset($_POST["btnInput"])){
	if(isset($_POST["btnSubmit"])){
		$_POST["{$Entity}Time"] = "{$_POST["{$Entity}Date"]} {$_POST["{$Entity}Time"]}:00";
//var_dump($_POST["{$Entity}Time"]); exit;
		if($EM->Input())$Terminal->Redirect("{$_POST["_Referer"]}&SucceededAction=Input"); // Redirect to previous location
	}

	$EM->LoadExistingData();

	#region Custom code
	if($EM->EntityID()){ // Existing record
		$ApplicationChangeLogTime = strtotime($_POST["{$Entity}Time"]);
		$_POST["{$Entity}Date"] = date("Y-m-d", $ApplicationChangeLogTime);
		$_POST["{$Entity}Time"] = date("H:i", $ApplicationChangeLogTime);
	}
	else{

	}
	#endregion Custom code

	$EM->InputUIHTML([
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Date") . "", $EM->InputDateWidth(), date("Y-m-d"), true, INPUT_TYPE_DATE, null), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Time") . "", $EM->InputTimeWidth(), date("H:i"), true, INPUT_TYPE_TIME, null), "{$Caption}", null, true, null),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Title") . "", $EM->InputFullWidth(), null, true), "{$Caption}", true, null, $EM->FieldCaptionInlineWidth()),
		HTML\UI\Field(HTML\UI\Textarea("{$Entity}" . ($Caption = "Description") . "", $EM->InputFullWidth(), 150, null, true), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\RadioGroup("{$Entity}Is" . ($Caption = "Active") . "", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")]), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
	]);

	print $EM->InputHTML();
}

#region List
$EM->SearchSQL([
	"1 = 1", // Custom fixed search condition
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Title") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Description") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}IsActive") . "", SetVariable($Column, "")) !== "" ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
]);

$EM->SearchUIHTML([
	HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Title") . "", 200), "{$Caption}", null, null),
	HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Description") . "", 200), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}Is" . ($Caption = "Active") . "", [new Option(), new Option(0, "No"), new Option(1, "Yes")]), "{$Caption}", null, true),
]);

print "{$EM->ListHTML()}";
if(SetVariable("SucceededAction") == "Input")print HTML\UI\Toast("{$Table["{$Entity}"]->FormalName()} input successful.");
#region List
?>

<style>
	html > body > main > .Datagrid > .Content > .Grid > tbody > tr > td:nth-child(2){white-space: nowrap;}
	html > body > main > .Datagrid > .Content > .Grid > tbody > tr > td:nth-child(3){width: 25%;}
</style>