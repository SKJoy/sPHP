<?php
namespace sPHP;

#region Entity management common configuration
$EM = new EntityManagement($Table[$Entity = "Export"], $Session->DebugMode());

$EM->ImportField([
	new Database\Field("{$Entity}" . ($Field = "Name") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "InitiationDate") . "", "{$Field}"),
	new Database\Field("" . ($Field = "Exporter") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("" . ($Field = "Client") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("" . ($Field = "Currency") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("{$Entity}" . ($Field = "HomeCurrency") . "ID", "{$Field}", null, $Table["Currency"], "{$Field}Name"),
	new Database\Field("{$Entity}HomeCurrency" . ($Field = "ConversionRate") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "AddressCountry") . "ID", "{$Field}", null, $Table["Country"], "{$Field}Name"),
	new Database\Field("Signatory" . ($Field = "StaffPost") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
	new Database\Field("{$Entity}Is" . ($Field = "Active") . "", "{$Field}"),
]);

$EM->InputValidation([
	new HTTP\InputValidation("{$Entity}Name", true),
	new HTTP\InputValidation("{$Entity}InitiationDate", true),
	new HTTP\InputValidation("ExporterID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("ClientID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("CurrencyID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("{$Entity}HomeCurrencyID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("{$Entity}HomeCurrencyConversionRate", true, VALIDATION_TYPE_NUMBER),
	new HTTP\InputValidation("{$Entity}AddressCountryID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("SignatoryStaffPostID", true, VALIDATION_TYPE_INTEGER),
]);

$EM->ValidateInput(function($Entity, $Database, $Table, $PrimaryKey, $ID){
	$Result = true;

	if($Table->Get( // Check for duplicate values for UNIQUE columns
		"
			(
					" . ($Column = "{$Entity}Name") . " = '{$Database->Escape($_POST["{$Column}"])}'
				AND	" . ($Column = "{$Entity}InitiationDate") . " = '{$Database->Escape($_POST["{$Column}"])}'
				AND	{$Table->Alias()}." . ($Column = "ClientID") . " = " . intval($_POST["{$Column}"]) . "
				AND	{$Table->Alias()}." . ($Column = "{$Entity}AddressCountryID") . " = " . intval($_POST["{$Column}"]) . "
			)
			AND	{$PrimaryKey} <> {$ID}
		"
	, null, null, null, null, null, null))$Result = "Same export on same initiation date with same client to same country exists!";

	return $Result;
});

$EM->ThumbnailColumn("x{$Entity}Picture");

$EM->BeforeInput(function($Entity, $Record){
	//$_POST[$Field = "{$Entity}PasswordHash"] = strlen($_POST["{$Entity}Password"]) ? md5($_POST["{$Entity}Password"]) : (is_null($Record) ? null : $Record["{$Field}"]);

	return true;
});

$EM->AfterInput(function($Entity, $AffectedRecord, $Record, $Table){
	$Result = false;

	if(isset($_POST["ProductID"])){ // Product input found
		$OptionEntity = "Product";
		$RelationEntity = "{$Entity}{$OptionEntity}";

		// Set up a database table for ExportProduct entity
		$ExportProductTable = new Database\Table("{$Entity} product");
		$ExportProductTable->Database($Table->Database());
		$ExportProductTable->SQLSELECTPath($Table->SQLSELECTPath());

		$NewExportProductCount = 0; // Counter for how many products inserted

		foreach($_POST["{$OptionEntity}ID"] as $Key=>$ProductID){
			// Validate input
			$_POST[$Field = "{$OptionEntity}ID"][$Key] = intval($_POST["{$Field}"][$Key]);
			$_POST[$Field = "PackID"][$Key] = intval($_POST["{$Field}"][$Key]);
			$_POST[$Field = "{$RelationEntity}PackCount"][$Key] = intval($_POST["{$Field}"][$Key]);
			$_POST[$Field = "{$RelationEntity}Rate"][$Key] = floatval($_POST["{$Field}"][$Key]);

			// Insert products with particulars
			if($_POST["{$OptionEntity}ID"][$Key] && $_POST["PackID"][$Key] && $_POST["{$RelationEntity}PackCount"][$Key]){
				$ExportProductTable->Put([
					($Field = "{$Entity}ID")=>$AffectedRecord["{$Field}"],
					($Field = "{$OptionEntity}ID")=>$_POST["{$Field}"][$Key],
					($Field = "PackID")=>$_POST["{$Field}"][$Key],
					($Field = "{$RelationEntity}PackCount")=>$_POST["{$Field}"][$Key],
					($Field = "{$RelationEntity}Rate")=>$_POST["{$Field}"][$Key],
					($Field = "{$RelationEntity}IsActive")=>1,
				]);

				$NewExportProductCount = $NewExportProductCount + 1; // Raise new product counter
			}
		}

		if(count($NewExportProductCount))$Result = true; // At least one new product is inserted
	}

	return $Result;
});

$EM->IntermediateEntity("x{$Entity}Category, x{$Entity}Event");
$EM->DefaultFromSearchColumn("ClientID, CurrencyID, {$Entity}HomeCurrencyID, {$Entity}AddressCountryID");

$EM->ListColumn([
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Name") . "", "{$Caption}"),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Initiation") . "Date", "{$Caption}", FIELD_TYPE_SHORTDATE),
	new HTML\UI\Datagrid\Column("" . ($Caption = "Exporter") . "LookupCaption", "{$Caption}"),
	new HTML\UI\Datagrid\Column("" . ($Caption = "Client") . "LookupCaption", "{$Caption}"),
	new HTML\UI\Datagrid\Column("{$Entity}Address" . ($Caption = "Country") . "Name", "{$Caption}", null, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "C") . "onsignmentCount", "{$Caption}", FIELD_TYPE_NUMBER),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "S") . "hipmentCount", "{$Caption}", FIELD_TYPE_NUMBER),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "P") . "roductCount", "{$Caption}", FIELD_TYPE_NUMBER),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Value") . "Caption", "{$Caption}", FIELD_TYPE_NUMBER),
	new HTML\UI\Datagrid\Column("{$Entity}HomeCurrency" . ($Caption = "") . "ConversionRate", "{$Caption}HCCR", FIELD_TYPE_NUMBER),
	new HTML\UI\Datagrid\Column("{$Entity}HomeCurrency" . ($Caption = "Value") . "Caption", "HC {$Caption}", FIELD_TYPE_NUMBER),
	new HTML\UI\Datagrid\Column("{$Entity}Is" . ($Caption = "Active") . "", "{$Caption}", FIELD_TYPE_BOOLEANICON),
]);

$EM->Action([
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}" . strtolower($ActionEntity = "{$Entity}Payment") . ".png", null, $Application->URL("Management/Generic/{$ActionEntity}"), null, null, null, "Payment"),
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}" . strtolower($ActionEntity = "Consignment") . ".png", null, $Application->URL("Management/Generic/{$ActionEntity}"), null, null, null, "{$ActionEntity}"),
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}" . strtolower($ActionEntity = "{$Entity}Product") . ".png", null, $Application->URL("Management/Generic/{$ActionEntity}"), null, null, null, "Product"),
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

$EM->OrderBy("{$Entity}InitiationDate");
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
	#region Explicitely set default input values (because the search inputs already sets the values!)
	$_POST["" . ($OptionEntity = "Currency") . "ID"] = $Table["{$OptionEntity}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}Code = 'USD'")[0]["{$OptionEntity}ID"];
	#endregion Explicitely set default input values (because the search inputs already sets the values!)

	$SignatoryStaffPostInput = new HTML\UI\Input("SignatoryStaffPost", $EM->InputFullWidth(), null, null);
	#endregion Custom code

	$EM->InputUIHTML([
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Name") . "", $EM->InputWidth(), null, true), "{$Caption}", null, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Initiation") . "Date", $EM->InputDateWidth(), date("Y-m-d"), true, INPUT_TYPE_DATE), "{$Caption} date", null, true, null),
		HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Client") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Select("{$Entity}AddressCountry" . ($Caption = "") . "ID", $Table[$OptionEntity = "Country{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1", "{$OptionEntity}LookupCaption ASC"), null, "{$OptionEntity}LookupCaption"), "Destination{$Caption}", null, true, null),
		HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Currency") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption", null, null, $Table["{$OptionEntity}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}Code = 'USD'")[0]["{$OptionEntity}ID"]), "{$Caption}: Payment", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field(HTML\UI\Select("{$Entity}" . ($Caption = "Home") . "CurrencyID", $Table[$OptionEntity = "Currency"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", null, true, null),
		HTML\UI\Field(HTML\UI\Input("{$Entity}HomeCurrency" . ($Caption = "Conversion") . "Rate", 100, 1, true), "{$Caption} rate", null, true, null),
		HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Exporter") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
		HTML\UI\Field($SignatoryStaffPostInput->HTML(), "Signatory staff", true, null, $EM->FieldCaptionWidth()),

		HTML\UI\Field("
			<style>
				#ProductTable > thead{background-color: #006bc7; text-align: center;}
				#ProductTable > tbody > tr > td:first-child{text-align: right;}
				#ProductTable .ActionButton{background-color: #0089ff;}
			</style>

			<table id=\"ProductTable\">
				<thead>
					<tr>
						<td>#</td>
						<td>Product</td>
						<td>Pack</td>
						<td>Count</td>
						<td>Rate</td>
						<td><button type=\"button\" onclick=\"Add{$Entity}ProductInputRow();\" class=\"ActionButton\">+</button></td>
					</tr>
				</thead>

				<tbody id=\"ProductTableBody\"></tbody>
			</table>
		", "Product", true, null, $EM->FieldCaptionWidth(), null, null, null, "This will create new product entries besides the existing."),

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
	"ER.DepartmentID IN (SELECT SP.DepartmentID FROM sphp_staffpost AS SP WHERE SP.StaffID IN (SELECT S.StaffID FROM sphp_staff AS S WHERE S.UserID = {$User->ID()}))",
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Name") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "ExporterID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "ClientID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "CurrencyID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}HomeCurrencyID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}AddressCountryID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "SignatoryStaffPostID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}IsActive") . "", SetVariable($Column, "")) !== "" ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
]);

$EM->SearchUIHTML([
	HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Name") . "", 200), "{$Caption}", null, null),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}" . ($Caption = "Exporter") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}" . ($Caption = "Client") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}" . ($Caption = "Currency") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Home") . "CurrencyID", $Table[$OptionEntity = "Currency"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption} currency", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}Address" . ($Caption = "Country") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}Signatory" . ($Caption = "Staff") . "Post", $Table[$OptionEntity = "{$Caption}Post"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption} post", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}Is" . ($Caption = "Active") . "", [new Option(), new Option(0, "No"), new Option(1, "Yes")]), "{$Caption}", null, true),
]);

print $EM->ListHTML();
#region List

#region Custom code
foreach($Table["Pack"]->Get("{$Table["Pack"]->Alias()}.PackIsActive = 1", "PackName ASC") as $Pack)$PackOptionHTML[] = "<option value=\"{$Pack["PackID"]}\">{$Pack["PackLookupCaption"]}</option>";
#endregion Custom code
?>

<script>
	function Add<?=$Entity?>ProductInputRow(){
		elmProductTableBody = document.getElementById('ProductTableBody');
		ItemChildCounter = elmProductTableBody.childElementCount + 1;

		elmTDSerial = document.createElement('td');
		elmTDSerial.innerHTML = ItemChildCounter;

		elmTDProduct = document.createElement('td');
		elmProduct = document.createElement('input');
		elmProduct.id = 'Product_' + ItemChildCounter;
		elmProduct.type = 'text';
		elmProduct.name = 'Product[]';
		elmProduct.style.width = '439px';
		elmTDProduct.appendChild(elmProduct);

		elmTDPack = document.createElement('td');
		elmPack = document.createElement('select');
		elmPack.name = 'PackID[]';
		elmPack.innerHTML = '<?=implode(null, str_replace("'", "\\'", $PackOptionHTML))?>';
		elmTDPack.appendChild(elmPack);

		elmTDQuantity = document.createElement('td');
		elmQuantity = document.createElement('input');
		elmQuantity.type = 'number';
		elmQuantity.name = '<?=$Entity?>ProductPackCount[]';
		elmQuantity.value = 1;
		elmQuantity.min = 1;
		elmQuantity.style.width = '100px';
		elmTDQuantity.appendChild(elmQuantity);

		elmTDRate = document.createElement('td');
		elmRate = document.createElement('input');
		elmRate.type = 'number';
		elmRate.name = '<?=$Entity?>ProductRate[]';
		elmRate.value = 0.01;
		elmRate.min = 0.01;
		elmRate.step = 0.01;
		elmRate.style.width = '100px';
		elmTDRate.appendChild(elmRate);

		elmTDAction = document.createElement('td');
		elmTDActionButton = document.createElement('button');
		elmTDActionButton.type = 'button';
		elmTDActionButton.className = 'ActionButton';
		elmTDActionButton.innerHTML = 'X';

		elmTDActionButton.onclick = function(){
			this.parentElement.parentElement.parentElement.removeChild(this.parentElement.parentElement);

			// Recreate the serial

			return true;
		}

		elmTDAction.appendChild(elmTDActionButton);

		elmTR = document.createElement('tr');
		elmTR.appendChild(elmTDSerial);
		elmTR.appendChild(elmTDProduct);
		elmTR.appendChild(elmTDPack);
		elmTR.appendChild(elmTDQuantity);
		elmTR.appendChild(elmTDRate);
		elmTR.appendChild(elmTDAction);

		elmProductTableBody.appendChild(elmTR);
		sJS.DHTML.Autofill.Make(elmProduct.id, 3, '<?=$Application->URL("API/V1/AJAXEntity", "Entity=Product&Column=ProductName,ProductGenericName")?>', 'ProductID[]', null, 'ProductLookupCaption', null, null, null);

		return true;
	}
</script>