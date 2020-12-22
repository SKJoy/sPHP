<?php
namespace sPHP;

$Path = substr($Environment->Path(), 0, strlen($Environment->Path()) - 1);
$Command = "git pull > /dev/null &";
$Output = $Status = null;
$TimeBegin = microtime(true);

chdir($Path); // Remove the trailing slash
exec($Command, $Output, $Status);

$TimeEnd = microtime(true);
print "<h1>GIT pull: Update from GIT repository</h1>";

DebugDump([
	"Path" => $Path, 
	"Command" => $Command, 
	"Output" => $Output, 
	"Status" => $Status, 
	"Time" => [
		"Begin" => date("r", $TimeBegin), 
		"End" => date("r", $TimeEnd), 
		"Duration" => date("H:i:s", $TimeEnd - $TimeBegin), 
	], 
]);
?>