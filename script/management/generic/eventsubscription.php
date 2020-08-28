<?php
namespace sPHP;

#region Entity management common configuration
$EM = new EntityManagement($Table[$Entity = "EventSubscription"]);

$EM->ImportField([
	new Database\Field("" . ($Field = "Event") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("" . ($Field = "User") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("{$Entity}" . ($Field = "AdditionalAdult") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Child") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Infant") . "", "{$Field}"),
	new Database\Field("{$Entity}Use" . ($Field = "OwnTransport") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "PaymentDate") . "", "{$Field}"),
	new Database\Field("{$Entity}Amount" . ($Field = "Payable") . "", "{$Field}"),
	new Database\Field("{$Entity}Amount" . ($Field = "Paid") . "", "{$Field}"),
	new Database\Field("{$Entity}Is" . ($Field = "Active") . "", "{$Field}"),
]);

$EM->InputValidation([
	new HTTP\InputValidation("EventID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("UserID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("{$Entity}AdditionalAdult", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("{$Entity}Child", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("{$Entity}UseOwnTransport", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("{$Entity}AmountPayable", true, VALIDATION_TYPE_NUMBER),
	new HTTP\InputValidation("{$Entity}AmountPaid", true, VALIDATION_TYPE_NUMBER),
	new HTTP\InputValidation("{$Entity}AdditionalAdult", true, VALIDATION_TYPE_INTEGER),
]);

$EM->ValidateInput(function($Entity, $Database, $Table, $PrimaryKey, $ID){
	$Result = true;

	if($Table->Get( // Check for duplicate values for UNIQUE columns
		"
			(
					{$Table->Alias()}." . ($Column = "EventID") . " = " . intval($_POST["{$Column}"]) . "
				AND	{$Table->Alias()}." . ($Column = "UserID") . " = " . intval($_POST["{$Column}"]) . "
			)
			AND	{$PrimaryKey} <> {$ID}
		"
	, null, null, null, null, null, null))$Result = "Same " . strtolower($Table->FormalName()) . " for same user exists!";

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
$EM->DefaultFromSearchColumn("EventID, UserID");

$EM->ListColumn([
	new HTML\UI\Datagrid\Column("" . ($Caption = "Event") . "LookupCaption", "{$Caption}"),
	new HTML\UI\Datagrid\Column("" . ($Caption = "User") . "LookupCaption", "{$Caption}"),
	new HTML\UI\Datagrid\Column("{$Entity}Additional" . ($Caption = "A") . "dult", "A{$Caption}", FIELD_TYPE_NUMBER),
	new HTML\UI\Datagrid\Column("{$Entity}Total" . ($Caption = "A") . "dult", "T{$Caption}", FIELD_TYPE_NUMBER),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "C") . "hild", "{$Caption}", FIELD_TYPE_NUMBER),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "I") . "nfant", "{$Caption}", FIELD_TYPE_NUMBER),
	new HTML\UI\Datagrid\Column("{$Entity}Use" . ($Caption = "O") . "wnTransport", "{$Caption}T", FIELD_TYPE_BOOLEANICON),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Payment") . "Date", "{$Caption}", FIELD_TYPE_SHORTDATE, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("{$Entity}Amount" . ($Caption = "Payable") . "", "{$Caption}", FIELD_TYPE_NUMBER),
	new HTML\UI\Datagrid\Column("{$Entity}Amount" . ($Caption = "Paid") . "", "{$Caption}", FIELD_TYPE_NUMBER),
	new HTML\UI\Datagrid\Column("{$Entity}Amount" . ($Caption = "Due") . "", "{$Caption}", FIELD_TYPE_NUMBER),
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

$EM->OrderBy("EventDateBegin");
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

	#region Custom code
	$EventInput = new HTML\UI\Input("Event", $EM->InputFullWidth(), null, null);
	$UserInput = new HTML\UI\Input("User", $EM->InputFullWidth(), null, null);
	#endregion Custom code

	$EM->InputUIHTML([
		//HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Event") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", null, null, $EM->FieldCaptionWidth()),
		//HTML\UI\Field(HTML\UI\Select("" . ($Caption = "User") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field($EventInput->HTML(), "Event", null, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field($UserInput->HTML(), "User", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Additional" . ($Caption = "Adult") . "", 50, 0, true, INPUT_TYPE_NUMBER, null, null, null, null, 0, 0), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Child") . "", 50, 0, true, INPUT_TYPE_NUMBER, null, null, null, null, 0, 0), "{$Caption}", null, true, null),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Infant") . "", 50, 0, true, INPUT_TYPE_NUMBER, null, null, null, null, 0, 0), "{$Caption}", null, true, null),
		HTML\UI\Field(HTML\UI\RadioGroup("{$Entity}Use" . ($Caption = "Own") . "Transport", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")], 0), "{$Caption} transport", null, true, null),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Payment") . "Date", $EM->InputDateWidth(), null, null, INPUT_TYPE_DATE), "{$Caption} date", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Amount" . ($Caption = "Payable") . "", 150, 0, true, INPUT_TYPE_NUMBER, null, null, null, null, 0.01, 0), "{$Caption}", null, true, null),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Amount" . ($Caption = "Paid") . "", 150, 0, true, INPUT_TYPE_NUMBER, null, null, null, null, 0.01, 0), "{$Caption}", null, true, null),
		HTML\UI\Field(HTML\UI\Textarea("{$Entity}" . ($Caption = "Note") . "", $EM->InputFullWidth(), $Configuration["TextareaHeight"]), "{$Caption}", true, null, $EM->FieldCaptionWidth(), null, null, null, "Optional"),
		HTML\UI\Field(HTML\UI\RadioGroup("{$Entity}Is" . ($Caption = "Active") . "", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")]), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		"
			<script>
				sJS.DHTML.Autofill.Make('{$UserInput->ID()}', 3, '{$Application->URL("API/V1/AJAXEntity", "Entity=" . ($OptionEntity = "User") . "&Column=U.UserEmail,U.UserNameFirst,U.UserNameMiddle,U.UserNameLast,U.UserPhoneMobile,U.UserPhoneHome,U.UserPhoneWork,U.UserPhoneOther")}', '{$OptionEntity}ID', null, '" . ($CaptionKey = "{$OptionEntity}LookupCaption") . "', null, '" . (($OptionEntityID = isset($_POST["{$OptionEntity}ID"]) ? $_POST["{$OptionEntity}ID"] : null) ? $Table["{$OptionEntity}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}ID = {$_POST["{$OptionEntity}ID"]}")[0]["{$CaptionKey}"] : null) . "', {$OptionEntityID});
				sJS.DHTML.Autofill.Make('{$EventInput->ID()}', 3, '{$Application->URL("API/V1/AJAXEntity", "Entity=" . ($OptionEntity = "Event") . "&Column=E.EventName")}', '{$OptionEntity}ID', null, '" . ($CaptionKey = "{$OptionEntity}LookupCaption") . "', null, '" . (($OptionEntityID = isset($_POST["{$OptionEntity}ID"]) ? $_POST["{$OptionEntity}ID"] : null) ? $Table["{$OptionEntity}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}ID = {$_POST["{$OptionEntity}ID"]}")[0]["{$CaptionKey}"] : null) . "', {$OptionEntityID});
			</script>
		",
	]);

	print $EM->InputHTML();
}

#region List
$EM->SearchSQL([
	"1 = 1", // Custom fixed search condition
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Note") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "EventID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "UserID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}IsActive") . "", SetVariable($Column, "")) !== "" ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
]);

$EM->SearchUIHTML([
	HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Note") . "", 200), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}" . ($Caption = "Event") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}" . ($Caption = "User") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}Is" . ($Caption = "Active") . "", [new Option(), new Option(0, "No"), new Option(1, "Yes")]), "{$Caption}", null, true),
]);

print $EM->ListHTML();
#region List
?>