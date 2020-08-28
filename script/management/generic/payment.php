<?php
namespace sPHP;

#region Entity management common configuration
$EM = new EntityManagement($Table[$Entity = "Payment"]);

$EM->ImportField([
	new Database\Field("{$Entity}" . ($Field = "Date") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "DueDate") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "PayDate") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Amount") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "PayAmount") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Reference") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Note") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Instruction") . "", "{$Field}"),
	new Database\Field("Payment" . ($Field = "Method") . "ID", "{$Field}", null, $Table["Payment{$Field}"], "{$Field}Name"),
	new Database\Field("" . ($Field = "BankAccount") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("{$Entity}Is" . ($Field = "Active") . "", "{$Field}"),
]);

$EM->InputValidation([
	new HTTP\InputValidation("{$Entity}Date", true),
	new HTTP\InputValidation("{$Entity}DueDate", true),
	new HTTP\InputValidation("{$Entity}PayDate", true),
	new HTTP\InputValidation("{$Entity}Amount", true),
	new HTTP\InputValidation("{$Entity}PayAmount", true),
	new HTTP\InputValidation("PaymentMethodID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("BankAccountID", true, VALIDATION_TYPE_INTEGER),
]);

$EM->ValidateInput(function($Entity, $Database, $Table, $PrimaryKey, $ID){
	$Result = true;

	if($Table->Get( // Check for duplicate values for UNIQUE columns
		"
			(
					1  = 0
				/*AND	" . ($Column = "{$Entity}Name") . " = '{$Database->Escape($_POST["{$Column}"])}'*/
				/*AND	{$Table->Alias()}." . ($Column = "PaymentMethodID") . " = " . intval($_POST["{$Column}"]) . "*/
			)
			AND	{$PrimaryKey} <> {$ID}
		"
	, null, null, null, null, null, null))$Result = "Same project on same platform exists!";

	return $Result;
});

$EM->ThumbnailColumn("x{$Entity}Picture");

$EM->BeforeInput(function($Entity, $Record){
	//$_POST[$Field = "{$Entity}PasswordHash"] = strlen($_POST["{$Entity}Password"]) ? md5($_POST["{$Entity}Password"]) : (is_null($Record) ? null : $Record["{$Field}"]);

	return true;
});

$EM->IntermediateEntity("Category, Event");
$EM->DefaultFromSearchColumn("PaymentMethodID, BankAccountID");

$EM->ListColumn([
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Date") . "", "{$Caption}", FIELD_TYPE_SHORTDATE),
	new HTML\UI\Datagrid\Column("{$Entity}Date" . ($Caption = "Due") . "", "{$Caption} on", FIELD_TYPE_SHORTDATE),
	new HTML\UI\Datagrid\Column("{$Entity}Date" . ($Caption = "Paid") . "", "{$Caption} on", FIELD_TYPE_SHORTDATE),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Amount") . "", "{$Caption}", FIELD_TYPE_NUMBER),
	new HTML\UI\Datagrid\Column("{$Entity}Amount" . ($Caption = "Paid") . "", "{$Caption}", FIELD_TYPE_NUMBER),
	new HTML\UI\Datagrid\Column("{$Entity}Amount" . ($Caption = "Due") . "", "{$Caption}", FIELD_TYPE_NUMBER),
	new HTML\UI\Datagrid\Column("Payment" . ($Caption = "Method") . "Name", "{$Caption}", null, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("Bank" . ($Caption = "Account") . "LookupCaption", "{$Caption}", null, null),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Note") . "", "{$Caption}"),
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

$EM->OrderBy("{$Entity}DateDue");
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
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Date") . "", $EM->InputDateWidth(), date("Y-m-d"), true, INPUT_TYPE_DATE), "{$Caption}: Entry", null, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Date" . ($Caption = "Due") . "", $EM->InputDateWidth(), null, true, INPUT_TYPE_DATE), "{$Caption} on", null, true, null),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Date" . ($Caption = "Pa") . "y", $EM->InputDateWidth(), null, true, INPUT_TYPE_DATE), "{$Caption}id on", null, true, null),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Amount") . "", $EM->InputDateWidth(), 1, true), "{$Caption}: Payable", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Amount" . ($Caption = "Pa") . "id", $EM->InputDateWidth(), 0, true), "{$Caption}id", null, true, null),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Reference") . "", $EM->InputFullWidth(), null, null, null), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Select("{$Entity}" . ($Caption = "Method") . "ID", $Table[$OptionEntity = "{$Entity}{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Select("Bank" . ($Caption = "Account") . "ID", $Table[$OptionEntity = "Bank{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Note") . "", $EM->InputFullWidth(), null, null, null), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Textarea("{$Entity}" . ($Caption = "Instruction") . "", $EM->InputFullWidth(), $Configuration["TextareaHeight"]), "{$Caption}", true, null, $EM->FieldCaptionWidth(), null, null, null, "Optional"),
		HTML\UI\Field(HTML\UI\RadioGroup("{$Entity}Is" . ($Caption = "Active") . "", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")]), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
	]);

	print $EM->InputHTML();
}

#region List
$EM->SearchSQL([
	"1 = 1", // Custom fixed search condition
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "PaymentMethodID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "BankAccountID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Note") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Instruction") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}IsActive") . "", SetVariable($Column, "")) !== "" ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
]);

$EM->SearchUIHTML([
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}Payment" . ($Caption = "Method") . "ID", $Table[$OptionEntity = "Payment{$Caption}"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", null, null),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}Bank" . ($Caption = "Account") . "ID", $Table[$OptionEntity = "Bank{$Caption}"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Note") . "", 200), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Instruction") . "", 200), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}Is" . ($Caption = "Active") . "", [new Option(), new Option(0, "No"), new Option(1, "Yes")]), "{$Caption}", null, true),
]);

print $EM->ListHTML();
#region List
?>