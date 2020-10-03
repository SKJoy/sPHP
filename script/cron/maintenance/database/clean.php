<?php
namespace sPHP;

if(isset($sPHPCronJob)){ // In case if this script runs through sPHP Cron object
	$Table = $Resource["Table"];
	$Utility = $Resource["Utility"];
	$Configuration = $Resource["Configuration"];

	$Database = $Table[array_keys($Table)[0]]->Database();
}

$CheckpointTime = microtime(true);

$Recordset = $Database->Query("
	SET @HistoryMonthsToKeep := 3; # Keep data of past N months
	SET @TimeToKeepFrom := CONCAT(DATE_ADD(DATE_FORMAT(NOW(), '%Y-%m-01'), INTERVAL (-1) * @HistoryMonthsToKeep MONTH), ' 00:00:00');
	SET @ProcessTimeStart := NOW();

	SELECT @TimeToKeepFrom AS TimeToKeepFrom;

	# Panel: Maintenance: Clean: Application
	#DELETE FROM pnl_terminaldataminutely WHERE TerminalDataMinutelyTimeFirst < @TimeToKeepFrom LIMIT 9999;

	# Panel: Maintenance: Clean: System
	DELETE FROM sphp_notification WHERE TimeInserted < @TimeToKeepFrom LIMIT 9999;
	DELETE FROM sphp_userdevice WHERE TimeInserted < @TimeToKeepFrom LIMIT 9999;
	DELETE FROM sphp_useruserdevice WHERE UserUserDeviceTimeActiveLast < @TimeToKeepFrom LIMIT 9999;
	DELETE FROM sphp_useruserdevicenotification WHERE TimeInserted < @TimeToKeepFrom OR UserUserDeviceNotificationIsRead = 1 LIMIT 9999;

	# Clean up aged Application Traffic
	DELETE FROM		sphp_applicationtraffic
	WHERE			ApplicationTrafficTime < @TimeToKeepFrom
		OR			ApplicationTrafficScript LIKE 'cron/%'
		OR			ApplicationTrafficScript LIKE 'api/%'
		OR			ApplicationTrafficScript IN ('user/signout', 'user/signin', 'user/signinaction', 'home')
		OR			ApplicationTrafficIP IN (
						'127.0.0.1', # Local
						'202.191.121.174', # DHK.Binary.Men
						'203.83.174.106', # Singularity
						''
					)
	LIMIT 9999;

	SELECT 'Complete' AS Status, @ProcessTimeStart AS Begin, TIMEDIFF(NOW(), @ProcessTimeStart) AS Duration;
");

$CheckpointTime_Database = microtime(true);
$ProcessStatus = is_array($Recordset) && isset($Recordset[count($Recordset) - 1][0]["Status"]) ? "Complete" : "Failed";

if(isset($sPHPCronJob)){ // Resturn result if ran through sPHP Cron object
	$CronJobResult["Error"] = ["Code" => 0, "Message" => null, ];

	$CronJobResult["Status"] = [
		"Status: {$ProcessStatus}",
		"Begin: " . date("H:i:s", $CheckpointTime) . "",
		"End: " . date("H:i:s", $CheckpointTime_Database) . " (" . number_format($CheckpointTime_Database - $CheckpointTime, 3) . ")",
	];
}
else{ // Output only if not an sPHP Cron Job (service, background process)
	print "
		<h2>Database: Clean</h2>

		<ul>
			<li>Status: {$ProcessStatus}</li>
			<li>Begin: " . date("H:i:s", $CheckpointTime) . "</li>
			<li>End: " . date("H:i:s", $CheckpointTime_Database) . " (" . number_format($CheckpointTime_Database - $CheckpointTime, 3) . ")</li>
		</ul>
	";
}
?>