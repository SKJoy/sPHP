<?php
namespace sPHP;

$ErrorLogFile = "{$ENV->LogPath()}error/database.json";
$ErrorLogFileExists = file_exists($ErrorLogFile);
$ErrorLog = $ErrorLogFileExists ? json_decode(file_get_contents($ErrorLogFile)) : "No error";
?>

<h1>Database error log<?=$ErrorLogFileExists ? ": " . date("{$Configuration["ShortDateFormat"]} g:i:s A", filemtime($ErrorLogFile)) . "" : null?></h1>
<?php DebugDump($ErrorLog)?>