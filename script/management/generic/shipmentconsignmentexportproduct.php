<?php
namespace sPHP;

$_POST["ShipmentID"] = intval($_POST["ShipmentID"]);
$Entity = "ShipmentConsignmentExportProduct";
$OptionEntity = "ConsignmentExportProduct";

if(isset($_POST["btnSubmit"])){ // Save ShipmentConsignmentExportProduct data
	$Table[$Entity]->Remove("ShipmentID = {$_POST["ShipmentID"]} AND {$OptionEntity}ID NOT IN (" . implode(", ", $_POST["{$OptionEntity}ID"]) . ")");

	// Update existing data
	$ExistingConsignmentExportProductID = [];

	if($ShipmentConsignmentExportProduct = $Table[$Entity]->Get("{$Table[$Entity]->Alias()}.ShipmentID = {$_POST["ShipmentID"]} AND {$Table[$Entity]->Alias()}.{$Entity}IsActive = 1"))foreach($ShipmentConsignmentExportProduct as $Record){
		$ExistingConsignmentExportProductID[] = $Record["{$OptionEntity}ID"];
		$Key = array_search($Record["{$OptionEntity}ID"], $_POST["{$OptionEntity}ID"]);

		$Table[$Entity]->Put([ // Update
			($Column = "{$Entity}PackCount")=>$_POST[$Column][$Key],
			($Column = "{$Entity}PackExtraCount")=>$_POST[$Column][$Key],
			($Column = "{$Entity}Batch")=>$_POST[$Column][$Key],
			($Column = "{$Entity}CartonGrossWeight")=>$_POST[$Column][$Key],
			($Column = "{$Entity}CartonNetWeight")=>$_POST[$Column][$Key],
			($Column = "{$Entity}CartonTotalGrossWeight")=>$_POST[$Column][$Key],
			($Column = "{$Entity}CartonTotalNetWeight")=>$_POST[$Column][$Key],
			($Column = "{$Entity}CartonSerialFrom")=>$_POST[$Column][$Key],
			($Column = "{$Entity}CartonSerialTo")=>$_POST[$Column][$Key],
		], "{$Entity}ID = {$Record["{$Entity}ID"]}");
	}

	foreach(array_keys(array_diff($_POST["{$OptionEntity}ID"], $ExistingConsignmentExportProductID)) as $Key){
		$Table[$Entity]->Put([ // Insert new data
			($Column = "ShipmentID")=>$_POST[$Column],
			($Column = "{$OptionEntity}ID")=>$_POST[$Column][$Key],
			($Column = "{$Entity}PackCount")=>$_POST[$Column][$Key],
			($Column = "{$Entity}PackExtraCount")=>$_POST[$Column][$Key],
			($Column = "{$Entity}Batch")=>$_POST[$Column][$Key],
			($Column = "{$Entity}CartonGrossWeight")=>$_POST[$Column][$Key],
			($Column = "{$Entity}CartonNetWeight")=>$_POST[$Column][$Key],
			($Column = "{$Entity}CartonTotalGrossWeight")=>$_POST[$Column][$Key],
			($Column = "{$Entity}CartonTotalNetWeight")=>$_POST[$Column][$Key],
			($Column = "{$Entity}CartonSerialFrom")=>$_POST[$Column][$Key],
			($Column = "{$Entity}CartonSerialTo")=>$_POST[$Column][$Key],
			($Column = "{$Entity}IsActive")=>1,
		]);
	}
}

// Load existing ShipmentConsignmentExportProduct data
if($ShipmentConsignmentExportProduct = $Table[$Entity]->Get("{$Table[$Entity]->Alias()}.ShipmentID = {$_POST["ShipmentID"]} AND {$Table[$Entity]->Alias()}.{$Entity}IsActive = 1"))foreach($ShipmentConsignmentExportProduct as $Record){
	$Data[$Record["{$OptionEntity}ID"]][$Column = "{$Entity}PackCount"] = $Record[$Column];
	$Data[$Record["{$OptionEntity}ID"]][$Column = "{$Entity}PackExtraCount"] = $Record[$Column];
	$Data[$Record["{$OptionEntity}ID"]][$Column = "{$Entity}Batch"] = $Record[$Column];
	$Data[$Record["{$OptionEntity}ID"]][$Column = "{$Entity}CartonGrossWeight"] = $Record[$Column];
	$Data[$Record["{$OptionEntity}ID"]][$Column = "{$Entity}CartonNetWeight"] = $Record[$Column];
	$Data[$Record["{$OptionEntity}ID"]][$Column = "{$Entity}CartonTotalGrossWeight"] = $Record[$Column];
	$Data[$Record["{$OptionEntity}ID"]][$Column = "{$Entity}CartonTotalNetWeight"] = $Record[$Column];
	$Data[$Record["{$OptionEntity}ID"]][$Column = "{$Entity}CartonSerialFrom"] = $Record[$Column];
	$Data[$Record["{$OptionEntity}ID"]][$Column = "{$Entity}CartonSerialTo"] = $Record[$Column];
}

//var_dump($Data); exit;
if($ConsignmentExportProduct = $Table[$OptionEntity]->Get("{$Table[$OptionEntity]->Alias()}.ConsignmentID = (SELECT S.ConsignmentID FROM sphp_shipment AS S WHERE S.ShipmentID = {$_POST["ShipmentID"]})")){
	foreach($ConsignmentExportProduct as $Key=>$Product){
		$ConsignmentExportProductTableRowHTML[] = "
			<tr>
				<td class=\"Serial\">" . ($Key + 1) . "</td>
				<td class=\"Product\">{$Product["ProductName"]}<input type=\"hidden\" name=\"{$OptionEntity}ID[]\" value=\"{$Product["{$OptionEntity}ID"]}\"></td>
				<td><input type=\"number\" name=\"{$Entity}" . ($ColumnSuffix = "PackCount") . "[]\" value=\"" . (isset($Data[$Product["{$OptionEntity}ID"]][$Column = "{$Entity}{$ColumnSuffix}"]) ? $Data[$Product["{$OptionEntity}ID"]][$Column] : $Product["{$OptionEntity}{$ColumnSuffix}"]) . "\" min=\"0\" style=\"width: 100px;\"></td>
				<td><input type=\"number\" name=\"{$Entity}" . ($ColumnSuffix = "PackExtraCount") . "[]\" value=\"" . (isset($Data[$Product["{$OptionEntity}ID"]][$Column = "{$Entity}{$ColumnSuffix}"]) ? $Data[$Product["{$OptionEntity}ID"]][$Column] : 0) . "\" min=\"0\" style=\"width: 100px;\"></td>
				<td><input type=\"text\" name=\"{$Entity}" . ($ColumnSuffix = "Batch") . "[]\" value=\"" . (isset($Data[$Product["{$OptionEntity}ID"]][$Column = "{$Entity}{$ColumnSuffix}"]) ? $Data[$Product["{$OptionEntity}ID"]][$Column] : null) . "\" style=\"width: 150px;\"></td>
				<td><input type=\"number\" name=\"{$Entity}" . ($ColumnSuffix = "CartonGrossWeight") . "[]\" value=\"" . (isset($Data[$Product["{$OptionEntity}ID"]][$Column = "{$Entity}{$ColumnSuffix}"]) ? $Data[$Product["{$OptionEntity}ID"]][$Column] : 1) . "\" min=\"0.001\" step=\"0.001\" style=\"width: 100px;\"></td>
				<td><input type=\"number\" name=\"{$Entity}" . ($ColumnSuffix = "CartonNetWeight") . "[]\" value=\"" . (isset($Data[$Product["{$OptionEntity}ID"]][$Column = "{$Entity}{$ColumnSuffix}"]) ? $Data[$Product["{$OptionEntity}ID"]][$Column] : 1) . "\" min=\"0.001\" step=\"0.001\" style=\"width: 100px;\"></td>
				<td><input type=\"number\" name=\"{$Entity}" . ($ColumnSuffix = "CartonTotalGrossWeight") . "[]\" value=\"" . (isset($Data[$Product["{$OptionEntity}ID"]][$Column = "{$Entity}{$ColumnSuffix}"]) ? $Data[$Product["{$OptionEntity}ID"]][$Column] : 1) . "\" min=\"0.001\" step=\"0.001\" style=\"width: 100px;\"></td>
				<td><input type=\"number\" name=\"{$Entity}" . ($ColumnSuffix = "CartonTotalNetWeight") . "[]\" value=\"" . (isset($Data[$Product["{$OptionEntity}ID"]][$Column = "{$Entity}{$ColumnSuffix}"]) ? $Data[$Product["{$OptionEntity}ID"]][$Column] : 1) . "\" min=\"0.001\" step=\"0.001\" style=\"width: 100px;\"></td>
				<td><input type=\"number\" name=\"{$Entity}" . ($ColumnSuffix = "CartonSerialFrom") . "[]\" value=\"" . (isset($Data[$Product["{$OptionEntity}ID"]][$Column = "{$Entity}{$ColumnSuffix}"]) ? $Data[$Product["{$OptionEntity}ID"]][$Column] : 1) . "\" min=\"1\" style=\"width: 100px;\"></td>
				<td><input type=\"number\" name=\"{$Entity}" . ($ColumnSuffix = "CartonSerialTo") . "[]\" value=\"" . (isset($Data[$Product["{$OptionEntity}ID"]][$Column = "{$Entity}{$ColumnSuffix}"]) ? $Data[$Product["{$OptionEntity}ID"]][$Column] : 1) . "\" min=\"1\" style=\"width: 100px;\"></td>
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