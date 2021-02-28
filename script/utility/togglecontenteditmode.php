<?php
	namespace sPHP;

	$SSN->ContentEditMode(!$SSN->ContentEditMode());
	$TRM->Redirect(null, "Content edit mode has been " . ($SSN->ContentEditMode() ? "enabled" : "disabled") . ".");
?>