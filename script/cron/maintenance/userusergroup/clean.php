<?php
namespace sPHP;

if(isset($sPHPCronJob)){ // In case if this script runs through sPHP Cron object
	$Table = $Resource["Table"];
	$Utility = $Resource["Utility"];
	$Configuration = $Resource["Configuration"];

	$Database = $Table[array_keys($Table)[0]]->Database();
}

$Entity = "UserUserGroup";
$CheckpointTime = microtime(true);

$Database->Query("
	DELETE
	FROM			sphp_userusergroup
	WHERE			UserID NOT IN (SELECT U.UserID FROM sphp_user AS U)
		OR			UserGroupID NOT IN (SELECT UG.UserGroupID FROM sphp_usergroup AS UG)
");

if(isset($sPHPCronJob)){ // Resturn result if ran through sPHP Cron object
	$CronJobResult["Error"] = ["Code" => 0, "Message" => null, ];

	$CronJobResult["Status"] = [
		"Begin: " . date("H:i:s", $CheckpointTime) . "",
		"End: " . date("H:i:s") . " (" . number_format(microtime(true) - $CheckpointTime, 3) . ")",
	];
}
else{ // Output only if not an sPHP Cron Job (service, background process)
	print "
		<h2>User user group: Clean</h2>

		<ul>
			<li>Begin: " . date("H:i:s", $CheckpointTime) . "</li>
			<li>End: " . date("H:i:s") . " (" . number_format(microtime(true) - $CheckpointTime, 3) . ")</li>
		</ul>
	";
}
?>