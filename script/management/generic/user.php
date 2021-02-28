<?php
namespace sPHP;

#region Entity management common configuration
$EM = new EntityManagement($TBL[$Entity = "User"]);

$EM->ImportField([
	new Database\Field("{$Entity}" . ($Field = "SignInName") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Email") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "NameFirst") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "NameMiddle") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "NameLast") . "", "{$Field}"),
	new Database\Field("" . ($Field = "Gender") . "ID", "{$Field}", null, $TBL["{$Field}"], "{$Field}Name"),
	new Database\Field("{$Entity}" . ($Field = "PhoneMobile") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "PhoneHome") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "PhoneWork") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "PhoneOther") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "URL") . "", "{$Field}"),
	new Database\Field("{$Entity}Address" . ($Field = "Country") . "ID", "{$Field}", null, $TBL["{$Field}"], "{$Field}Name"),
	new Database\Field("" . ($Field = "Language") . "ID", "{$Field}", null, $TBL["{$Field}"], "{$Field}Name"),
	new Database\Field("{$Entity}Is" . ($Field = "Active") . "", "{$Field}"),
]);

$EM->InputValidation([
	new HTTP\InputValidation("{$Entity}SignInName", true),
	new HTTP\InputValidation("{$Entity}Email", true),
	new HTTP\InputValidation("{$Entity}NameFirst", true),
	new HTTP\InputValidation("{$Entity}NameLast", true),
	new HTTP\InputValidation("GenderID", true, VALIDATION_TYPE_INTEGER),
	//new HTTP\InputValidation("{$Entity}PhoneMobile", true),
	new HTTP\InputValidation("{$Entity}AddressCountryID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("LanguageID", true, VALIDATION_TYPE_INTEGER),
]);

$EM->ValidateInput(function($Entity, $DTB, $TBL, $PrimaryKey, $ID){
	$Result = true;

	if($TBL->Get( // Check for duplicate values for UNIQUE columns
		"
			(
					" . ($Column = "{$Entity}SignInName") . " = '{$DTB->Escape($_POST["{$Column}"])}'
				OR	" . ($Column = "{$Entity}Email") . " = '{$DTB->Escape($_POST["{$Column}"])}'
				/*OR	" . ($Column = "{$Entity}PhoneMobile") . " = '{$DTB->Escape($_POST["{$Column}"])}'*/
			)
			AND	{$PrimaryKey} <> {$ID}
		"
	, null, null, null, null, null, null))$Result = "Username, email or mobile phone already exists!";

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
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Sign") . "InName", "{$Caption} in"),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Email") . "", "{$Caption}", FIELD_TYPE_EMAIL),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Phone") . "", "{$Caption}", FIELD_TYPE_PHONE),
	//new HTML\UI\Datagrid\Column("{$Entity}Address" . ($Caption = "Country") . "FlagURL", "{$Caption}", FIELD_TYPE_ICON),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Group") . "Name", "{$Caption}"),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Last") . "ActiveTime", "{$Caption} activity on", FIELD_TYPE_DATETIME),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "S") . "ignUpIsActivated", "{$Caption}A", FIELD_TYPE_BOOLEANICON),
	new HTML\UI\Datagrid\Column("{$Entity}Is" . ($Caption = "Active") . "", "{$Caption}", FIELD_TYPE_BOOLEANICON),
]);

$EM->Action([
	new HTML\UI\Datagrid\Action("{$ENV->IconURL()}edit.png", null, $Application->URL($_POST["_Script"], "btnInput")),
	new HTML\UI\Datagrid\Action("{$ENV->IconURL()}delete.png", null, $Application->URL($_POST["_Script"], "btnDelete"), null, "return confirm('Are you sure to remove the information?');"),
]);

$EM->BatchActionHTML([
	HTML\UI\Button("<img src=\"{$ENV->IconURL()}search.png\" alt=\"Search\" class=\"Icon\">Search", BUTTON_TYPE_SUBMIT, "btnSearch", true),
	HTML\UI\Button("<img src=\"{$ENV->IconURL()}add.png\" alt=\"Add new\" class=\"Icon\">Add new", BUTTON_TYPE_SUBMIT, "btnInput", true),
	HTML\UI\Button("<img src=\"{$ENV->IconURL()}delete.png\" alt=\"Remove\" class=\"Icon\">Remove", BUTTON_TYPE_SUBMIT, "btnDelete", true, "return confirm('Are you sure to remove the information?');"),
	HTML\UI\Button("<img src=\"{$ENV->IconURL()}export.png\" alt=\"Export\" class=\"Icon\">Export", BUTTON_TYPE_SUBMIT, "btnExport", true),
	HTML\UI\Button("<img src=\"{$ENV->IconURL()}import.png\" alt=\"Import\" class=\"Icon\">Import", BUTTON_TYPE_SUBMIT, "btnImport", true),
]);

$EM->OrderBy("{$Entity}LastActiveTime");
$EM->Order("DESC");
$EM->URL($Application->URL($_POST["_Script"]));
$EM->IconURL($ENV->IconURL());
$EM->EncryptionKey($Application->EncryptionKey());
$EM->FieldCaptionWidth($CFG["FieldCaptionWidth"]);
$EM->FieldCaptionInlineWidth($CFG["FieldCaptionInlineWidth"]);
$EM->FieldContentFullWidth($CFG["FieldContentFullWidth"]);
$EM->InputWidth($CFG["InputWidth"]);
$EM->InputInlineWidth($CFG["InputInlineWidth"]);
$EM->InputFullWidth($CFG["InputFullWidth"]);
$EM->InputDateWidth($CFG["InputDateWidth"]);
$EM->TempPath($ENV->TempPath());
$EM->SearchInputPrefix($CFG["SearchInputPrefix"]);
$EM->UploadPath($ENV->UploadPath());
$EM->ThumbnailMaximumDimension(48);
$EM->RecordsPerPage($CFG["DatagridRowsPerPage"]);
$EM->BaseURL($ENV->URL()); // ???????????
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
		$_POST["{$Entity}SignUpIsActivated"] = 0;
		if($EM->Input())$Terminal->Redirect($_POST["_Referer"]); // Redirect to previous location
	}

	$EM->LoadExistingData();

	#region Default value
	SetVariable($Field = "GenderID", SetVariable("{$EM->SearchInputPrefix()}{$Field}"));
	SetVariable($Field = "LanguageID", SetVariable("{$EM->SearchInputPrefix()}{$Field}"));
	#endregion Default value

	$EM->InputUIHTML([
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Sign") . "InName", $EM->InputWidth(), null, true, null), "{$Caption} in name", null, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Email") . "", $EM->InputWidth(), null, true, INPUT_TYPE_EMAIL), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Password") . "", $EM->InputInlineWidth()), "{$Caption}", null, true, $EM->FieldCaptionInlineWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Name" . ($Caption = "First") . "", $EM->InputWidth(), null, true), "Name: {$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Name" . ($Caption = "Middle") . "", $EM->InputInlineWidth()), "{$Caption}", null, true, $EM->FieldCaptionInlineWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Name" . ($Caption = "Last") . "", $EM->InputWidth(), null, true), "Name: {$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Gender") . "ID", $TBL[$OptionEntity = "{$Caption}"]->Get("{$TBL["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Phone" . ($Caption = "Mobile") . "", $EM->InputWidth(), null, null), "Phone: {$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Phone" . ($Caption = "Home") . "", $EM->InputInlineWidth()), "{$Caption}", null, true, $EM->FieldCaptionInlineWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Phone" . ($Caption = "Work") . "", $EM->InputWidth()), "Phone: {$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Phone" . ($Caption = "Other") . "", $EM->InputInlineWidth()), "{$Caption}", null, true, $EM->FieldCaptionInlineWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "URL") . "", $CFG["InputFullWidth"]), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Picture") . "", $CFG["InputFullWidth"], "{$ENV->UploadURL()}{$EM->LowercaseEntityName()}/" . (isset($_POST["{$Entity}{$Caption}"]) ? $_POST["{$Entity}{$Caption}"] : null) . "", null, INPUT_TYPE_FILE), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Address" . ($Caption = "Street") . "", $CFG["InputFullWidth"]), "Address: {$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Address" . ($Caption = "City") . "", $EM->InputWidth()), "Address: {$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Address" . ($Caption = "State") . "", $EM->InputInlineWidth()), "{$Caption}", null, true, $EM->FieldCaptionInlineWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Address" . ($Caption = "ZIP") . "", $EM->InputWidth()), "Address: {$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Select("{$Entity}Address" . ($Caption = "Country") . "ID", $TBL[$OptionEntity = "{$Caption}"]->Get("{$TBL["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1", "{$OptionEntity}LookupCaption ASC"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", null, true, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Language") . "ID", $TBL[$OptionEntity = "{$Caption}"]->Get("{$TBL["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\CheckboxGroup("User" . ($Caption = "Group") . "ID", $TBL[$OptionEntity = "User{$Caption}"]->Get(null, "{$TBL["{$OptionEntity}"]->Alias()}.{$OptionEntity}Weight DESC"), null, "{$OptionEntity}LookupCaption", null, "{$OptionEntity}ID"), "{$Caption}", true, null, $EM->FieldCaptionWidth(), $CFG["FieldContentFullWidth"]),
		HTML\UI\Field(HTML\UI\RadioGroup("{$Entity}Is" . ($Caption = "Active") . "", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")]), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
	]);

	print $EM->InputHTML();
}

#region List
$EM->SearchSQL([
	// Custom fixed search condition
	"1 = 1 AND {$TBL["{$Entity}"]->Alias()}.{$Entity}ID <> " . intval($SSN->User()->ID()) . "",

	// Search interface specific condition
	SetVariable("{$CFG["SearchInputPrefix"]}" . ($Column = "{$Entity}SignInName") . "", SetVariable($Column)) ? "{$TBL["{$Entity}"]->Alias()}.{$Column} LIKE '%{$DTB->Escape($_POST["{$CFG["SearchInputPrefix"]}{$Column}"])}%'" : null,
	SetVariable("{$CFG["SearchInputPrefix"]}" . ($Column = "{$Entity}Email") . "", SetVariable($Column)) ? "{$TBL["{$Entity}"]->Alias()}.{$Column} LIKE '%{$DTB->Escape($_POST["{$CFG["SearchInputPrefix"]}{$Column}"])}%'" : null,
	SetVariable("{$CFG["SearchInputPrefix"]}" . ($Column = "{$Entity}Name") . "", SetVariable($Column)) ? "({$TBL["{$Entity}"]->Alias()}.{$Column}First LIKE '%{$DTB->Escape($_POST["{$CFG["SearchInputPrefix"]}{$Column}"])}%' OR {$TBL["{$Entity}"]->Alias()}.{$Column}Middle LIKE '%{$DTB->Escape($_POST["{$CFG["SearchInputPrefix"]}{$Column}"])}%' OR {$TBL["{$Entity}"]->Alias()}.{$Column}Last LIKE '%{$DTB->Escape($_POST["{$CFG["SearchInputPrefix"]}{$Column}"])}%')" : null,
	SetVariable("{$CFG["SearchInputPrefix"]}" . ($Column = "GenderID") . "", SetVariable($Column)) ? "{$TBL["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$CFG["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$CFG["SearchInputPrefix"]}" . ($Column = "{$Entity}Phone") . "", SetVariable($Column)) ? "({$TBL["{$Entity}"]->Alias()}.{$Column}Mobile LIKE '%{$DTB->Escape($_POST["{$CFG["SearchInputPrefix"]}{$Column}"])}%' OR {$TBL["{$Entity}"]->Alias()}.{$Column}Home LIKE '%{$DTB->Escape($_POST["{$CFG["SearchInputPrefix"]}{$Column}"])}%' OR {$TBL["{$Entity}"]->Alias()}.{$Column}Work LIKE '%{$DTB->Escape($_POST["{$CFG["SearchInputPrefix"]}{$Column}"])}%' OR {$TBL["{$Entity}"]->Alias()}.{$Column}Other LIKE '%{$DTB->Escape($_POST["{$CFG["SearchInputPrefix"]}{$Column}"])}%')" : null,
	SetVariable("{$CFG["SearchInputPrefix"]}" . ($Column = "{$Entity}Address") . "", SetVariable($Column)) ? "({$TBL["{$Entity}"]->Alias()}.{$Column}Street LIKE '%{$DTB->Escape($_POST["{$CFG["SearchInputPrefix"]}{$Column}"])}%' OR {$TBL["{$Entity}"]->Alias()}.{$Column}City LIKE '%{$DTB->Escape($_POST["{$CFG["SearchInputPrefix"]}{$Column}"])}%' OR {$TBL["{$Entity}"]->Alias()}.{$Column}State LIKE '%{$DTB->Escape($_POST["{$CFG["SearchInputPrefix"]}{$Column}"])}%' OR {$TBL["{$Entity}"]->Alias()}.{$Column}ZIP LIKE '%{$DTB->Escape($_POST["{$CFG["SearchInputPrefix"]}{$Column}"])}%' OR AC.CountryName LIKE '%{$DTB->Escape($_POST["{$CFG["SearchInputPrefix"]}{$Column}"])}%')" : null,
	SetVariable("{$CFG["SearchInputPrefix"]}" . ($Column = "{$Entity}GroupID") . "", SetVariable($Column)) ? "{$TBL["{$Entity}"]->Alias()}.{$Entity}ID IN (SELECT {$TBL["{$Entity}"]->Alias()}UG.{$Entity}ID FROM {$TBL["{$Entity}"]->Prefix()}userusergroup AS {$TBL["{$Entity}"]->Alias()}UG WHERE {$TBL["{$Entity}"]->Alias()}UG.{$Column} = " . intval($_POST["{$CFG["SearchInputPrefix"]}{$Column}"]) . ")" : null,
	SetVariable("{$CFG["SearchInputPrefix"]}" . ($Column = "{$Entity}SignUpIsActivated") . "", SetVariable($Column, "")) !== "" ? "{$TBL["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$CFG["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$CFG["SearchInputPrefix"]}" . ($Column = "{$Entity}IsActive") . "", SetVariable($Column, "")) !== "" ? "{$TBL["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$CFG["SearchInputPrefix"]}{$Column}"]) . "" : null,
]);

$EM->SearchUIHTML([
	HTML\UI\Field(HTML\UI\Input("{$CFG["SearchInputPrefix"]}{$Entity}" . ($Caption = "Sign") . "InName", 200), "{$Caption} in", null, null),
	HTML\UI\Field(HTML\UI\Input("{$CFG["SearchInputPrefix"]}{$Entity}" . ($Caption = "Email") . "", 200), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Input("{$CFG["SearchInputPrefix"]}{$Entity}" . ($Caption = "Name") . "", 200), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$CFG["SearchInputPrefix"]}" . ($Caption = "Gender") . "ID", $TBL[$OptionEntity = "{$Caption}"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Input("{$CFG["SearchInputPrefix"]}{$Entity}" . ($Caption = "Phone") . "", 200), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Input("{$CFG["SearchInputPrefix"]}{$Entity}" . ($Caption = "Address") . "", 200), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$CFG["SearchInputPrefix"]}{$Entity}" . ($Caption = "Group") . "ID", $TBL[$OptionEntity = "{$Entity}{$Caption}"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$CFG["SearchInputPrefix"]}{$Entity}" . ($Caption = "Sign") . "UpIsActivated", [new Option(), new Option(0, "No"), new Option(1, "Yes")]), "{$Caption} up activated", null, true),
	HTML\UI\Field(HTML\UI\Select("{$CFG["SearchInputPrefix"]}{$Entity}Is" . ($Caption = "Active") . "", [new Option(), new Option(0, "No"), new Option(1, "Yes")]), "{$Caption}", null, true),
]);

print $EM->ListHTML();
#region List
?>