<?php
namespace sPHP;
$ErrorLogFile = "{$ENV->Path()}error.php.log";
$ErrorLogFileExists = file_exists($ErrorLogFile);

if($ErrorLogFileExists){
	$ErrorLogFileModificationTimeCaption = date("{$CFG["ShortDateFormat"]} {$CFG["TimeFormat"]}", filemtime($ErrorLogFile));

	if(isset($_POST["btnClear"])){
		unlink($ErrorLogFile);
		$ErrorLogFileExists = file_exists($ErrorLogFile);
		//print HTML\UI\MessageBox("PHP error log cleared.", "System");
	}
}
else{
	$ErrorLogFileModificationTimeCaption = "Never";
}

$ErrorLogContentElementID = "ErrorLogContent_" . rand() . "";
?>

<div class="Console">
	<div class="Title">
		PHP error log<?=$ErrorLogFileModificationTimeCaption == "Never" ? null : " : {$ErrorLogFileModificationTimeCaption}"?>

		<nav class="Action">
			<a href="<?=$APP->URL($_POST["_Script"], "")?>" class="Item" onclick="return true;"><img src="<?=$ENV->IconURL()?>refresh.png" alt="Clear" class="Icon">Reload</a>
			<a href="<?=$APP->URL($_POST["_Script"], "btnClear")?>" class="Item" onclick="return confirm('Are you sure to clear the PHP error log?');"><img src="<?=$ENV->IconURL()?>clear.png" alt="Clear" class="Icon">Clear</a>
		</nav>
	</div>

	<div id="<?=$ErrorLogContentElementID?>" class="Content"><?=$ErrorLogFileExists ? file_get_contents($ErrorLogFile) : null?></div>
	<div class="Footer">Last error: <?=$ErrorLogFileModificationTimeCaption?></div>
</div>

<script>
	var ErrorLogContentElement = document.getElementById('<?=$ErrorLogContentElementID?>');
	ErrorLogContentElement.scrollTo(0, ErrorLogContentElement.scrollHeight);
</script>