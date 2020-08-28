<?php
namespace sPHP;

$Entity = "ConsignmentExportProduct";

if(isset($_POST["btnSubmit"])){
	$OptionEntity = "ExportProduct";
	$ExistingExportProductID = [];

	// Reindex pack count POST variable with export product ID
	foreach($_POST["{$OptionEntity}ID"] as $Key=>$ExportProductID)$ConsignmentExportProductPackCount[$ExportProductID] = $_POST["{$Entity}PackCount"][$Key];

	// Update existing export product
	if($ConsignmentExportProduct = $Table[$Entity]->Get("{$Table[$Entity]->Alias()}.ConsignmentID = {$_POST["ConsignmentID"]} AND {$Table[$Entity]->Alias()}.{$OptionEntity}ID IN (" . implode(", ", $_POST["{$OptionEntity}ID"]) . ")"))foreach($ConsignmentExportProduct AS $Product){
		$ExistingExportProductID[] = $Product["{$OptionEntity}ID"];

		$Table[$Entity]->Put([
			($Column = "{$Entity}PackCount")=>$ConsignmentExportProductPackCount[$Product["{$OptionEntity}ID"]],
		], "{$Table[$Entity]->Structure()["Primary"][0]} = {$Product[$Table[$Entity]->Structure()["Primary"][0]]}", null, null);
	}

	// Add new export product
	foreach(array_diff($_POST["{$OptionEntity}ID"], $ExistingExportProductID) as $ExportProductID){
		$Table[$Entity]->Put([
			($Column = "{$OptionEntity}ID")=>$ExportProductID,
			($Column = "ConsignmentID")=>$_POST[$Column],
			($Column = "{$Entity}PackCount")=>$ConsignmentExportProductPackCount[$ExportProductID],
			($Column = "{$Entity}IsActive")=>1,
		], null, null, null);
	}

	$Terminal->Redirect($_POST["_Referer"], "Operation completed successfully.");
}

foreach($Table[$OptionEntity = "ExportProduct"]->Get("{$Table[$OptionEntity]->Alias()}.ExportID = (SELECT C.ExportID FROM sphp_consignment AS C WHERE C.ConsignmentID = {$_POST["ConsignmentID"]})", "P.ProductName ASC") as $Key=>$ExportProduct){
	$ExportProductTableRowHTML[] = "
		<tr>
			<td>" . ($Key + 1) . "</td>
			<td>{$ExportProduct["ProductName"]}<input type=\"hidden\" name=\"{$OptionEntity}ID[]\" value=\"{$ExportProduct["{$OptionEntity}ID"]}\"></td>
			<td>{$ExportProduct["{$OptionEntity}Rate"]} {$ExportProduct["CurrencyCode"]}</td>
			<td><input type=\"number\" name=\"{$Entity}PackCount[]\" value=\"{$ExportProduct["{$OptionEntity}PackCount"]}\" min=\"0\" style=\"width: 100px;\"></td>
		</tr>
	";
}

print "
	<div class=\"AlignCenter\">
		" . HTML\UI\Form(
			$Application->URL($_POST["_Script"]), // Submission URL
			"
				<style>
					#ExportProductTable > thead{background-color: #006bc7; text-align: center;}
					#ExportProductTable > tbody > tr > td{padding-top: 0; padding-bottom: 0;}
					#ExportProductTable > tbody > tr > td:first-child{text-align: right;}
					#ExportProductTable > tbody > tr > td:nth-child(4){padding: 0;}
					#ExportProductTable > tbody > tr > td > input{border-width: 0;}
					#ExportProductTable > tbody > tr > td > input:hover{background-color: #FFFF77;}
					#ExportProductTable button{background-color: #0089ff;}
				</style>

				<table id=\"ExportProductTable\">
					<thead>
						<tr>
							<td>#</td>
							<td>Product</td>
							<td>Rate</td>
							<td>Quantity</td>
						</tr>
					</thead>

					<tbody id=\"ExportProductTableBody\">" . implode(null, $ExportProductTableRowHTML) . "</tbody>
				</table>

				<input type=\"hidden\" name=\"ConsignmentID\" value=\"{$_POST["ConsignmentID"]}\">
			",
			"Update", // Submit button caption
			$Application->EncryptionKey(), // Signature modifier
			"<img src=\"{$Environment->IconURL()}" . strtolower($Entity) . ".png\" alt=\"{$Table[$Entity]->EntityName()}\" class=\"Icon\">Stage " . strtolower($Table[$Entity]->FormalName()) . "", // Title
			"Use the list below to update records into the system.", // Header
			"Press the 'Update' button to save the information.", // Footer
			"All field(s) are required except marked optional.", // Status
			"frm{$Table[$Entity]->EntityName()}Input" // ID
		) . "
	</div>
";
?>