<?php
namespace sPHP;

#region Entity management common configuration
$EM = new EntityManagement($Table[$Entity = "ExportPayment"]);

$EM->ImportField([
	new Database\Field("" . ($Field = "Export") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("" . ($Field = "Payment") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("{$Entity}" . ($Field = "Note") . "", "{$Field}"),
	new Database\Field("{$Entity}Is" . ($Field = "Active") . "", "{$Field}"),
]);

$EM->InputValidation([
	new HTTP\InputValidation("ExportID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("PaymentID", null, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("{$Entity}Note", true),
]);

$EM->ValidateInput(function($Entity, $Database, $Table, $PrimaryKey, $ID){
	$Result = true;

	if($Table->Get( // Check for duplicate values for UNIQUE columns
		"
			(
					" . ($Column = "{$Entity}Note") . " = '{$Database->Escape($_POST["{$Column}"])}'
				AND	{$Table->Alias()}." . ($Column = "ExportID") . " = " . intval($_POST["{$Column}"]) . "
			)
			AND	{$PrimaryKey} <> {$ID}
		"
	, null, null, null, null, null, null))$Result = "Same note with same export exists!";

	return $Result;
});

$EM->ThumbnailColumn("x{$Entity}Picture");

$EM->BeforeInput(function($Entity, $Record){
	//$_POST[$Field = "{$Entity}PasswordHash"] = strlen($_POST["{$Entity}Password"]) ? md5($_POST["{$Entity}Password"]) : (is_null($Record) ? null : $Record["{$Field}"]);

	return true;
});

$EM->IntermediateEntity("Category, Event");
$EM->DefaultFromSearchColumn("ExportID, PaymentID");

$EM->ListColumn([
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Note") . "", "{$Caption}"),
	new HTML\UI\Datagrid\Column("" . ($Caption = "Export") . "LookupCaption", "{$Caption}", null, null),
	new HTML\UI\Datagrid\Column("Payment" . ($Caption = "Date") . "", "{$Caption}", FIELD_TYPE_SHORTDATE, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("Payment" . ($Caption = "Due") . "Date", "{$Caption} on", FIELD_TYPE_SHORTDATE, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("Payment" . ($Caption = "Pa") . "yDate", "{$Caption}id on", FIELD_TYPE_SHORTDATE, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("Payment" . ($Caption = "Amount") . "", "{$Caption}", FIELD_TYPE_NUMBER, null),
	new HTML\UI\Datagrid\Column("Payment" . ($Caption = "Pa") . "yAmount", "{$Caption}id", FIELD_TYPE_NUMBER, null),
	new HTML\UI\Datagrid\Column("Payment" . ($Caption = "Due") . "Amount", "{$Caption}", FIELD_TYPE_NUMBER, null),
	new HTML\UI\Datagrid\Column("Payment" . ($Caption = "Note") . "", "{$Caption}", null, null),
	new HTML\UI\Datagrid\Column("Payment" . ($Caption = "Method") . "Name", "{$Caption}", null, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("" . ($Caption = "Bank") . "AccountLookupCaption", "{$Caption} A/C", null, null),
	new HTML\UI\Datagrid\Column("{$Entity}Is" . ($Caption = "Active") . "", "{$Caption}", FIELD_TYPE_BOOLEANICON),
]);

$EM->Action([
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

$EM->OrderBy("PaymentDueAmount > 0 DESC, PaymentDueDate");
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
		if($EM->Input()){
			#region Create or update payment record
			$ExportPaymentRecord = $Table[$Entity]->GET("{$Table[$Entity]->Alias()}.{$Entity}ID = " . ($_POST["{$Entity}ID"] ? $_POST["{$Entity}ID"] : "@@IDENTITY") . "");
			$Table["Payment"]->PUT($_POST, $ExportPaymentRecord[0]["PaymentID"] ? "PaymentID = {$ExportPaymentRecord[0]["PaymentID"]}" : null, null, null);
			if(!$ExportPaymentRecord[0]["PaymentID"])$Table[$Entity]->Put(["PaymentID"=>$Table["Payment"]->Get("{$Table["Payment"]->Alias()}.PaymentID = @@IDENTITY")[0]["PaymentID"]], "{$Entity}ID = {$ExportPaymentRecord[0]["{$Entity}ID"]}");
			#endregion Create or update payment record

			$Terminal->Redirect($_POST["_Referer"]); // Redirect to previous location
		}
	}

	$EM->LoadExistingData();

	$EM->InputUIHTML([
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Note") . "", $EM->InputFullWidth(), null, null), "{$Caption}", null, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Export") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("Payment" . ($Caption = "Amount") . "", 150, 1, true), "{$Caption}: Due", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("Payment" . ($Caption = "Pa") . "yAmount", 150, 0, true), "{$Caption}id", null, true, null),
		HTML\UI\Field(HTML\UI\Input("Payment" . ($Caption = "Date") . "", $EM->InputDateWidth(), null, true, INPUT_TYPE_DATE), "{$Caption}: Entry", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("Payment" . ($Caption = "Due") . "Date", $EM->InputDateWidth(), null, true, INPUT_TYPE_DATE), "{$Caption}", null, true, null),
		HTML\UI\Field(HTML\UI\Input("" . ($Caption = "Payment") . "PayDate", $EM->InputDateWidth(), null, null, INPUT_TYPE_DATE), "{$Caption}", null, true, null),
		HTML\UI\Field(HTML\UI\Select("Payment" . ($Caption = "Method") . "ID", $Table[$OptionEntity = "Payment{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Bank") . "AccountID", $Table[$OptionEntity = "{$Caption}Account"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption} A/C", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("" . ($Caption = "Payment") . "Note", $EM->InputFullWidth(), null, null), "{$Caption} note", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\RadioGroup("{$Entity}Is" . ($Caption = "Active") . "", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")]), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
	]);

	print $EM->InputHTML();
}

#region List
$EM->SearchSQL([
	"1 = 1", // Custom fixed search condition
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Note") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "ExportID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}IsActive") . "", SetVariable($Column, "")) !== "" ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
]);

$EM->SearchUIHTML([
	HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Note") . "", 200), "{$Caption}", null, null),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}" . ($Caption = "Export") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}Is" . ($Caption = "Active") . "", [new Option(), new Option(0, "No"), new Option(1, "Yes")]), "{$Caption}", null, true),
]);

print $EM->ListHTML();
#region List
?>