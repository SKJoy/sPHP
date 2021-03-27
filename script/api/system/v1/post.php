<?php
namespace sPHP;

// Append process timing to Diagnostics node
$ProcessTimeStop = microtime(true);
$APIResponse["Diagnostics"]["Request"]["Time"] = ["Start" => date("Y-m-d H:i:s", $ProcessTimeStart), "Stop" => date("Y-m-d H:i:s", $ProcessTimeStop), "Duration MS" => $ProcessTimeStop - $ProcessTimeStart, ];

print json_encode($APIResponse);
?>