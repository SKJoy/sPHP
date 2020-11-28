<?php
namespace sPHP;

$Entity = "ProductSubcategory";
$LowercaseEntity = strtolower($Entity);
$EntityName = $Table[$Entity]->FormalName();
$LowercaseEntityName = strtolower($Table[$Entity]->FormalName());
$EntityID = isset($_POST["{$Entity}ID"]) && (($_POST["{$Entity}ID"] = is_array($_POST["{$Entity}ID"]) ? $_POST["{$Entity}ID"] : intval($_POST["{$Entity}ID"])) || is_array($_POST["{$Entity}ID"])) ? $_POST["{$Entity}ID"] : 0;

if(isset($_POST["btnExport"])){
	if(isset($_POST["btnSubmit"])){
		foreach($_POST as $ColumnKey=>$ThisColumn)if(substr($ColumnKey, 0, strlen($Marker = "Column_")) == $Marker)$Column[] = substr($ColumnKey, strlen("Column_"));
		$Table["{$Entity}"]->Export($Column, null, $_POST["Format"], "{$Environment->TempPath()}Export_{$Entity}.csv", $_POST["{$Entity}IDList"] ? "OP.{$Entity}ID IN ({$_POST["{$Entity}IDList"]})" : null, "{$_POST["OrderBy"]} {$_POST["Order"]}");
		$Terminal->Redirect($_POST["RedirectionURL"]);
	}

	$EntityIDList = is_array($EntityID) ? implode(", ", $EntityID) : null;
	foreach(array_keys($Table["{$Entity}"]->Get("" . (is_array($EntityID) ? "OP.{$Entity}ID IN ({$EntityIDList})" : null) . "", "{$_POST["OrderBy"]} {$_POST["Order"]}")[0]) as $Column)$ColumnOption[] = new Option($Column);

	print "
		<div class=\"AlignCenter\">
			" . HTML\UI\Form(
				$Application->URL($_POST["_Script"]), // Submission URL
				"
					" . HTML\UI\Field(HTML\UI\CheckboxGroup("" . ($Caption = "Column") . "", $ColumnOption), "{$Caption}", null, null, $Configuration["FieldCaptionWidth"], $Configuration["FieldContentFullWidth"]) . "
					" . HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Format") . "", [new Option(IMPORT_TYPE_CSV), new Option(IMPORT_TYPE_TSV), new Option(IMPORT_TYPE_XML), new Option(IMPORT_TYPE_JSON)]), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "
					" . HTML\UI\Field(HTML\UI\RadioGroup("" . ($Caption = "Header") . "", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")]), "{$Caption}", false, true) . "
					" . HTML\UI\Input("{$Entity}" . ($Caption = "ID") . "List", $Configuration["InputWidth"], $EntityIDList, true, INPUT_TYPE_HIDDEN) . "
					" . HTML\UI\Input("" . ($Caption = "Order") . "By", $Configuration["InputWidth"], null, true, INPUT_TYPE_HIDDEN) . "
					" . HTML\UI\Input("" . ($Caption = "Order") . "", $Configuration["InputWidth"], null, true, INPUT_TYPE_HIDDEN) . "
					" . HTML\UI\Input("btn" . ($Caption = "Export") . "", $Configuration["InputWidth"], null, true, INPUT_TYPE_HIDDEN) . "
					" . HTML\UI\Input("" . ($Caption = "Redirection") . "URL", $Configuration["InputWidth"], $_SERVER["HTTP_REFERER"], true, INPUT_TYPE_HIDDEN) . "
				",
				"Export", // Submit button caption
				$Application->EncryptionKey(), // Signature modifier
				"<img src=\"{$Environment->IconURL()}{$LowercaseEntity}.png\" alt=\"{$EntityName}\" class=\"Icon\">Export {$LowercaseEntityName} data", // Title
				"Use the form below to export {$LowercaseEntityName} data to the desired format.", // Header
				"Press the 'Export' button to save the information.", // Footer
				"All field(s) are required except marked optional.", // Status
				"frm{$Entity}Export" // ID
			) . "
		</div>
	";
}

if(isset($_POST["btnImport"])){
	Upload("{$Environment->TempPath()}");

	if(isset($_POST["{$Entity}DataFile"])){
		$Table["{$Entity}"]->Import("{$Environment->TempPath()}{$_POST["{$Entity}DataFile"]}", [
			new Database\Field("{$Entity}" . ($Field = "Name") . "", "{$Field}"),
			new Database\Field("Product" . ($Field = "Category") . "ID", "{$Field}", null, $Table["{$Field}"], "{$Field}Name"),
			new Database\Field("{$Entity}Is" . ($Field = "Active") . "", "{$Field}"),
		]);

		unlink("{$Environment->TempPath()}{$_POST["{$Entity}DataFile"]}");
		$Terminal->Redirect($_POST["RedirectionURL"]);
	}

	print "
		<div class=\"AlignCenter\">
			" . HTML\UI\Form(
				$Application->URL($_POST["_Script"]), // Submission URL
				"
					" . HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Data") . "File", $Configuration["InputFullWidth"], null, true, INPUT_TYPE_FILE), "{$Caption}", false, false, $Configuration["FieldCaptionWidth"]) . "
					" . HTML\UI\Input("btn" . ($Caption = "Import") . "", $Configuration["InputWidth"], null, true, INPUT_TYPE_HIDDEN) . "
					" . HTML\UI\Input("" . ($Caption = "Redirection") . "URL", $Configuration["InputWidth"], $_SERVER["HTTP_REFERER"], true, INPUT_TYPE_HIDDEN) . "
				",
				"Import", // Submit button caption
				$Application->EncryptionKey(), // Signature modifier
				"<img src=\"{$Environment->IconURL()}{$LowercaseEntity}.png\" alt=\"{$EntityName}\" class=\"Icon\">Import {$LowercaseEntityName}", // Title
				"Use the form below to upload data file to import {$LowercaseEntityName} from.", // Header
				"Press the 'Import' button to save the information.", // Footer
				"All field(s) are required except marked optional.", // Status
				"frm{$Entity}Import" // ID
			) . "
		</div>
	";
}

if(isset($_POST["btnInput"])){ // Input form or INSERT/UPDATE record
	if(is_array($EntityID))$EntityID = 0;
	if($EntityID)$Record = $Table["{$Entity}"]->Get("{$Entity}ID = {$EntityID}")[0]; // Existing record
	$IntermediateEntityList = ListToArray("xCategory, xEvent");

	if(isset($_POST["btnSubmit"])){ // INSERT/UPDATE record
		$Form = new HTML\UI\Form(null, null, null, $Application->EncryptionKey(), null, null, null, null,
			$_POST["_ID"],
			[
				new HTTP\InputValidation("{$Entity}Name", true),
				new HTTP\InputValidation("ProductCategoryID", true, VALIDATION_TYPE_INTEGER),
			]
		);

		if($Form->Verify()){ // Form input okay
			if($Table["{$Entity}"]->Get( // Check for duplicate values for UNIQUE columns
				"
					(
							" . ($Column = "{$Entity}Name") . " = '{$Database->Escape($_POST["{$Column}"])}'
						AND	{$Table["{$Entity}"]->Alias()}." . ($Column = "ProductCategoryID") . " = " . intval($_POST["{$Column}"]) . "
					)
					AND	{$Entity}ID <> {$EntityID}
				"
			, null, null, null, null, null, null))$Form->ErrorMessage("Name already exists!");

			if(!$Form->ErrorMessage()){ // Everything looks fine
				Upload("{$Environment->UploadPath()}{$LowercaseEntity}/");
				$FilePOSTKey = array_keys($_FILES); // Key list of file POST variables

				// Delete existing files for uploaded new file or when asked to delete
				foreach($FilePOSTKey as $Column){
					// Set POST value for no file uploaded
					if(!isset($_POST["{$Column}"]) || $_POST["{$Column}"] === false)$_POST["{$Column}"] = null;

					if(isset($Record) && $Record["{$Column}"]){ // Existing record and file data exists
						if(
								// New file uploaded or asked to delete existing file
								($_POST["{$Column}"] || isset($_POST["__DeleteExistingFile_{$Column}"]))
							&&	file_exists($ExistingFile = "{$EntityUploadPath}{$Record["{$Column}"]}") // File exists
						)unlink($ExistingFile); // Delete existing file

						if( // No file uploaded and didn't ask to delete existing file either
							!$_POST["{$Column}"] && !isset($_POST["__DeleteExistingFile_{$Column}"])
						)$_POST["{$Column}"] = $Record["{$Column}"]; // Set existing value to the POST variable
					}
				}

				// Create thumbnail
				foreach(array_intersect($Utility->ListToArray("x{$Entity}Picture, x{$Entity}Avatar"), $FilePOSTKey) as $Column){
					$_POST[$ThumbnailField = "{$Column}Thumbnail"] = $_POST[$Column] ? ($_POST[$Column] != $Record[$Column] ? $Utility->Graphic()->Resample("{$EntityUploadPath}{$_POST[$Column]}", 48, 48) : $Record[$ThumbnailField]) : null;
					if($_POST[$ThumbnailField] != $Record[$ThumbnailField] && $Record[$ThumbnailField] && file_exists($ExistingFile = "{$EntityUploadPath}{$Record[$ThumbnailField]}"))unlink($ExistingFile);
				}

				#region Custom field
				//$_POST[$Field = "{$Entity}PasswordHash"] = strlen($_POST["{$Entity}Password"]) ? md5($_POST["{$Entity}Password"]) : $Record["{$Field}"];
				#endregion Custom field

				$Table["{$Entity}"]->Put($_POST, $EntityID ? "{$Entity}ID = {$EntityID}" : null, null, false);
				$AffectedRecord = $Table["{$Entity}"]->Get("{$Entity}ID = " . ($EntityID ? $EntityID : "@@IDENTITY") . "")[0];

				// Insert option data through intermediate table
				foreach($IntermediateEntityList as $OptionEntity)if(isset($Table[$IntermediateEntity = "{$Entity}{$OptionEntity}"])){
					$Table[$IntermediateEntity]->Remove("{$Entity}ID = {$AffectedRecord["{$Entity}ID"]}");
					foreach($_POST as $Key=>$OptionID)if(substr($Key, 0, strlen("{$OptionEntity}ID_")) == "{$OptionEntity}ID_")$IntermediateOptionData[] = ["{$Entity}ID"=>$AffectedRecord["{$Entity}ID"], "{$OptionEntity}ID"=>$OptionID, "{$Entity}{$OptionEntity}IsActive"=>1];
					if(isset($IntermediateOptionData))$Table["{$IntermediateEntity}"]->Put($IntermediateOptionData);
				}

				print "" . HTML\UI\MessageBox("Information saved into the database successfully.", "System") . "";
				$Terminal->Redirect($_POST["_Referer"]); // Redirect to previous location
			}
		}
	}

	#region Input form
	if($EntityID)foreach($Record as $Key=>$Value)$_POST[$Key] = $Value; // Load existing data into the input form

	#region CUSTOM: Set default values
	// From search paramethers
	SetVariable($Field = "CategoryID", SetVariable("{$Configuration["SearchInputPrefix"]}{$Field}"));
	#endregion CUSTOM: Set default values

	// Load option data from intermediate table
	foreach($IntermediateEntityList as $OptionEntity)if(isset($Table[$IntermediateEntity = "{$Entity}{$OptionEntity}"])){
		$OptionRecordset = $Table[$IntermediateEntity]->Get("{$Table[$IntermediateEntity]->Alias()}.{$Entity}ID = {$EntityID} AND {$Entity}{$OptionEntity}IsActive = 1", null, null, null, null, null, false);
		if(is_array($OptionRecordset))foreach($OptionRecordset as $Option)$_POST["{$OptionEntity}ID_{$Option["{$OptionEntity}ID"]}"] = $Option["{$OptionEntity}ID"];
	}

	unset($_POST["_Referer"]); // Reset referer URL form field

	print "
		<div class=\"AlignCenter\">
			" . HTML\UI\Form(
				$Application->URL($_POST["_Script"]), // Submission URL
				"
					" . HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Name") . "", $Configuration["InputFullWidth"], null, true), "{$Caption}", null, null, $Configuration["FieldCaptionWidth"]) . "
					" . HTML\UI\Field(HTML\UI\Select("Product" . ($Caption = "Category") . "ID", $Table[$OptionEntity = "Product{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "
					" . HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Picture") . "", $Configuration["InputFullWidth"], "{$Environment->UploadURL()}{$LowercaseEntity}/" . (isset($_POST["{$Entity}{$Caption}"]) ? $_POST["{$Entity}{$Caption}"] : null) . "", null, INPUT_TYPE_FILE), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "
					" . HTML\UI\Field(HTML\UI\RadioGroup("{$Entity}Is" . ($Caption = "Active") . "", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")]), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "
					" . HTML\UI\Input("btn" . ($Caption = "Input") . "", $Configuration["InputWidth"], null, true, INPUT_TYPE_HIDDEN) . "
					" . HTML\UI\Input("{$Entity}" . ($Caption = "ID") . "", $Configuration["InputWidth"], $EntityID, true, INPUT_TYPE_HIDDEN) . "
				",
				$EntityID ? "Update" : "Insert", // Submit button caption
				$Application->EncryptionKey(), // Signature modifier
				"<img src=\"{$Environment->IconURL()}{$LowercaseEntity}.png\" alt=\"{$EntityName}\" class=\"Icon\">" . ($EntityID ? "Edit" : "Add new") . " {$LowercaseEntityName}", // Title
				"Use the form below to add a new {$LowercaseEntityName} record into the system.", // Header
				"Press the 'Insert' or 'Update' button to save the information.", // Footer
				"All field(s) are required except marked optional.", // Status
				"frm{$Entity}Input" // ID
			) . "
		</div>
	";
	#endregion Input form
}

if(isset($_POST["btnDelete"])){
	$Table["{$Entity}"]->Remove("{$Entity}ID IN (" . (is_array($EntityID) ? implode(", ", $EntityID) : $EntityID) . ")");
	print "" . HTML\UI\MessageBox("Information removed from system.", "System") . "";
	$Terminal->Redirect($_SERVER["HTTP_REFERER"]);
}

#region List
#region Search SQL
$SearchSQL[] = "1 = 1"; // Custom fixed search condition
$SearchSQL[] = SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Name") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null;
$SearchSQL[] = SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "ProductCategoryID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null;
$SearchSQL[] = SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}IsActive") . "", SetVariable($Column, "")) !== "" ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null;

// Generate URL arguments for search field(s)
foreach($_POST as $Key=>$Value)if(substr($Key, 0, strlen($Configuration["SearchInputPrefix"])) == $Configuration["SearchInputPrefix"] && strlen($Value))$SearchArgument[] = "{$Key}=" . urlencode($Value) . "";
#endregion Search SQL

print "
	" . HTML\UI\Datagrid(
		// WHERE clause for search
		$Table["{$Entity}"]->Get(implode(" AND ", array_filter($SearchSQL)), "" . SetVariable("OrderBy", "{$Entity}Name") . " " . SetVariable("Order", "ASC") . "", ((SetVariable("Page", 1) - 1) * ($RecordsPerPage = $Configuration["DatagridRowsPerPage"])) + 1, $RecordsPerPage, null, null, false),
		$Application->URL($_POST["_Script"], isset($SearchArgument) ? implode("&", $SearchArgument) : null),
		$Table["{$Entity}"]->Count(),
		[ // Columns to display
			new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Picture") . "URL", "{$Caption}", FIELD_TYPE_PICTUREURL),
			new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Name") . "", "{$Caption}"),
			new HTML\UI\Datagrid\Column("Product" . ($Caption = "Category") . "Name", "{$Caption}"),
			new HTML\UI\Datagrid\Column("{$Entity}Is" . ($Caption = "Active") . "", "{$Caption}", FIELD_TYPE_BOOLEANICON),
		],
		"<img src=\"{$Environment->IconURL()}{$LowercaseEntity}.png\" alt=\"{$EntityName}\" class=\"Icon\">{$EntityName}",
		$RecordsPerPage,
		"{$Entity}ID",
		[ // Action
			new HTML\UI\Datagrid\Action("{$Environment->IconURL()}edit.png", null, $Application->URL($_POST["_Script"], "btnInput")),
			new HTML\UI\Datagrid\Action("{$Environment->IconURL()}delete.png", null, $Application->URL($_POST["_Script"], "btnDelete"), null, "return confirm('Are you sure to remove the information?');"),
		],
		$Environment->URL(), // Base URL
		$Environment->IconURL(), // Base URL for icons
		"Total of {$Table["{$Entity}"]->Count()} record(s) took " . round($Table["{$Entity}"]->LastDuration() * 1000) . " ms",
		"
			" . HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Name") . "", 200), "{$Caption}", null, null) . "
			" . HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}Product" . ($Caption = "Category") . "ID", $Table[$OptionEntity = "Product{$Caption}"]->Get(), new Option(), "{$OptionEntity}LookupCaption"), "{$Caption}", null, true) . "
			" . HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}Is" . ($Caption = "Active") . "", [new Option(), new Option(0, "No"), new Option(1, "Yes")]), "{$Caption}", null, true) . "
			<div class=\"ColumnWrapper\"></div>

			<div class=\"ButtonRow\">
				" . HTML\UI\Button("<img src=\"{$Environment->IconURL()}search.png\" alt=\"Search\" class=\"Icon\">Search", BUTTON_TYPE_SUBMIT, "btnSearch", true) . "
				" . HTML\UI\Button("<img src=\"{$Environment->IconURL()}add.png\" alt=\"Add new\" class=\"Icon\">Add new", BUTTON_TYPE_SUBMIT, "btnInput", true) . "
				" . HTML\UI\Button("<img src=\"{$Environment->IconURL()}delete.png\" alt=\"Remove\" class=\"Icon\">Remove", BUTTON_TYPE_SUBMIT, "btnDelete", true, "return confirm('Are you sure to remove the information?');") . "
				" . HTML\UI\Button("<img src=\"{$Environment->IconURL()}export.png\" alt=\"Export\" class=\"Icon\">Export", BUTTON_TYPE_SUBMIT, "btnExport", true) . "
				" . HTML\UI\Button("<img src=\"{$Environment->IconURL()}import.png\" alt=\"Import\" class=\"Icon\">Import", BUTTON_TYPE_SUBMIT, "btnImport", true) . "
			</div>
		"
	) . "
";
#endregion List
?>