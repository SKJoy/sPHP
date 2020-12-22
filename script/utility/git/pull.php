<?php
namespace sPHP;

$Command = "git pull > /dev/null &";
$Output = $Status = null;

exec($Command, $Output, $Status);

DebugDump([
	"Command" => $Command, 
	"Output" => $Output, 
	"Status" => $Status, 
]);
?>