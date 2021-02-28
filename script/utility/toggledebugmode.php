<?php
	namespace sPHP;

	$SSN->DebugMode(!$SSN->DebugMode());
	$TRM->Redirect(null, "Debug mode has been " . ($SSN->DebugMode() ? "enabled" : "disabled") . ".");
?>