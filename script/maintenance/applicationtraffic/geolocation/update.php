<?php
namespace sPHP;

if(isset($sPHPCronJob)){ // In case if this script runs through sPHP Cron object
	$Table = $Resource["Table"];
	$Utility = $Resource["Utility"];
	$Database = $Table[array_keys($Table)[0]]->Database();
}

$Entity = "ApplicationTraffic";
$RecordCount = 999;
$CheckpointTime = microtime(true);
$SQL = [];

foreach($Table[$Entity]->Get("ATr.{$Entity}Latitude IS NULL", "ATr.{$Entity}Time DESC", 1, $RecordCount) as $Record){
	if($GeoData = $Utility->IP2Geo($Record["{$Entity}IP"])){
		$Metro = $GeoData->location->metroCode ? "'{$Database->Escape($GeoData->location->metroCode)}'" : "NULL";
		$City = $GeoData->location->metroCode ? "'{$Database->Escape($GeoData->city->names["en"])}'" : "NULL";
		$PostCode = $GeoData->location->metroCode ? "'{$Database->Escape($GeoData->postal->code)}'" : "NULL";
		$Country = $GeoData->location->metroCode ? "'{$Database->Escape($GeoData->country->names["en"])}'" : "NULL";

		$UPDATESQL = "UPDATE sphp_applicationtraffic SET {$Entity}Latitude = {$GeoData->location->latitude}, {$Entity}Longitude = {$GeoData->location->longitude}, {$Entity}Metro = {$Metro}, {$Entity}City = {$City}, {$Entity}PostCode = {$PostCode}, {$Entity}Country = {$Country}";
	}
	else{
		$UPDATESQL = "UPDATE sphp_applicationtraffic SET {$Entity}Latitude = 0, {$Entity}Longitude = 0";
	}

	$SQL[] = "{$UPDATESQL} WHERE {$Entity}ID = {$Record["{$Entity}ID"]};";
}

$CheckpointTime_PHP = microtime(true);

if(count($SQL)){
	$FullSQL = implode("\n", $SQL);
	$Database->Query($FullSQL);
	//print "<pre>{$FullSQL}</pre>";
}

$CheckpointTime_Database = microtime(true);

// Output only if not an sPHP Cron Job (service, background process)
if(!isset($sPHPCronJob))print "
	<h2>Application traffic: GeoLocation: Update</h2>

	<ul>
		<li>Count: {$RecordCount} (" . count($SQL) . ")</li>
		<li>Begin: " . date("H:i:s", $CheckpointTime) . "</li>
		<li>PHP: " . date("H:i:s", $CheckpointTime_PHP) . " (" . number_format($CheckpointTime_PHP - $CheckpointTime, 3) . ")</li>
		<li>Database: " . date("H:i:s", $CheckpointTime_Database) . " (" . number_format($CheckpointTime_Database - $CheckpointTime_PHP, 3) . ")</li>
		<li>End: " . date("H:i:s", $CheckpointTime_Database) . " (" . number_format($CheckpointTime_Database - $CheckpointTime, 3) . ")</li>
	</ul>
";


if(isset($sPHPCronJob)){ // Resturn result if ran through sPHP Cron object
	$CronJobResult = ["Error" => [], "Count" => $RecordCount, ];
}
?>