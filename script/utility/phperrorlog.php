<?php
namespace sPHP;
$ErrorLogFile = "{$Environment->Path()}error.php.log";
$ErrorLogFileExists = file_exists($ErrorLogFile);

if($ErrorLogFileExists && isset($_POST["btnClear"])){
	unlink($ErrorLogFile);
	$ErrorLogFileExists = file_exists($ErrorLogFile);
	//print HTML\UI\MessageBox("PHP error log cleared.", "System");
}
?>

<h1>PHP error log</h1>

<div class="Console">
	<div class="Title">
		PHP error log

		<nav class="Action">
			<a href="<?=$Application->URL($_POST["_Script"], "btnClear")?>" class="Item" onclick="return confirm('Are you sure to clear the PHP error log?');"><img src="<?=$Environment->IconURL()?>clear.png" alt="Clear" class="Icon">Clear</a>
		</nav>
	</div>

	<div class="Content"><?=$ErrorLogFileExists ? file_get_contents($ErrorLogFile) : null?></div>
	<div class="Footer">Last error: <?=$ErrorLogFileExists ? date("{$Configuration["ShortDateFormat"]} {$Configuration["TimeFormat"]}", filemtime($ErrorLogFile)) : "Never"?></div>
</div>