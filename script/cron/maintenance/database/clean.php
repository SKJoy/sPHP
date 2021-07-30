<?php
namespace sPHP;

if(isset($sPHPCronJob)){ // In case if this script runs through sPHP Cron object
	$Table = \sPHP::$Table;
	$Utility = \sPHP::$Utility;
	$Configuration = \sPHP::$Configuration;

	$Database = $Table[array_keys($Table)[0]]->Database();
}

$DateTimeFormat = "{$Configuration["ShortDateFormat"]} {$Configuration["TimeFormat"]}";

$Recordset = $Database->Query("
	# Argument
		SET @HistoryMonthsToKeep := 3; # Keep data of past N months
		SET @HistoryDaysToKeep := {$Configuration["HistoricDataExpiryDay"]}; # Keep data of past N days

	# Parameter
		SET @ProcessTimeStart := NOW();
		
		#SET @TimeToKeepFrom := CONCAT(DATE_ADD(DATE_FORMAT(@ProcessTimeStart, '%Y-%m-01'), INTERVAL (-1) * @HistoryMonthsToKeep MONTH), ' 00:00:00');
		SET @TimeToKeepFrom := CONCAT(DATE_ADD(DATE_FORMAT(@ProcessTimeStart, '%Y-%m-01'), INTERVAL (-1) * @HistoryDaysToKeep DAY), ' 00:00:00');

	# Aged
		DELETE FROM sphp_notification WHERE TimeInserted < @TimeToKeepFrom LIMIT 9999;
		DELETE FROM sphp_userdevice WHERE TimeInserted < @TimeToKeepFrom LIMIT 9999;
		DELETE FROM sphp_useruserdevice WHERE UserUserDeviceTimeActiveLast < @TimeToKeepFrom LIMIT 9999;
		DELETE FROM sphp_useruserdevicenotification WHERE TimeInserted < @TimeToKeepFrom OR UserUserDeviceNotificationIsRead = 1 LIMIT 9999;
		DELETE FROM sphp_log WHERE TimeInserted < @TimeToKeepFrom LIMIT 9999;

	# Orphan
		DELETE UUG FROM	sphp_userusergroup AS UUG
			LEFT JOIN	sphp_user AS U ON U.UserID = UUG.UserID
			LEFT JOIN	sphp_usergroup AS UG ON UG.UserGroupID = UUG.UserGroupID
		WHERE			U.UserID IS NULL
			OR			UG.UserGroupID IS NULL;

	# Application traffic
		DELETE FROM		sphp_applicationtraffic
		WHERE			ApplicationTrafficTime < @TimeToKeepFrom
			OR			ApplicationTrafficScript LIKE 'cron/%'
			OR			ApplicationTrafficScript LIKE 'api/%'
			OR			ApplicationTrafficScript IN ('user/signout', 'user/signin', 'user/signinaction', 'home')
			OR			ApplicationTrafficIP IN (
							'127.0.0.1', # Local
							'::'
						)
		;

	# Status
		SELECT			@HistoryMonthsToKeep AS HistoryMonthsToKeep, 
						@HistoryDaysToKeep AS HistoryDaysToKeep, 
						@TimeToKeepFrom AS TimeToKeepFrom, 
						@ProcessTimeStart AS ProcessTimeStart, 
						TIMEDIFF(NOW(), @ProcessTimeStart) AS ProcessDuration
		;
");

if(is_array($Recordset) && count($Recordset)){
	$ProcessStatus = $Recordset[0][0];
	$TimeToKeepFromCaption = date($DateTimeFormat, strtotime($ProcessStatus["TimeToKeepFrom"]));
	$ProcessTimeStartCaption = date($DateTimeFormat, strtotime($ProcessStatus["ProcessTimeStart"]));

	if(isset($sPHPCronJob)){ // Resturn result if ran through sPHP Cron object
		$CronJobResult["Error"] = ["Code" => 0, "Message" => null, ];	
	
		$CronJobResult["Status"] = [
			"Keep from: {$TimeToKeepFromCaption}",
		];
	}
	else{ // Output only if not an sPHP Cron Job (service, background process)
		print "
			<h2>System: Database: Clean</h2>
	
			<ul>
				<li>Keep from: {$TimeToKeepFromCaption}</li>
				<li>Begin: {$ProcessTimeStartCaption}</li>
				<li>Duration: {$ProcessStatus["ProcessDuration"]}</li>
			</ul>
		";
	}
}
else{
	if(isset($sPHPCronJob)){ // Resturn result if ran through sPHP Cron object
		$CronJobResult["Error"] = ["Code" => -1, "Message" => "Database query failed", ];
	}
	else{ // Output only if not an sPHP Cron Job (service, background process)
		print "
			<h2>System: Database: Clean</h2>
	
			<ul>
				<li>Error: Database query failed</li>
			</ul>
		";
	}
}
?>