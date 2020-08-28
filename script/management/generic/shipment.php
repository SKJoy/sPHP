<?php
namespace sPHP;

#region Entity management common configuration
$EM = new EntityManagement($Table[$Entity = "Shipment"]);

$EM->ImportField([
	new Database\Field("{$Entity}" . ($Field = "Name") . "", "{$Field}"),
	new Database\Field("" . ($Field = "Consignment") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("{$Entity}From" . ($Field = "Address") . "ID", "From{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("{$Entity}To" . ($Field = "Address") . "ID", "To{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("{$Entity}" . ($Field = "SendDate") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "ArrivalDate") . "", "{$Field}"),
	new Database\Field("{$Entity}Has" . ($Field = "Arrived") . "", "{$Field}"),
	new Database\Field("" . ($Field = "CargoHandler") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("Transport" . ($Field = "RouteType") . "ID", "{$Field}", null, $Table["Transport{$Field}"], "{$Field}Name"),
	new Database\Field("Signatory" . ($Field = "StaffPost") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("{$Entity}Is" . ($Field = "Active") . "", "{$Field}"),
]);

$EM->InputValidation([
	new HTTP\InputValidation("{$Entity}Name", true),
	new HTTP\InputValidation("ConsignmentID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("{$Entity}FromAddressID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("{$Entity}ToAddressID", true, null),
	new HTTP\InputValidation("{$Entity}SendDate", true, null),
	new HTTP\InputValidation("{$Entity}ArrivalDate", true, null),
	//new HTTP\InputValidation("{$Entity}HasArrived", true, VALIDATION_TYPE_INTEGER, "{$Entity} arrived (reached destination)"),
	new HTTP\InputValidation("CargoHandlerID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("TransportRouteTypeID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("SignatoryStaffPostID", true, VALIDATION_TYPE_INTEGER),
	//new HTTP\InputValidation("PaymentMethodID", null, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("BankAccountID", null, VALIDATION_TYPE_INTEGER),
]);

$EM->ValidateInput(function($Entity, $Database, $Table, $PrimaryKey, $ID){
	$Result = true;

	if($Table->Get( // Check for duplicate values for UNIQUE columns
		"
			(
					" . ($Column = "{$Entity}Name") . " = '{$Database->Escape($_POST["{$Column}"])}'
				AND	{$Table->Alias()}." . ($Column = "ConsignmentID") . " = " . intval($_POST["{$Column}"]) . "
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
$EM->DefaultFromSearchColumn("ConsignmentID, {$Entity}FromAddressID, {$Entity}ToAddressID, TransportRouteTypeID, CargoHandlerID");

$EM->ListColumn([
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Name") . "", "{$Caption}"),
	new HTML\UI\Datagrid\Column("" . ($Caption = "Consignment") . "LookupCaption", "{$Caption}", null, null),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "From") . "AddressLookupCaption", "{$Caption}", null, null),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "To") . "AddressLookupCaption", "{$Caption}", null, null),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Sen") . "dDate", "{$Caption}t", FIELD_TYPE_SHORTDATE),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Arrival") . "Date", "{$Caption}", FIELD_TYPE_SHORTDATE),
	new HTML\UI\Datagrid\Column("{$Entity}Has" . ($Caption = "Arrived") . "", "{$Caption}", FIELD_TYPE_BOOLEANICON),
	new HTML\UI\Datagrid\Column("Cargo" . ($Caption = "Handler") . "LookupCaption", "{$Caption}", null, null),
	new HTML\UI\Datagrid\Column("Transport" . ($Caption = "Route") . "TypeLookupCaption", "{$Caption}", null, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "P") . "roductCount", "{$Caption}", FIELD_TYPE_NUMBER, null),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Value") . "Caption", "{$Caption}", FIELD_TYPE_NUMBER, null),
	new HTML\UI\Datagrid\Column("{$Entity}Is" . ($Caption = "Active") . "", "{$Caption}", FIELD_TYPE_BOOLEANICON),
]);

$EM->Action([
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}{$Entity}" . strtolower($ActionEntity = "CommercialInvoice") . ".png", null, $Application->URL("{$Entity}/{$ActionEntity}"), "_blank", null, null, "Commercial invoice"),
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}{$Entity}" . strtolower($ActionEntity = "PackingList") . ".png", null, $Application->URL("{$Entity}/{$ActionEntity}"), "_blank", null, null, "Packing list"),
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}{$Entity}" . strtolower($ActionEntity = "ProformaInvoice") . ".png", null, $Application->URL("{$Entity}/{$ActionEntity}"), "_blank", null, null, "Proforma invoice"),
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}" . strtolower($ActionEntity = "{$Entity}ConsignmentExportProductCarton") . ".png", null, $Application->URL("Management/Generic/{$ActionEntity}"), null, null, null, "Carton"),
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}" . strtolower($ActionEntity = "{$Entity}Payment") . ".png", null, $Application->URL("Management/Generic/{$ActionEntity}"), null, null, null, "Payment"),
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}" . strtolower($ActionEntity = "{$Entity}ConsignmentExportProduct") . ".png", null, $Application->URL("Management/Generic/{$ActionEntity}"), null, null, null, "Product"),
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

$EM->OrderBy("{$Entity}IsActive DESC, {$Entity}HasArrived ASC, ISNULL({$Entity}ArrivalDate) DESC, {$Entity}SendDate ASC, ConsignmentLookupCaption ASC, {$Entity}Name");
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

if(isset($_POST["btnConsignment"])){
	if(isset($_POST["btnSubmit"])){
		$EM->Consignment();
		$Terminal->Redirect($_POST["_Referer"]);
	}

	print $EM->ConsignmentHTML();
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
	$SignatoryStaffPostInput = new HTML\UI\Input("SignatoryStaffPost", $EM->InputFullWidth(), null, null);
	$ReferencePrefix = "PH/EXP/" . (($Consignment = $Table[$OptionEntity = "Consignment"]->Get("{$OptionEntity}ID = " . intval(SetVariable("{$OptionEntity}ID", 0)) . "")) ? "{$Consignment[0]["ExportAddressCountryISOCode2"]}/" : null);
	#endregion Custom code

	$EM->InputUIHTML([
		HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Consignment") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", null, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Select("{$Entity}" . ($Caption = "From") . "AddressID", $Table[$OptionEntity = "Address"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Select("{$Entity}" . ($Caption = "To") . "AddressID", $Table[$OptionEntity = "Address"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", null, true, null),
		HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Cargo") . "HandlerID", $Table[$OptionEntity = "{$Caption}Handler"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption} handler", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Select("Transport" . ($Caption = "Route") . "TypeID", $Table[$OptionEntity = "Transport{$Caption}Type"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption} type", null, true),
		//HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Payment") . "MethodID", $Table[$OptionEntity = "{$Caption}Method"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption", null, null, 3), "{$Caption} method", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Bank") . "AccountID", $Table[$OptionEntity = "{$Caption}Account"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption} A/C", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Payment") . "Term", $EM->InputFullWidth(), null, null), "{$Caption} term", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field($SignatoryStaffPostInput->HTML(), "Signatory staff", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Name") . "", $EM->InputFullWidth(), $ReferencePrefix, true), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}ReferenceInvoice" . ($Caption = "Proforma") . "", $EM->InputWidth(), "{$ReferencePrefix}PROF/" . date("m") . "/" . date("y") . "", true), "Invoice ref: {$Caption}", true, null, $EM->FieldCaptionInlineWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}ReferenceInvoice" . ($Caption = "Commercial") . "", $EM->InputInlineWidth(), "{$ReferencePrefix}INV/" . date("m") . "/" . date("y") . "", true), "{$Caption}", null, true, $EM->FieldCaptionInlineWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Send") . "Date", $EM->InputDateWidth(), null, true, INPUT_TYPE_DATE), "Date: {$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Arrival") . "Date", $EM->InputDateWidth(), null, null, INPUT_TYPE_DATE), "{$Caption}", null, true, null),
		HTML\UI\Field(HTML\UI\RadioGroup("{$Entity}Has" . ($Caption = "Arrived") . "", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")], 0), "{$Caption}", null, true, null),
		HTML\UI\Field(HTML\UI\RadioGroup("{$Entity}Is" . ($Caption = "Active") . "", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")]), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		"
			<script>
				sJS.DHTML.Autofill.Make('{$SignatoryStaffPostInput->ID()}', 3, '{$Application->URL("API/V1/AJAXEntity", "Entity=" . ($OptionEntity = "StaffPost") . "&Column=S.StaffCode,S.StaffEmail,S.StaffPhone,E.EnterpriseName,E.EnterpriseShortName,O.OrganizationName,O.OrganizationShortName,D.DepartmentName,D.DepartmentShortName,DS.DesignationName,DS.DesignationShortName,U.UserEmail,U.UserNameFirst,U.UserNameMiddle,U.UserNameLast,U.UserPhoneMobile,U.UserPhoneHome,U.UserPhoneWork,U.UserPhoneOther")}', 'Signatory{$OptionEntity}ID', null, '" . ($CaptionKey = "{$OptionEntity}LookupCaption") . "', null, '" . (($OptionEntityID = isset($_POST["Signatory{$OptionEntity}ID"]) ? $_POST["Signatory{$OptionEntity}ID"] : null) ? $Table["{$OptionEntity}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}ID = {$_POST["Signatory{$OptionEntity}ID"]}")[0]["{$CaptionKey}"] : null) . "', {$OptionEntityID});
			</script>
		",
	]);

	print $EM->InputHTML();
}

#region List
$EM->SearchSQL([
	"1 = 1", // Custom fixed search condition
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Name") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "ConsignmentID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}FromAddressID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}ToAddressID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}HasArrived") . "", SetVariable($Column, "")) !== "" ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "CargoHandlerID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "TransportRouteTypeID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "SignatoryStaffPostID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}IsActive") . "", SetVariable($Column, "")) !== "" ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
]);

$EM->SearchUIHTML([
	HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Name") . "", 200), "{$Caption}", null, null),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}" . ($Caption = "Consignment") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "From") . "AddressID", $Table[$OptionEntity = "Address"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "To") . "AddressID", $Table[$OptionEntity = "Address"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}Has" . ($Caption = "Arrived") . "", [new Option(), new Option(0, "No"), new Option(1, "Yes")]), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}" . ($Caption = "Cargo") . "HandlerID", $Table[$OptionEntity = "{$Caption}Handler"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption} handler", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}Transport" . ($Caption = "Route") . "TypeID", $Table[$OptionEntity = "Transport{$Caption}Type"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption} type", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}Signatory" . ($Caption = "Staff") . "Post", $Table[$OptionEntity = "{$Caption}Post"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption} post", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}Is" . ($Caption = "Active") . "", [new Option(), new Option(0, "No"), new Option(1, "Yes")]), "{$Caption}", null, true),
]);

print "
	<style>
		body > main > .Datagrid > .Content > .Grid > tbody > tr > .Action{width: 139px; white-space: initial;}
	</style>

	{$EM->ListHTML()}
";
#region List
?>