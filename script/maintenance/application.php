<?php
namespace sPHP;
$CronProcessName = "Application maintenance";

$Cron = new Cron( // Cron job (service)
	$Environment->ScriptPath(), // Process base path for status & command files
	$CronProcessName, // Unique process name
	[ // Jobs
		new Cron\Job("{$Environment->SystemScriptPath()}maintenance/applicationtraffic/geolocation/update.php", 60, null, "Application traffic: GeoLocation: Update"),
		//new Cron\Job("HTTP://YBX.BondStein.ComX", 5, [], "Call an URL"),
		//new Cron\Job("dir > dir.txt", 15, null, "Execute SHELL command"),
	],
	[ // Resources to pass to Jobs of type PHP script
		"Table" => $Table,
		"Utility" => $Utility,
	],
	3600, // Maximum execution time in seconds
	5, // Service iternation interval in seconds; Non positive value disables service mode
	null, // Custom EXIT command
	true // Verbose mode
);

//$Cron->Command("RESUME"); // Set command
//DebugDump($Cron->Execute()); // Execute
//DebugDump($Cron);

print HTML\UI\MessageBox("{$CronProcessName}: " . ($Cron->Execute() ? "Success" : "Failed") . "", "Cron");
?>