<?php
namespace sPHP;

#region Entity management common configuration
$EM = new EntityManagement($TBL[$Entity = "DictionaryData"], $ENV);
$PassthroughURLArgument = $EM->SearchURLArgument([]); // For custom search argument, empty aray will generate standard URL argument

$EM->ImportField([
	new Database\Field("" . ($Field = "Dictionary") . "ID", "{$Field}", $TBL["{$Field}"], "{$Field}Name"),
	new Database\Field("" . ($Field = "Language") . "ID", "{$Field}", $TBL["{$Field}"], "{$Field}Name"),
	new Database\Field("{$Entity}" . ($Field = "Name") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "ValueText") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "ValueNumber") . "", "{$Field}"),
	new Database\Field("{$Entity}Is" . ($Field = "Active") . "", "{$Field}"),
]);

$EM->InputValidation([
	new HTTP\InputValidation("DictionaryID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("LanguageID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("{$Entity}Name", true, null),
	new HTTP\InputValidation("{$Entity}ValueText", null, null),
	new HTTP\InputValidation("{$Entity}ValueNumber", null, VALIDATION_TYPE_NUMBER),
	new HTTP\InputValidation("{$Entity}IsActive", true, VALIDATION_TYPE_INTEGER),
]);

$EM->ValidateInput(function($Entity, $DTB, $TBL, $PrimaryKey, $ID){
	#region Shortcut variables
	$DTB = \sPHP::$Database;
	$TBL = \sPHP::$Table[$Entity];
	$TableName = "{$TBL->Prefix()}{$TBL->Name()}";
	$TableAlias = $TBL->Alias();
	$PrimaryKey = $TBL->Structure()["Primary"][0];
	$CurrentEntityID = intval(SetVariable("{$PrimaryKey}"));
	#endregion Shortcut variables
	
	$Result = true;

	if($Result === true && $DTB->Query($SQL = "
		SELECT			COUNT(0) AS Count
		FROM			{$TableName} AS {$TableAlias}
		WHERE			{$TableAlias}." . ($Column = "{$PrimaryKey}") . " != {$CurrentEntityID}
			AND			{$TableAlias}." . ($Column = "DictionaryID") . " = " . intval($_POST["{$Column}"]) . "
			AND			{$TableAlias}." . ($Column = "LanguageID") . " = " . intval($_POST["{$Column}"]) . "
			AND			{$TableAlias}." . ($Column = "{$Entity}Name") . " = '{$DTB->Escape($_POST["{$Column}"])}'
	")[0][0]["Count"])$Result = "Duplicate name for same type"; //DebugDump($SQL);

	return $Result;
});

$EM->ThumbnailColumn("x{$Entity}Picture, x{$Entity}Picture2");

$EM->BeforeInput(function($Entity, $Record){
	//$_POST[$Field = "{$Entity}PasswordHash"] = strlen($_POST["{$Entity}Password"]) ? md5($_POST["{$Entity}Password"]) : (is_null($Record) ? null : $Record["{$Field}"]);

	return true;
});

$EM->IntermediateEntity("x{$Entity}Service, xEvent");
$EM->DefaultFromSearchColumn("{$Entity}Name, DictionaryID, LanguageID");

$EM->ListColumn([
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Name") . "", "{$Caption}", null, null, null, null, null, null, null, null, null, null, null, "<!--<img src=\"{$ENV->IconURL()}copy.png\" alt=\"Copy\" class=\"Icon\" onclick=\"sJS.Clipboard.Copy(document.getElementById('%{$Entity}Name%[%{$Entity}ID%]').innerHTML);\"> --><span id=\"%{$Entity}Name%[%{$Entity}ID%]\">%{$Entity}Name%</span>"),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Value") . "Preview", "{$Caption}", null, null, null, null, null, null, null, null, null, null, null, "<img src=\"{$ENV->IconURL()}dictionarydata-type-%{$Entity}Type%.png\" alt=\"%{$Entity}Type%\" title=\"%{$Entity}Type%\" class=\"Icon\"> %{$Entity}ValuePreview%"),
	new HTML\UI\Datagrid\Column("" . ($Caption = "Language") . "Name", "{$Caption}"),
	new HTML\UI\Datagrid\Column("" . ($Caption = "Dictionary") . "Name", "{$Caption}"),
	new HTML\UI\Datagrid\Column("Dictionary" . ($Caption = "Type") . "Name", "{$Caption}"),
	new HTML\UI\Datagrid\Column("{$Entity}Is" . ($Caption = "Active") . "", "{$Caption}", FIELD_TYPE_BOOLEANICON, null),
]);

$EM->Action([
	//new HTML\UI\Datagrid\Action("{$ENV->IconURL()}" . strtolower($OptionEntity = "{$Entity}" . ($Caption = "Point") . "") . ".png", null, $APP->URL("{$Entity}/Designer"), null, null, null, "{$Caption}", null, null),
	new HTML\UI\Datagrid\Action("{$ENV->IconURL()}edit.png", null, $APP->URL($_POST["_Script"], "btnInput"), null, null, null, "Edit", null, null),
	new HTML\UI\Datagrid\Action("{$ENV->IconURL()}delete.png", null, $APP->URL($_POST["_Script"], "btnDelete"), null, "return confirm('Are you sure to remove the Event?');", null, "Delete", null, null),
]);

$EM->BatchActionHTML([
	HTML\UI\Button("<img src=\"{$ENV->IconURL()}search.png\" alt=\"Search\" class=\"Icon\">Search", BUTTON_TYPE_SUBMIT, "btnSearch", true),
	HTML\UI\Button("<img src=\"{$ENV->IconURL()}add.png\" alt=\"Add new\" class=\"Icon\">Add new", BUTTON_TYPE_SUBMIT, "btnInput", true),
	HTML\UI\Button("<img src=\"{$ENV->IconURL()}delete.png\" alt=\"Remove\" class=\"Icon\">Remove", BUTTON_TYPE_SUBMIT, "btnDelete", true, "return confirm('Are you sure to remove the Event?');"),
	HTML\UI\Button("<img src=\"{$ENV->IconURL()}export.png\" alt=\"Export\" class=\"Icon\">Export", BUTTON_TYPE_SUBMIT, "btnExport", true),
	HTML\UI\Button("<img src=\"{$ENV->IconURL()}import.png\" alt=\"Import\" class=\"Icon\">Import", BUTTON_TYPE_SUBMIT, "btnImport", true),
	"
		<a href=\"{$APP->URL("Management/Generic/" . ($OptionEntity = "Dictionary") . "")}\" target=\"\" class=\"LinkButton\"><img src=\"{$ENV->IconURL()}" . strtolower($OptionEntity) . ".png\" alt=\"" . ($Caption = "{$TBL["{$OptionEntity}"]->FormalName()}") . "\" class=\"Icon\">{$Caption}</a>
		<a href=\"{$APP->URL("Management/Generic/" . ($OptionEntity = "Language") . "")}\" target=\"\" class=\"LinkButton\"><img src=\"{$ENV->IconURL()}" . strtolower($OptionEntity) . ".png\" alt=\"" . ($Caption = "{$TBL["{$OptionEntity}"]->FormalName()}") . "\" class=\"Icon\">{$Caption}</a>

		<!--
		<div class=\"ReportMenu\">
			<div class=\"Caption LinkButton\"><img src=\"{$ENV->IconURL()}report.png\" alt=\"Report\" class=\"Icon\">Report</div>
			<div class=\"Pad\">
				<a href=\"{$APP->URL("{$Entity}/Dashboard", "{$PassthroughURLArgument}")}\" target=\"_blank\" class=\"Item LinkButton\"><img src=\"{$ENV->IconURL()}report_dashboard.png\" alt=\"Dashboard\" class=\"Icon\">Dashboard</a>
				<a href=\"{$APP->URL("{$Entity}/Report", "{$PassthroughURLArgument}")}\" target=\"_blank\" class=\"Item LinkButton\"><img src=\"{$ENV->IconURL()}report_barge.png\" alt=\"Barge\" class=\"Icon\">Carrier report</a>
			<div>
		</div>
		-->
	", 
]);

// Strange!!! TMV seems to dislike '?IS+NULL' in the URL!
$EM->OrderBy("DictionaryTypeName ASC, DictionaryName ASC, LanguageName ASC, {$Entity}Name"); // {$Entity}ParentLookupCaption ASC, {$Entity}Name ASC, {$Entity}Order
$EM->Order("ASC");
$EM->URL($APP->URL($_POST["_Script"]));
$EM->IconURL($ENV->IconURL());
$EM->EncryptionKey($APP->EncryptionKey());
$EM->FieldCaptionWidth($CFG["FieldCaptionWidth"]);
$EM->FieldCaptionInlineWidth($CFG["FieldCaptionInlineWidth"]);
$EM->FieldContentFullWidth($CFG["FieldContentFullWidth"]);
$EM->InputWidth($CFG["InputWidth"]);
$EM->InputInlineWidth($CFG["InputInlineWidth"]);
$EM->InputFullWidth($CFG["InputFullWidth"]);
$EM->InputDateWidth($CFG["InputDateWidth"]);
$EM->InputTimeWidth($CFG["InputTimeWidth"]);
$EM->TempPath($ENV->TempPath());
$EM->SearchInputPrefix($CFG["SearchInputPrefix"]);
$EM->UploadPath($ENV->UploadPath());
$EM->ThumbnailMaximumDimension(48);
$EM->RecordsPerPage($CFG["DatagridRowsPerPage"]);
$EM->BaseURL($ENV->UploadURL()); // URL to prefix for uploaded resources????????
//$EM->DatagridCSSSelector("ReportTable");
//$EM->ListTitle("{$TBL[$Entity]->FormalName()}");
//$EM->ListRowExpandURL($APP->URL("Report/Terminal/Location_Current", "btnSubmit&_NoHeader&_NoFooter&NoSearch"));
#endregion Entity management common configuration

if(false && isset($_POST["btnImport"])){ // Prevent action
	if(isset($_POST["btnSubmit"])){
		$EM->Import();
		$TRM->Redirect($_POST["_Referer"]);
	}

	print $EM->ImportHTML();
}

if(isset($_POST["btnDelete"])){ // Prevent action
	$EM->Delete($SQL = "
			TRUE
		/*
		AND	" . ($USR->UserGroupIdentifierHighest() == "CUSTOMER" ? "RouteID IN (SELECT R.RouteID FROM pnl_route AS R LEFT JOIN pnl_customeruser AS CU ON CU.CustomerID = R.CustomerID WHERE CU.UserID = {$USR->ID()})" : "TRUE") . " # Customer
		AND	" . ($USR->UserGroupIdentifierHighest() == "CUSTOMER_MANAGER" ? "RouteID IN (SELECT R.RouteID FROM pnl_route AS R LEFT JOIN pnl_customermanager AS CM ON CM.CustomerID = R.CustomerID WHERE CM.UserID = {$USR->ID()})" : "TRUE") . " # Customer manager
		*/
	"); //DebugDump($SQL);

	$TRM->Redirect($_SERVER["HTTP_REFERER"]);
}

if(isset($_POST["btnInput"])){
	if(isset($_POST["btnSubmit"])){
		#region Custom code
		if($EM->EntityID()){ // Update

		}
		else{ // Add new

		}

		// Set defaults for values without input (checkbox)
		//foreach(explode(",", str_replace(" ", "", "x{$Entity}BeginFlagOriginEntry, x{$Entity}BeginFlagLoadBegin")) as $Field)SetVariable($Field, 0);
		#endregion Custom code
		
		if($AffectedRecord = $EM->Input()){ 
			#region Custom code
			#endregion Custom code

			$TRM->Redirect("{$_POST["_Referer"]}&SucceededAction=Input"); // Redirect to previous location
		}
	}

	$EM->LoadExistingData();

	#region Custom code
	if($EM->EntityID()){ // Update

	}
	else{ // Add new
		
	}
	#endregion Custom code

	$EM->InputUIHTML([
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Name") . "", $EM->InputFullWidth()), "{$Caption}", null, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Textarea("{$Entity}Value" . ($Caption = "Text") . "", $EM->InputFullWidth(), $CFG["TextareaHeight"]), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}Value" . ($Caption = "Number") . "", $EM->InputWidth(), null, null, INPUT_TYPE_NUMBER, null, null, null, null, 0.00001), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Dictionary") . "ID", $TBL[$OptionEntity = "{$Caption}"]->Get("{$TBL[$OptionEntity]->Alias()}.{$OptionEntity}IsActive = 1", "" . ($CaptionColumn = "{$OptionEntity}LookupCaption") . " ASC"), null, "{$CaptionColumn}"), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Language") . "ID", $TBL[$OptionEntity = "{$Caption}"]->Get("{$TBL[$OptionEntity]->Alias()}.{$OptionEntity}IsActive = 1", "" . ($CaptionColumn = "{$OptionEntity}LookupCaption") . " ASC"), null, "{$CaptionColumn}", null, null, $TBL[$OptionEntity]->Get("{$TBL[$OptionEntity]->Alias()}.{$OptionEntity}Name = 'English (United States)'")[0]["{$OptionEntity}ID"]), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\RadioGroup("{$Entity}Is" . ($Caption = "Active") . "", [new HTML\UI\Radio(1, "Yes", null, null, null, "{$Entity}IsActive"), new HTML\UI\Radio(0, "No")]), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
	]);

	print $EM->InputHTML();
}

#region List
$EM->SearchSQL($SQL = [
	"TRUE", // Custom fixed search condition

	// Fixed condition
	//$USR->UserGroupIdentifierHighest() == "CUSTOMER" ? "R.CustomerID = (SELECT CU.CustomerID FROM pnl_customeruser AS CU WHERE CU.UserID = {$USR->ID()})" : null, 

	// Numeric columns
	SetVariable("{$CFG["SearchInputPrefix"]}" . ($Column = "{$Entity}ID") . "", SetVariable($Column)) ? "{$TBL[$Entity]->Alias()}.{$Column} = " . intval($_POST["{$CFG["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$CFG["SearchInputPrefix"]}" . ($Column = "DictionaryTypeID") . "", SetVariable($Column)) ? "D.{$Column} = " . intval($_POST["{$CFG["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$CFG["SearchInputPrefix"]}" . ($Column = "DictionaryID") . "", SetVariable($Column)) && $_POST["{$CFG["SearchInputPrefix"]}DictionaryTypeID"] ? "{$TBL[$Entity]->Alias()}.{$Column} = " . intval($_POST["{$CFG["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$CFG["SearchInputPrefix"]}" . ($Column = "LanguageID") . "", SetVariable($Column)) ? "{$TBL[$Entity]->Alias()}.{$Column} = " . intval($_POST["{$CFG["SearchInputPrefix"]}{$Column}"]) . "" : null,
	strlen(SetVariable("{$CFG["SearchInputPrefix"]}" . ($Column = "{$Entity}IsActive") . "", SetVariable($Column))) ? "{$TBL["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$CFG["SearchInputPrefix"]}{$Column}"]) . "" : null,

	// Text columns
	SetVariable("{$CFG["SearchInputPrefix"]}" . ($Column = "{$Entity}Name") . "", SetVariable($Column)) ? "{$TBL[$Entity]->Alias()}.{$Column} LIKE '%{$DTB->Escape($_POST["{$CFG["SearchInputPrefix"]}{$Column}"])}%'" : null,

	// Not indexed columns; for optimization
	SetVariable("{$CFG["SearchInputPrefix"]}" . ($Column = "{$Entity}ValueText") . "", SetVariable($Column)) ? "{$TBL[$Entity]->Alias()}.{$Column} LIKE '%{$DTB->Escape($_POST["{$CFG["SearchInputPrefix"]}{$Column}"])}%'" : null,
]); //DebugDump($SQL);

$EM->SearchUIHTML([
	//HTML\UI\Field(HTML\UI\Input("{$CFG["SearchInputPrefix"]}{$Entity}" . ($Caption = "ID") . "", 100), "{$Caption}", null, null),
	HTML\UI\Field(HTML\UI\Input("{$CFG["SearchInputPrefix"]}{$Entity}" . ($Caption = "Name") . "", 150), "{$Caption}", null, null),
	HTML\UI\Field(HTML\UI\Input("{$CFG["SearchInputPrefix"]}{$Entity}Value" . ($Caption = "Text") . "", 150), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$CFG["SearchInputPrefix"]}Dictionary" . ($Caption = "Type") . "ID", $TBL[$OptionEntity = "Dictionary{$Caption}"]->Get("{$TBL[$OptionEntity]->Alias()}.{$OptionEntity}IsActive = 1", "" . ($CaptionColumn = "{$OptionEntity}LookupCaption") . " ASC"), new Option(), "{$CaptionColumn}", null, null, null, null, null, "{$CFG["SearchInputPrefix"]}DictionaryTypeID") . HTML\UI\Select("{$CFG["SearchInputPrefix"]}" . ($Caption = "Dictionary") . "ID", $TBL[$OptionEntity = "{$Caption}"]->Get("{$TBL[$OptionEntity]->Alias()}.{$OptionEntity}IsActive = 1 AND {$TBL[$OptionEntity]->Alias()}.DictionaryTypeID = " . intval($_POST["{$CFG["SearchInputPrefix"]}DictionaryTypeID"]) . "", "" . ($CaptionColumn = "{$OptionEntity}LookupCaption") . " ASC"), new Option(), "{$CaptionColumn}", null, null, null, null, null, "{$CFG["SearchInputPrefix"]}DictionaryID"), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$CFG["SearchInputPrefix"]}" . ($Caption = "Language") . "ID", $TBL[$OptionEntity = "{$Caption}"]->Get("{$TBL[$OptionEntity]->Alias()}.{$OptionEntity}IsActive = 1", "" . ($CaptionColumn = "{$OptionEntity}LookupCaption") . " ASC"), new Option(), "{$CaptionColumn}"), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$CFG["SearchInputPrefix"]}{$Entity}Is" . ($Caption = "Active") . "", [new Option(), new Option(0, "No"), new Option(1, "Yes")]), "{$Caption}", null, true),
]);

print "
	{$EM->ListHTML()}
	" . (SetVariable("SucceededAction") == "Input" ? "<script>sJS.DHTML.Toast('{$TBL["{$Entity}"]->FormalName()} input successful.');</script>" : null) . "
";
#region List

if(isset($_POST["btnExport"]))$TRM->LetDownload( // Let download the records as CSV
	$TBL[$Entity]->Get(implode(" AND ", array_filter($EM->SearchSQL())), "{$EM->OrderBy()} {$EM->Order()}"), // Recordset
	null, 
	"{$TBL["{$Entity}"]->FormalName()} " . date("Y-m-d H-i-s") . " " . rand(0, 9999) . ".csv" // File name
);
?>

<script>
	sJS.DHTML.Select.Child.Attach( // Create DitctionaryType > Dictionary dependant selection
		'<?="{$CFG["SearchInputPrefix"]}DictionaryTypeID"?>', // ParentObject
		'<?=$APP->URL("API/V1/AJAXEntity", "Entity=Dictionary&Column=DictionaryTypeID")?>&__Keyword=', // APIURL
		'<?="{$CFG["SearchInputPrefix"]}DictionaryID"?>', // ChildObject
		'DictionaryName', // CaptionKey
		'<?=$_POST["{$CFG["SearchInputPrefix"]}DictionaryID"]?>', // DefaultChildValue
		'DictionaryID' //ValueKey
	); 
</script>