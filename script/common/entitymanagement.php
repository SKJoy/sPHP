<?php
namespace sPHP;

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
		$Table["{$Entity}"]->Import("{$Environment->TempPath()}{$_POST["{$Entity}DataFile"]}", $ImportField);
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
	if($InputIsPermissionErrorMessage){
		print HTML\UI\MessageBox("{$InputIsPermissionErrorMessage}", "Security", "MessageBoxError");
	}
	else{
		if(is_array($EntityID))$EntityID = 0;
		if(isset($InputRecord))$Record = $InputRecord; // Existing record

		if(isset($_POST["btnSubmit"])){ // INSERT/UPDATE record
			$Form = new HTML\UI\Form(null, null, null, $Application->EncryptionKey(), null, null, null, null, $_POST["_ID"], $InputFieldValidation);

			if($Form->Verify()){ // Form input okay
				// Check for duplicate values for UNIQUE columns
				if($Table["{$Entity}"]->Get($InputDataVerificationSQL, null, null, null, null, null, null))$Form->ErrorMessage("Name already exists!");

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
					foreach(array_intersect($Utility->ListToArray($InputThumbnailColumnList), $FilePOSTKey) as $Column){
						$_POST[$ThumbnailField = "{$Column}Thumbnail"] = $_POST[$Column] ? ($_POST[$Column] != $Record[$Column] ? $Utility->Graphic()->Resample("{$EntityUploadPath}{$_POST[$Column]}", $InputThumbnailMaximumDimension, $InputThumbnailMaximumDimension) : $Record[$ThumbnailField]) : null;
						if($_POST[$ThumbnailField] != $Record[$ThumbnailField] && $Record[$ThumbnailField] && file_exists($ExistingFile = "{$EntityUploadPath}{$Record[$ThumbnailField]}"))unlink($ExistingFile);
					}

					#region Custom field
					//$_POST[$Field = "{$Entity}PasswordHash"] = strlen($_POST["{$Entity}Password"]) ? md5($_POST["{$Entity}Password"]) : $Record["{$Field}"];
					#endregion Custom field

					$Table["{$Entity}"]->Put($_POST, $EntityID ? "{$Entity}ID = {$EntityID}" : null, null, false);
					$AffectedRecord = $Table["{$Entity}"]->Get("{$Entity}ID = " . ($EntityID ? $EntityID : "@@IDENTITY") . "")[0];

					// Insert option data through intermediate table
					foreach($InputIntermediateEntityList as $OptionEntity)if(isset($Table[$IntermediateEntity = "{$Entity}{$OptionEntity}"])){
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
		#region CUSTOM: Set default values
		// From search paramethers
		foreach(ListToArray($InputDefaultValueFromSearchFieldList) as $Field)SetVariable($Field, SetVariable("{$Configuration["SearchInputPrefix"]}{$Field}"));
		#endregion CUSTOM: Set default values

		unset($_POST["_Referer"]); // Reset referer URL form field

		print "
			<div class=\"AlignCenter\">
				" . HTML\UI\Form(
					$Application->URL($_POST["_Script"]), // Submission URL
					$InputInterface,
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
}

if(isset($_POST["btnDelete"])){
	$Table["{$Entity}"]->Remove("{$Entity}ID IN (" . (is_array($EntityID) ? implode(", ", $EntityID) : $EntityID) . ")");
	print "" . HTML\UI\MessageBox("Information removed from system.", "System") . "";
	$Terminal->Redirect($_SERVER["HTTP_REFERER"]);
}

#region List
// Generate URL arguments for search field(s)
foreach($_POST as $Key=>$Value)if(substr($Key, 0, strlen($Configuration["SearchInputPrefix"])) == $Configuration["SearchInputPrefix"] && strlen($Value))$SearchArgument[] = "{$Key}=" . urlencode($Value) . "";

print "
	" . HTML\UI\Datagrid(
		// WHERE clause for search
		$Table["{$Entity}"]->Get(implode(" AND ", array_filter($ListSearchSQL)), "" . SetVariable("OrderBy", $ListOrderBy) . " " . SetVariable("Order", $ListOrder) . "", ((SetVariable("Page", 1) - 1) * ($ListRecordsPerPage)) + 1, $ListRecordsPerPage, null, null, null),
		$Application->URL($_POST["_Script"], isset($SearchArgument) ? implode("&", $SearchArgument) : null),
		$Table["{$Entity}"]->Count(),
		$ListColumn,
		"<img src=\"{$Environment->IconURL()}{$LowercaseEntity}.png\" alt=\"{$EntityName}\" class=\"Icon\">{$EntityName}",
		$ListRecordsPerPage,
		"{$Entity}ID",
		$ListAction,
		$ListBaseURL, // Base URL
		$ListIconURL, // Base URL for icons
		"Total of {$Table["{$Entity}"]->Count()} record(s) took " . round($Table["{$Entity}"]->LastDuration() * 1000) . " ms",
		"
			{$ListSearchInterface}
			<div class=\"ColumnWrapper\"></div>
			" . (isset($ListBatchActionInterface) && trim($ListBatchActionInterface) ? "<div class=\"ButtonRow\">{$ListBatchActionInterface}</div>" : null) . "
		"
	) . "
";
#endregion List
?>