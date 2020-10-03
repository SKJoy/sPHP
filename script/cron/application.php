<?php
namespace sPHP;
$CronProcessName = "Application";
$MinimumInterval = 3; // Seconds
$MinuteInterval = 60; // Seconds
$HourInterval = 3600; // Seconds
$DayInterval = 86400; // Seconds

$Cron = new Cron( // Cron job (service)
	$Environment->ScriptPath(), // Process base path for status & command files
	$CronProcessName, // Unique process name
	[ // Jobs
		// Application managed
		new Cron\Job("{$Environment->ScriptPath()}cron/maintenance/database/clean.php", $HourInterval, null, "Database: Clean (application)"),

		// System managed
		new Cron\Job("{$Environment->SystemScriptPath()}cron/notification/send.php", $Configuration["CronNotificationInterval"], null, "Notification: Send"),
		new Cron\Job("{$Environment->SystemScriptPath()}cron/maintenance/database/clean.php", $DayInterval, null, "Database: Clean (system)"),
		new Cron\Job("{$Environment->SystemScriptPath()}cron/maintenance/applicationtraffic/geolocation/update.php", $HourInterval, null, "Application traffic: GeoLocation: Update"),

		// Other
		//new Cron\Job("HTTP://YBX.BondStein.ComX", 5, [], "Call an URL"),
		//new Cron\Job("dir > dir.txt", 15, null, "Execute SHELL command"),
	],
	[ // Resources to pass to Jobs of type PHP script
		"Table" => $Table,
		"Utility" => $Utility,
		"Configuration" => $Configuration,
	],
	$Configuration["CronMaximumExecutionTime"], // Maximum execution time in seconds
	SetVariable("Command") == "EXECUTE" ? null : $MinimumInterval, // Service iternation interval in seconds; Non positive value disables service mode
	null, // Custom EXIT command
	true // Verbose mode
); //DebugDump($Cron);

if(in_array($_POST["Command"], ["EXIT", "RESUME"])){
	$Cron->Command($_POST["Command"]);
	print HTML\UI\MessageBox("{$CronProcessName}: Command: {$_POST["Command"]}", "Cron");
}
else{
	print HTML\UI\MessageBox("{$CronProcessName}: " . ($Cron->Execute() ? "Success" : "Failed") . "", "Cron");
}
?>