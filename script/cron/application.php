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
		new Cron\Job("{$Environment->ScriptPath()}cron/maintenance/database/clean.php", $DayInterval, null, "Database: Clean (application)"),

		// System managed
		new Cron\Job("{$Environment->SystemScriptPath()}cron/notification/send.php", $MinimumInterval, null, "Notification: Send"),
		new Cron\Job("{$Environment->SystemScriptPath()}cron/maintenance/database/clean.php", $DayInterval, null, "Database: Clean"),
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
	isset($_POST["Execute"]) ? null : $MinimumInterval, // Service iternation interval in seconds; Non positive value disables service mode
	null, // Custom EXIT command
	true // Verbose mode
);

if(isset($_POST["SetExit"]))$Cron->Command("EXIT");
if(isset($_POST["SetResume"]))$Cron->Command("RESUME");
//DebugDump($Cron);
print HTML\UI\MessageBox("{$CronProcessName}: " . ($Cron->Execute() ? "Success" : "Failed") . "", "Cron");
?>