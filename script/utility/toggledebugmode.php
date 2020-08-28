<?php
	namespace sPHP;

	$Session->DebugMode(!$Session->DebugMode());
	$Terminal->Redirect(null, "Debug mode has been " . ($Session->DebugMode() ? "enabled" : "disabled") . ".");
?>