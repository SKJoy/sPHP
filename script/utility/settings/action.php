<?php
	namespace sPHP;

	$Settings = new Content("System settings", null, $Environment->ContentPath());
	foreach(array_keys($Configuration) as $SettingName)if(isset($_POST[$SettingName]))$Value[$SettingName] = $_POST[$SettingName];

	$Value["AdministratorPasswordHash"] = $_POST["AdministratorPassword"] ? md5($_POST["AdministratorPassword"]) : (isset($Settings->Value()["AdministratorPasswordHash"]) ? $Settings->Value()["AdministratorPasswordHash"] : $Configuration["AdministratorPasswordHash"]);
	$Settings->Value($Value);

	print "" . HTML\UI\MessageBox(
		"Settings were saved. Click <a href=\"{$Application->URL("Utility/Settings/Input")}\">here</a> to continue.",
		"System"
	) . "";
?>