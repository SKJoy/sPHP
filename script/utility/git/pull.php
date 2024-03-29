<?php
namespace sPHP;

$TimeBegin = microtime(true);
$Path = substr($Environment->Path(), 0, strlen($Environment->Path()) - 1);
$Command = "git pull";
$Output = $Status = null;

chdir($Path); // Remove the trailing slash
exec($Command, $Output, $Status);
//$Output = shell_exec($Command);
//system($Command, $Status);

print "<h1><img src=\"{$Environment->IconURL()}git_pull.png\" alt=\"Cron\" class=\"Icon\"> GIT pull: Update from GIT repository</h1>";
$TimeEnd = microtime(true);

DebugDump([
	"Path" => $Path, 
	"Command" => $Command, 
	"Output" => $Output, 
	"Status" => $Status, 
	"Time" => [
		"Begin" => date("r", $TimeBegin), 
		"End" => date("r", $TimeEnd), 
		"Duration" => str_pad($TimeEnd - $TimeBegin, 10, "0", STR_PAD_LEFT), 
	], 
]);
?>