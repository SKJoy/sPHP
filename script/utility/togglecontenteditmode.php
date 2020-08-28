<?php
	namespace sPHP;

	$Session->ContentEditMode(!$Session->ContentEditMode());
	$Terminal->Redirect(null, "Content edit mode has been " . ($Session->ContentEditMode() ? "enabled" : "disabled") . ".");
?>