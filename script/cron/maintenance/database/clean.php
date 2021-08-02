<?php
namespace sPHP;

if(isset($sPHPCronJob)){ // In case if this script runs through sPHP Cron object
	$Table = \sPHP::$Table;
	$Utility = \sPHP::$Utility;
	$Configuration = \sPHP::$Configuration;

	$Database = $Table[array_keys($Table)[0]]->Database();
}

$Recordset = $Database->Query("
	# Argument
		SET @HistoryDaysToKeep := {$Configuration["HistoricDataExpiryDay"]}; # Keep data of past N days

	# Parameter
		SET @ProcessTimeStart := NOW();
		SET @TimeToKeepFrom := CONCAT(DATE_ADD(DATE_FORMAT(@ProcessTimeStart, '%Y-%m-01'), INTERVAL (-1) * @HistoryDaysToKeep DAY), ' 00:00:00');

	# Aged
		DELETE FROM sphp_notification WHERE TimeInserted < @TimeToKeepFrom LIMIT 9999;
		DELETE FROM sphp_userdevice WHERE TimeInserted < @TimeToKeepFrom LIMIT 9999;
		DELETE FROM sphp_useruserdevice WHERE UserUserDeviceTimeActiveLast < @TimeToKeepFrom LIMIT 9999;
		DELETE FROM sphp_useruserdevicenotification WHERE TimeInserted < @TimeToKeepFrom OR UserUserDeviceNotificationIsRead = 1 LIMIT 9999;
		DELETE FROM sphp_log WHERE TimeInserted < @TimeToKeepFrom LIMIT 9999;

	# Orphan
		DELETE UUG FROM	sphp_userusergroup AS UUG LEFT JOIN sphp_user AS U ON U.UserID = UUG.UserID LEFT JOIN sphp_usergroup AS UG ON UG.UserGroupID = UUG.UserGroupID WHERE U.UserID IS NULL OR UG.UserGroupID IS NULL;

	# Application traffic
		DELETE FROM		sphp_applicationtraffic
		WHERE			ApplicationTrafficTime < @TimeToKeepFrom
			OR			ApplicationTrafficScript LIKE 'cron/%'
			OR			ApplicationTrafficScript LIKE 'api/%'
			OR			ApplicationTrafficScript IN ('user/signout', 'user/signin', 'user/signinaction', 'home')
			OR			ApplicationTrafficIP IN (
							'127.0.0.1', # Local IPv4
							'::' # Local IPv6
						)
		;

	# Status
		SELECT			@HistoryDaysToKeep AS HistoryDaysToKeep, 
						@HistoryMonthsToKeep AS HistoryMonthsToKeep, 
						@TimeToKeepFrom AS TimeToKeepFrom, 
						@ProcessTimeStart AS ProcessTimeStart, 
						TIMEDIFF(NOW(), @ProcessTimeStart) AS ProcessDuration
		;
");

$DateTimeFormat = "" . \sPHP::$CFG["ShortDateFormat"] . " " . \sPHP::$CFG["TimeFormat"] . "";

if(is_array($Recordset) && count($Recordset)){
	$CronJobResult["Error"] = ["Code" => 0, "Message" => null, ];
	$ProcessStatus = $Recordset[count($Recordset) - 1][0];

	$TimeToKeepFromCaption = date($DateTimeFormat, strtotime($ProcessStatus["TimeToKeepFrom"]));
	$ProcessTimeStartCaption = date($DateTimeFormat, strtotime($ProcessStatus["ProcessTimeStart"]));
}
else{
	$CronJobResult["Error"] = ["Code" => 1, "Message" => "Database query failed", ];
}

if(isset($sPHPCronJob)){ // Resturn result if ran through sPHP Cron object
	if($CronJobResult["Error"]["Code"] == 0){
		$CronJobResult["Status"] = [
			"Keep from: <span class=\"Important\">{$TimeToKeepFromCaption}</span>", 
			"History days: {$ProcessStatus["HistoryDaysToKeep"]}", 
		];
	}
}
else{ // Output only if not an sPHP Cron Job (service, background process)
	if($CronJobResult["Error"]["Code"] == 0){
		$HTML[] = "<li>Keep from: {$TimeToKeepFromCaption}</li>";
		$HTML[] = "<li>History days: {$ProcessStatus["HistoryDaysToKeep"]} Sec</li>";
	}
	else{
		$HTML[] = "<li>ERROR: {$CronJobResult["Error"]["Message"]}</li>";
	}

	print "
		<h2>System: Database: Clean</h2>
		<ul>" . implode(null, $HTML) . "</ul>
	";
}
?>