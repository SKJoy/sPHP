<?php
namespace sPHP;

#region Entity management common configuration
$EM = new EntityManagement($Table[$Entity = "User"]);

$EM->ImportField([
	new Database\Field("{$Entity}" . ($Field = "Email") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "NameFirst") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "NameMiddle") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "NameLast") . "", "{$Field}"),
	new Database\Field("" . ($Field = "Gender") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("{$Entity}" . ($Field = "PhoneMobile") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "PhoneHome") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "PhoneWork") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "PhoneOther") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "URL") . "", "{$Field}"),
	new Database\Field("{$Entity}Address" . ($Field = "Country") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("" . ($Field = "Language") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("{$Entity}Is" . ($Field = "Active") . "", "{$Field}"),
]);

$EM->InputValidation([
	new HTTP\InputValidation("{$Entity}Email", true),
	new HTTP\InputValidation("{$Entity}NameFirst", true),
	new HTTP\InputValidation("{$Entity}NameLast", true),
	new HTTP\InputValidation("GenderID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("{$Entity}PhoneMobile", true),
	new HTTP\InputValidation("{$Entity}AddressCountryID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("LanguageID", true, VALIDATION_TYPE_INTEGER),
]);

$EM->ValidateInput(function($Entity, $Database, $Table, $PrimaryKey, $ID){
	$Result = true;

	if($Table->Get( // Check for duplicate values for UNIQUE columns
		"
			(
					" . ($Column = "{$Entity}Email") . " = '{$Database->Escape($_POST["{$Column}"])}'
				/*OR	" . ($Column = "{$Entity}PhoneMobile") . " = '{$Database->Escape($_POST["{$Column}"])}'*/
			)
			AND	{$PrimaryKey} <> {$ID}
		"
	, null, null, null, null, null, null))$Result = "Email or mobile phone already exists!";

	return $Result;
});

$EM->ThumbnailColumn("{$Entity}Picture");

$EM->BeforeInput(function($Entity, $Record){
	$_POST[$Field = "{$Entity}PasswordHash"] = strlen($_POST["{$Entity}Password"]) ? md5($_POST["{$Entity}Password"]) : (is_null($Record) ? null : $Record["{$Field}"]);

	return true;
});

$EM->IntermediateEntity("{$Entity}Group");
$EM->DefaultFromSearchColumn("GenderID, LanguageID");

$EM->ListColumn([
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Picture") . "ThumbnailURL", "{$Caption}", FIELD_TYPE_PICTURE),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Name") . "", "{$Caption}"),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Email") . "", "{$Caption}", FIELD_TYPE_EMAIL),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Phone") . "", "{$Caption}", FIELD_TYPE_PHONE),
	new HTML\UI\Datagrid\Column("{$Entity}Address" . ($Caption = "Country") . "FlagURL", "{$Caption}", FIELD_TYPE_ICON),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Group") . "Name", "{$Caption}"),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Last") . "ActiveTime", "{$Caption} activity on", FIELD_TYPE_DATETIME),
	new HTML\UI\Datagrid\Column("{$Entity}Is" . ($Caption = "Active") . "", "{$Caption}", FIELD_TYPE_BOOLEANICON),
]);

$EM->Action([
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}edit.png", null, $Application->URL($_POST["_Script"], "btnInput")),
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}delete.png", null, $Application->URL($_POST["_Script"], "btnDelete"), null, "return confirm('Are you sure to remove the information?');"),
]);

$EM->BatchActionHTML([
	HTML\UI\Button("<img src=\"{$Environment->IconURL()}search.png\" alt=\"Search\" class=\"Icon\">Search", BUTTON_TYPE_SUBMIT, "btnSearch", true),
	HTML\UI\Button("<img src=\"{$Environment->IconURL()}add.png\" alt=\"Add new\" class=\"Icon\">Add new", BUTTON_TYPE_SUBMIT, "btnInput", true),
	HTML\UI\Button("<img src=\"{$Environment->IconURL()}delete.png\" alt=\"Remove\" class=\"Icon\">Remove", BUTTON_TYPE_SUBMIT, "btnDelete", true, "return confirm('Are you sure to remove the information?');"),
	HTML\UI\Button("<img src=\"{$Environment->IconURL()}export.png\" alt=\"Export\" class=\"Icon\">Export", BUTTON_TYPE_SUBMIT, "btnExport", true),
	HTML\UI\Button("<img src=\"{$Environment->IconURL()}import.png\" alt=\"Import\" class=\"Icon\">Import", BUTTON_TYPE_SUBMIT, "btnImport", true),
]);

$EM->OrderBy("{$Entity}LastActiveTime");
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
		$EM->Input();
		$Terminal->Redirect($_POST["_Referer"]); // Redirect to previous location
	}

	$EM->LoadExistingData();

	#region Default value
	SetVariable($Field = "GenderID", SetVariable("{$EM->SearchInputPrefix()}{$Field}"));
	SetVariable($Field = "LanguageID", SetVariable("{$EM->SearchInputPrefix()}{$Field}"));
	#endregion Default value

	$EM->InputUIHTML([
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Email") . "", $EM->InputWidth(), null, true, INPUT_TYPE_EMAIL), "{$Caption}", null, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Password") . "", $EM->InputInlineWidth()), "{$Caption}", null, true, $EM->FieldCaptionInlineWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Name" . ($Caption = "First") . "", $EM->InputWidth(), null, true), "Name: {$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Name" . ($Caption = "Middle") . "", $EM->InputInlineWidth()), "{$Caption}", null, true, $EM->FieldCaptionInlineWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Name" . ($Caption = "Last") . "", $EM->InputWidth(), null, true), "Name: {$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Gender") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Phone" . ($Caption = "Mobile") . "", $EM->InputWidth(), null, null), "Phone: {$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Phone" . ($Caption = "Home") . "", $EM->InputInlineWidth()), "{$Caption}", null, true, $EM->FieldCaptionInlineWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Phone" . ($Caption = "Work") . "", $EM->InputWidth()), "Phone: {$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Phone" . ($Caption = "Other") . "", $EM->InputInlineWidth()), "{$Caption}", null, true, $EM->FieldCaptionInlineWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "URL") . "", $Configuration["InputFullWidth"]), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Picture") . "", $Configuration["InputFullWidth"], "{$Environment->UploadURL()}{$EM->LowercaseEntityName()}/" . (isset($_POST["{$Entity}{$Caption}"]) ? $_POST["{$Entity}{$Caption}"] : null) . "", null, INPUT_TYPE_FILE), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Address" . ($Caption = "Street") . "", $Configuration["InputFullWidth"]), "Address: {$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Address" . ($Caption = "City") . "", $EM->InputWidth()), "Address: {$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Address" . ($Caption = "State") . "", $EM->InputInlineWidth()), "{$Caption}", null, true, $EM->FieldCaptionInlineWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Address" . ($Caption = "ZIP") . "", $EM->InputWidth()), "Address: {$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Select("{$Entity}Address" . ($Caption = "Country") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", null, true, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Language") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\CheckboxGroup("User" . ($Caption = "Group") . "ID", $Table[$OptionEntity = "User{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption", null, "{$OptionEntity}ID"), "{$Caption}", true, null, $EM->FieldCaptionWidth(), $Configuration["FieldContentFullWidth"]),
		HTML\UI\Field(HTML\UI\RadioGroup("{$Entity}Is" . ($Caption = "Active") . "", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")]), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
	]);

	print $EM->InputHTML();
}

#region List
$EM->SearchSQL([
	// Custom fixed search condition
	"1 = 1 AND {$Table["{$Entity}"]->Alias()}.{$Entity}ID <> " . intval($Session->User()->ID()) . "",

	// Search interface specific condition
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Email") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Name") . "", SetVariable($Column)) ? "({$Table["{$Entity}"]->Alias()}.{$Column}First LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%' OR {$Table["{$Entity}"]->Alias()}.{$Column}Middle LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%' OR {$Table["{$Entity}"]->Alias()}.{$Column}Last LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%')" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "GenderID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Phone") . "", SetVariable($Column)) ? "({$Table["{$Entity}"]->Alias()}.{$Column}Mobile LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%' OR {$Table["{$Entity}"]->Alias()}.{$Column}Home LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%' OR {$Table["{$Entity}"]->Alias()}.{$Column}Work LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%' OR {$Table["{$Entity}"]->Alias()}.{$Column}Other LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%')" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Address") . "", SetVariable($Column)) ? "({$Table["{$Entity}"]->Alias()}.{$Column}Street LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%' OR {$Table["{$Entity}"]->Alias()}.{$Column}City LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%' OR {$Table["{$Entity}"]->Alias()}.{$Column}State LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%' OR {$Table["{$Entity}"]->Alias()}.{$Column}ZIP LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%' OR AC.CountryName LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%')" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}GroupID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Entity}ID IN (SELECT {$Table["{$Entity}"]->Alias()}UG.{$Entity}ID FROM {$Table["{$Entity}"]->Prefix()}{$LowercaseEntity}{$LowercaseEntity}group AS {$Table["{$Entity}"]->Alias()}UG WHERE {$Table["{$Entity}"]->Alias()}UG.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . ")" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}IsActive") . "", SetVariable($Column, "")) !== "" ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
]);

$EM->SearchUIHTML([
	HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Email") . "", 200), "{$Caption}", null, null),
	HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Name") . "", 200), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}" . ($Caption = "Gender") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Phone") . "", 200), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Address") . "", 200), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Group") . "ID", $Table[$OptionEntity = "{$Entity}{$Caption}"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}Is" . ($Caption = "Active") . "", [new Option(), new Option(0, "No"), new Option(1, "Yes")]), "{$Caption}", null, true),
]);

print $EM->ListHTML();
#region List
?>