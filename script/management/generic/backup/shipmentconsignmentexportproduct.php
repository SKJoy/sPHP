<?php
namespace sPHP;

$_POST["ShipmentID"] = intval($_POST["ShipmentID"]);
$Entity = "ShipmentConsignmentExportProduct";

if(isset($_POST["btnSubmit"])){
	$OptionEntity = "ConsignmentExportProduct";
	$ExistingShipmentConsignmentExportProductID = [];
//var_dump($_POST);
	// Reindex pack count POST variable with export product ID
	foreach($_POST["{$OptionEntity}ID"] as $Key=>$ConsignmentExportProductID){
		$ShipmentConsignmentExportProductPackCount[$ConsignmentExportProductID] = $_POST["{$Entity}PackCount"][$Key];
		$ShipmentConsignmentExportProductPackExtraCount[$ConsignmentExportProductID] = $_POST["{$Entity}PackExtraCount"][$Key];
		$ShipmentConsignmentExportProductBatch[$ConsignmentExportProductID] = $_POST["{$Entity}Batch"][$Key];
		//$ShipmentConsignmentExportProductCartonQuantityDefinition[$ConsignmentExportProductID] = $_POST["{$Entity}CartonQuantityDefinition"][$Key];
		$ShipmentConsignmentExportProductCartonGrossWeight[$ConsignmentExportProductID] = $_POST["{$Entity}CartonGrossWeight"][$Key];
		$ShipmentConsignmentExportProductCartonNetWeight[$ConsignmentExportProductID] = $_POST["{$Entity}CartonNetWeight"][$Key];
		$ShipmentConsignmentExportProductCartonTotalGrossWeight[$ConsignmentExportProductID] = $_POST["{$Entity}CartonTotalGrossWeight"][$Key];
		$ShipmentConsignmentExportProductCartonTotalNetWeight[$ConsignmentExportProductID] = $_POST["{$Entity}CartonTotalNetWeight"][$Key];
		$ShipmentConsignmentExportProductCartonSerialFrom[$ConsignmentExportProductID] = $_POST["{$Entity}CartonSerialFrom"][$Key];
		$ShipmentConsignmentExportProductCartonSerialTo[$ConsignmentExportProductID] = $_POST["{$Entity}CartonSerialTo"][$Key];
	}

	$Table["{$Entity}"]->Remove("ShipmentID = {$_POST["ShipmentID"]} AND ConsignmentExportProductID NOT IN (" . implode(", ", $_POST["ConsignmentExportProductID"]) . ")");

	// Update existing export product
	if($ShipmentConsignmentExportProduct = $Table[$Entity]->Get("{$Table[$Entity]->Alias()}.ShipmentID = {$_POST["ShipmentID"]} AND {$Table[$Entity]->Alias()}.{$OptionEntity}ID IN (" . implode(", ", $_POST["{$OptionEntity}ID"]) . ")"))foreach($ShipmentConsignmentExportProduct AS $Product){
		$ExistingShipmentConsignmentExportProductID[] = $Product["{$OptionEntity}ID"];

		$Table[$Entity]->Put([
			($Column = "{$Entity}PackCount")=>$ShipmentConsignmentExportProductPackCount[$Product["{$OptionEntity}ID"]],
			($Column = "{$Entity}PackExtraCount")=>$ShipmentConsignmentExportProductPackExtraCount[$Product["{$OptionEntity}ID"]],
			($Column = "{$Entity}Batch")=>$ShipmentConsignmentExportProductBatch[$Product["{$OptionEntity}ID"]],
			//($Column = "{$Entity}CartonQuantityDefinition")=>$ShipmentConsignmentExportProductCartonQuantityDefinition[$Product["{$OptionEntity}ID"]],
			($Column = "{$Entity}CartonGrossWeight")=>$ShipmentConsignmentExportProductCartonGrossWeight[$Product["{$OptionEntity}ID"]],
			($Column = "{$Entity}CartonNetWeight")=>$ShipmentConsignmentExportProductCartonNetWeight[$Product["{$OptionEntity}ID"]],
			($Column = "{$Entity}CartonTotalGrossWeight")=>$ShipmentConsignmentExportProductCartonTotalGrossWeight[$Product["{$OptionEntity}ID"]],
			($Column = "{$Entity}CartonTotalNetWeight")=>$ShipmentConsignmentExportProductCartonTotalNetWeight[$Product["{$OptionEntity}ID"]],
			($Column = "{$Entity}CartonSerialFrom")=>$ShipmentConsignmentExportProductCartonSerialFrom[$Product["{$OptionEntity}ID"]],
			($Column = "{$Entity}CartonSerialTo")=>$ShipmentConsignmentExportProductCartonSerialTo[$Product["{$OptionEntity}ID"]],
		], "{$Table[$Entity]->Structure()["Primary"][0]} = {$Product[$Table[$Entity]->Structure()["Primary"][0]]}", null, null);
	}

	// Add new export product
	foreach(array_diff($_POST["{$OptionEntity}ID"], $ExistingShipmentConsignmentExportProductID) as $ConsignmentExportProductID){
		$Table[$Entity]->Put([
			($Column = "{$OptionEntity}ID")=>$ConsignmentExportProductID,
			($Column = "ShipmentID")=>$_POST[$Column],
			($Column = "{$Entity}PackCount")=>$ShipmentConsignmentExportProductPackCount[$ConsignmentExportProductID],
			($Column = "{$Entity}PackExtraCount")=>$ShipmentConsignmentExportProductPackExtraCount[$ConsignmentExportProductID],
			($Column = "{$Entity}Batch")=>$ShipmentConsignmentExportProductBatch[$ConsignmentExportProductID],
			//($Column = "{$Entity}CartonQuantityDefinition")=>$ShipmentConsignmentExportProductCartonQuantityDefinition[$ConsignmentExportProductID],
			($Column = "{$Entity}CartonGrossWeight")=>$ShipmentConsignmentExportProductCartonGrossWeight[$ConsignmentExportProductID],
			($Column = "{$Entity}CartonNetWeight")=>$ShipmentConsignmentExportProductCartonNetWeight[$ConsignmentExportProductID],
			($Column = "{$Entity}CartonTotalGrossWeight")=>$ShipmentConsignmentExportProductCartonTotalGrossWeight[$ConsignmentExportProductID],
			($Column = "{$Entity}CartonTotalNetWeight")=>$ShipmentConsignmentExportProductCartonTotalNetWeight[$ConsignmentExportProductID],
			($Column = "{$Entity}CartonSerialFrom")=>$ShipmentConsignmentExportProductCartonSerialFrom[$ConsignmentExportProductID],
			($Column = "{$Entity}CartonSerialTo")=>$ShipmentConsignmentExportProductCartonSerialTo[$ConsignmentExportProductID],
			($Column = "{$Entity}IsActive")=>1,
		], null, null, null);
	}

	$Terminal->Redirect($_POST["_Referer"], "Operation completed successfully.");
}

// Load currently saved shipment consignment export product particulars
if($ShipmentConsignmentExportProduct = $Table["ShipmentConsignmentExportProduct"]->Get("{$Table["ShipmentConsignmentExportProduct"]->Alias()}.ShipmentID = {$_POST["ShipmentID"]}")){
	foreach($ShipmentConsignmentExportProduct as $Product){
		$ShipmentConsignmentExportProductPackCount[$Product["ProductID"]] = $Product["ShipmentConsignmentExportProductPackCount"];
		$ShipmentConsignmentExportProductPackExtraCount[$Product["ProductID"]] = $Product["ShipmentConsignmentExportProductPackExtraCount"];
		$ShipmentConsignmentExportProductBatch[$Product["ProductID"]] = $Product["ShipmentConsignmentExportProductBatch"];
		//$ShipmentConsignmentExportProductCartonQuantityDefinition[$Product["ProductID"]] = $Product["ShipmentConsignmentExportProductCartonQuantityDefinition"];
		$ShipmentConsignmentExportProductCartonGrossWeight[$Product["ProductID"]] = $Product["ShipmentConsignmentExportProductCartonGrossWeight"];
		$ShipmentConsignmentExportProductCartonNetWeight[$Product["ProductID"]] = $Product["ShipmentConsignmentExportProductCartonNetWeight"];
		$ShipmentConsignmentExportProductCartonTotalGrossWeight[$Product["ProductID"]] = $Product["ShipmentConsignmentExportProductCartonTotalGrossWeight"];
		$ShipmentConsignmentExportProductCartonTotalNetWeight[$Product["ProductID"]] = $Product["ShipmentConsignmentExportProductCartonTotalNetWeight"];
		$ShipmentConsignmentExportProductCartonSerialFrom[$Product["ProductID"]] = $Product["ShipmentConsignmentExportProductCartonSerialFrom"];
		$ShipmentConsignmentExportProductCartonSerialTo[$Product["ProductID"]] = $Product["ShipmentConsignmentExportProductCartonSerialTo"];
	}
}

if($ConsignmentExportProduct = $Table[$OptionEntity = "ConsignmentExportProduct"]->Get("{$Table[$OptionEntity]->Alias()}.ConsignmentID = (SELECT S.ConsignmentID FROM sphp_shipment AS S WHERE S.ShipmentID = {$_POST["ShipmentID"]})")){
	foreach($ConsignmentExportProduct as $Key=>$Product){
		$ConsignmentExportProductTableRowHTML[] = "
			<tr>
				<td class=\"Serial\">" . ($Key + 1) . "</td>
				<td class=\"Product\">{$Product["ProductName"]}<input type=\"hidden\" name=\"{$OptionEntity}ID[]\" value=\"{$Product["{$OptionEntity}ID"]}\"></td>
				<td><input type=\"number\" name=\"{$Entity}PackCount[]\" value=\"" . (isset($ShipmentConsignmentExportProductPackCount[$Product["ProductID"]]) ? $ShipmentConsignmentExportProductPackCount[$Product["ProductID"]] : $Product["{$OptionEntity}PackCount"]) . "\" style=\"width: 100px;\"></td>
				<td><input type=\"number\" name=\"{$Entity}PackExtraCount[]\" value=\"" . (isset($ShipmentConsignmentExportProductPackExtraCount[$Product["ProductID"]]) ? $ShipmentConsignmentExportProductPackExtraCount[$Product["ProductID"]] : 0) . "\" style=\"width: 100px;\"></td>
				<td><input type=\"text\" name=\"{$Entity}Batch[]\" value=\"" . (isset($ShipmentConsignmentExportProductBatch[$Product["ProductID"]]) ? $ShipmentConsignmentExportProductBatch[$Product["ProductID"]] : null) . "\" style=\"width: 150px;\"></td>
				<!--<td><input type=\"text\" name=\"{$Entity}CartonQuantityDefinition[]\" value=\"" . (isset($ShipmentConsignmentExportProductCartonQuantityDefinition[$Product["ProductID"]]) ? $ShipmentConsignmentExportProductCartonQuantityDefinition[$Product["ProductID"]] : null) . "\" style=\"width: 250px;\"></td>-->
				<td><input type=\"number\" name=\"{$Entity}CartonGrossWeight[]\" value=\"" . (isset($ShipmentConsignmentExportProductCartonGrossWeight[$Product["ProductID"]]) ? $ShipmentConsignmentExportProductCartonGrossWeight[$Product["ProductID"]] : 1) . "\" style=\"width: 100px;\"></td>
				<td><input type=\"number\" name=\"{$Entity}CartonNetWeight[]\" value=\"" . (isset($ShipmentConsignmentExportProductCartonNetWeight[$Product["ProductID"]]) ? $ShipmentConsignmentExportProductCartonNetWeight[$Product["ProductID"]] : 1) . "\" style=\"width: 100px;\"></td>
				<td><input type=\"number\" name=\"{$Entity}CartonTotalGrossWeight[]\" value=\"" . (isset($ShipmentConsignmentExportProductCartonTotalGrossWeight[$Product["ProductID"]]) ? $ShipmentConsignmentExportProductCartonTotalGrossWeight[$Product["ProductID"]] : 1) . "\" style=\"width: 100px;\"></td>
				<td><input type=\"number\" name=\"{$Entity}CartonTotalNetWeight[]\" value=\"" . (isset($ShipmentConsignmentExportProductCartonTotalNetWeight[$Product["ProductID"]]) ? $ShipmentConsignmentExportProductCartonTotalNetWeight[$Product["ProductID"]] : 1) . "\" style=\"width: 100px;\"></td>
				<td><input type=\"number\" name=\"{$Entity}CartonSerialFrom[]\" value=\"" . (isset($ShipmentConsignmentExportProductCartonSerialFrom[$Product["ProductID"]]) ? $ShipmentConsignmentExportProductCartonSerialFrom[$Product["ProductID"]] : 1) . "\" style=\"width: 100px;\"></td>
				<td><input type=\"number\" name=\"{$Entity}CartonSerialTo[]\" value=\"" . (isset($ShipmentConsignmentExportProductCartonSerialTo[$Product["ProductID"]]) ? $ShipmentConsignmentExportProductCartonSerialTo[$Product["ProductID"]] : 1) . "\" style=\"width: 100px;\"></td>
			</tr>
		";
	}

	print "
		<div class=\"AlignCenter\">
			" . HTML\UI\Form(
				$Application->URL($_POST["_Script"]), // Submission URL
				"
					<style>
						#ConsignmentExportProductTable > thead{background-color: #006bc7; text-align: center; font-weight: bold;}
						#ConsignmentExportProductTable > tbody > tr > td{padding: 0;}
						#ConsignmentExportProductTable > tbody > tr > .Serial{background-color: #E7E7FF; padding-left: 5px; padding-right: 5px; text-align: right;}
						#ConsignmentExportProductTable > tbody > tr > .Product{background-color: LightBlue; padding-left: 5px; padding-right: 5px;}
						#ConsignmentExportProductTable input{border-width: 0;}
						#ConsignmentExportProductTable input:hover{background-color: #FFFF77;}
					</style>

					<table id=\"ConsignmentExportProductTable\">
						<thead>
							<tr>
								<td rowspan=\"2\">#</td>
								<td rowspan=\"2\">Product</td>
								<td rowspan=\"2\">Count</td>
								<td rowspan=\"2\">Extra</td>
								<td rowspan=\"2\">Batch</td>
								<!--<td rowspan=\"2\">Carton quantity</td>-->
								<td rowspan=\"2\">G Wt/Ct</td>
								<td rowspan=\"2\">N Wt/Ct</td>
								<td colspan=\"2\">Total</td>
								<td rowspan=\"2\">C Sl from</td>
								<td rowspan=\"2\">C Sl to</td>
							</tr>

							<tr>
								<td>G Wt/Ct</td>
								<td>N Wt/Ct</td>
							</tr>
						</thead>

						<tbody id=\"ConsignmentExportProductTableBody\">" . implode(null, $ConsignmentExportProductTableRowHTML) . "</tbody>
					</table>

					<input type=\"hidden\" name=\"ShipmentID\" value=\"{$_POST["ShipmentID"]}\">
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
}
else{
	print HTML\UI\MessageBox("Please stage products in consignment first.", $Table[$Entity]->FormalName());
}
?>