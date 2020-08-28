<?php
	namespace sPHP;

	// Load user saved settings values
	$CustomSetting = new Content("System settings", null, $Environment->ContentPath());

	// Merge custom settings values with default configuration values to let new setting value be set
	foreach($Configuration as $Key=>$Value)$Setting[$Key] = isset($CustomSetting->Value()[$Key]) ? $CustomSetting->Value()[$Key] : $Value;

	$NumberInputWidth = 80;

	$TimeZoneOption[] = new Option("-12:00");
	$TimeZoneOption[] = new Option("-11:30");
	$TimeZoneOption[] = new Option("-11:00");
	$TimeZoneOption[] = new Option("-10:30");
	$TimeZoneOption[] = new Option("-10:00");
	$TimeZoneOption[] = new Option("-09:30");
	$TimeZoneOption[] = new Option("-09:00");
	$TimeZoneOption[] = new Option("-08:30");
	$TimeZoneOption[] = new Option("-08:00");
	$TimeZoneOption[] = new Option("-07:30");
	$TimeZoneOption[] = new Option("-07:00");
	$TimeZoneOption[] = new Option("-06:30");
	$TimeZoneOption[] = new Option("-06:00");
	$TimeZoneOption[] = new Option("-05:30");
	$TimeZoneOption[] = new Option("-05:00");
	$TimeZoneOption[] = new Option("-04:30");
	$TimeZoneOption[] = new Option("-04:00");
	$TimeZoneOption[] = new Option("-03:30");
	$TimeZoneOption[] = new Option("-03:00");
	$TimeZoneOption[] = new Option("-02:30");
	$TimeZoneOption[] = new Option("-02:00");
	$TimeZoneOption[] = new Option("-01:30");
	$TimeZoneOption[] = new Option("-01:00");
	$TimeZoneOption[] = new Option("-00:30");
	$TimeZoneOption[] = new Option("00:00", "00:00 GMT");
	$TimeZoneOption[] = new Option("+00:30");
	$TimeZoneOption[] = new Option("+01:00");
	$TimeZoneOption[] = new Option("+01:30");
	$TimeZoneOption[] = new Option("+02:00");
	$TimeZoneOption[] = new Option("+02:30");
	$TimeZoneOption[] = new Option("+03:00");
	$TimeZoneOption[] = new Option("+03:30");
	$TimeZoneOption[] = new Option("+04:00");
	$TimeZoneOption[] = new Option("+04:30");
	$TimeZoneOption[] = new Option("+05:00");
	$TimeZoneOption[] = new Option("+05:30");
	$TimeZoneOption[] = new Option("+06:00", "+06:00 Bangladesh, Bhutan, Kazakhstan");
	$TimeZoneOption[] = new Option("+06:30");
	$TimeZoneOption[] = new Option("+07:00");
	$TimeZoneOption[] = new Option("+07:30");
	$TimeZoneOption[] = new Option("+08:00");
	$TimeZoneOption[] = new Option("+08:30");
	$TimeZoneOption[] = new Option("+09:00");
	$TimeZoneOption[] = new Option("+09:30");
	$TimeZoneOption[] = new Option("+10:00");
	$TimeZoneOption[] = new Option("+10:30");
	$TimeZoneOption[] = new Option("+11:00");
	$TimeZoneOption[] = new Option("+11:30");
	$TimeZoneOption[] = new Option("+12:00", "+12:00 Fiji, Marshal Islands, New Zealand");

	print "<div class=\"AlignCenter\">" . HTML\UI\Form(
		$Application->URL("Utility/Settings/Action"), // Action URL
		// Content
		"
			<div class=\"SectionTitle\">Administrator</div>
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Administrator" . ($Caption = "Email") . "", $Configuration["InputFullWidth"], $Setting[$SettingName], true, INPUT_TYPE_EMAIL), "{$Caption}*", null, null, $Configuration["FieldCaptionWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Administrator" . ($Caption = "Password") . "", $Configuration["InputFullWidth"]), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"], null, null, null, "Leave blank to retain existing password") . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Administrator" . ($Caption = "Name") . "", $Configuration["InputFullWidth"], $Setting[$SettingName], true), "{$Caption}*", true, null, $Configuration["FieldCaptionWidth"]) . "

			<div class=\"SectionTitle\">Company</div>
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Company" . ($Caption = "Name") . "", $Configuration["InputFullWidth"], $Setting[$SettingName]), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Company" . ($Caption = "Address") . "", $Configuration["InputFullWidth"], $Setting[$SettingName]), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Company" . ($Caption = "Phone") . "", $Configuration["InputWidth"], $Setting[$SettingName], null, INPUT_TYPE_PHONE), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"], $Configuration["FieldContentWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Company" . ($Caption = "Email") . "", $Configuration["InputFullWidth"], $Setting[$SettingName], null, INPUT_TYPE_EMAIL), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Company" . ($Caption = "URL") . "", $Configuration["InputFullWidth"], $Setting[$SettingName], null, INPUT_TYPE_URL), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "

			<div class=\"SectionTitle\">Application</div>
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "" . ($Caption = "Name") . "", $Configuration["InputFullWidth"], $Setting[$SettingName], true), "{$Caption}*", true, null, $Configuration["FieldCaptionWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "" . ($Caption = "Short") . "Name", $Configuration["InputWidth"], $Setting[$SettingName]), "{$Caption} name", true, null, $Configuration["FieldCaptionWidth"], $Configuration["FieldContentWidth"]) . "

			<div class=\"SectionTitle\">Title</div>
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "" . ($Caption = "Title") . "", $Configuration["InputWidth"], $Setting[$SettingName]), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"], $Configuration["FieldContentWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Title" . ($Caption = "Prefix") . "", $Configuration["InputInlineWidth"], $Setting[$SettingName]), "{$Caption}", null, true, $Configuration["FieldCaptionWidth"], $Configuration["FieldContentInlineWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Title" . ($Caption = "Suffix") . "", $Configuration["InputWidth"], $Setting[$SettingName]), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"], $Configuration["FieldContentWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Title" . ($Caption = "Seperator") . "", $Configuration["InputInlineWidth"], $Setting[$SettingName]), "{$Caption}", null, true, $Configuration["FieldCaptionWidth"], $Configuration["FieldContentInlineWidth"]) . "

			<div class=\"SectionTitle\">Meta</div>
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "" . ($Caption = "Description") . "", $Configuration["InputFullWidth"], $Setting[$SettingName]), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "" . ($Caption = "Keyword") . "", $Configuration["InputFullWidth"], $Setting[$SettingName]), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "

			<div class=\"SectionTitle\">Database</div>
			" . HTML\UI\Field(HTML\UI\Select($SettingName = "Database" . ($Caption = "Type") . "", [new Option("MYSQL", "MySQL"), new Option("MSSQL", "Microsoft SQL Server 2014+")], null, null, null, null, $Setting[$SettingName]), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"], null, null, null, null) . "
			" . HTML\UI\Field(HTML\UI\Select($SettingName = "Database" . ($Caption = "Time") . "zone", $TimeZoneOption, null, null, null, null, $Setting[$SettingName]), "{$Caption} zone", null, true, null, null, null, null, "Should match application time zone") . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Database" . ($Caption = "Host") . "", $Configuration["InputInlineWidth"], $Setting[$SettingName]), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Database" . ($Caption = "Name") . "", $Configuration["InputInlineWidth"], $Setting[$SettingName]), "{$Caption}", null, true, $Configuration["FieldCaptionInlineWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Database" . ($Caption = "User") . "", $Configuration["InputWidth"], $Setting[$SettingName]), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Database" . ($Caption = "Password") . "", $Configuration["InputInlineWidth"], $Setting[$SettingName]), "{$Caption}", null, true, $Configuration["FieldCaptionInlineWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Database" . ($Caption = "ODBC") . "Driver", $Configuration["InputFullWidth"], $Setting[$SettingName]), "{$Caption} driver", true, null, $Configuration["FieldCaptionWidth"], null, null, null, "e.g.: SQL Server Native Client 11.0. Doesn't work with MySQL database type") . "

			<div class=\"SectionTitle\">Head</div>
			" . HTML\UI\Field(HTML\UI\Textarea($SettingName = "" . ($Caption = "HTML") . "HeadCode", $Configuration["InputFullWidth"], 100, $Setting[$SettingName]), "{$Caption} head code", true, null, $Configuration["FieldCaptionWidth"]) . "

			<div class=\"SectionTitle\">Interface</div>
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Field" . ($Caption = "Caption") . "Width", $NumberInputWidth, $Setting[$SettingName], true, INPUT_TYPE_NUMBER), "Field: {$Caption} width", true, null, $Configuration["FieldCaptionWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Field" . ($Caption = "Caption") . "InlineWidth", $NumberInputWidth, $Setting[$SettingName], true, INPUT_TYPE_NUMBER), "{$Caption} inline width", null, true, null) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Field" . ($Caption = "Content") . "Width", $NumberInputWidth, $Setting[$SettingName], null, INPUT_TYPE_NUMBER), "Field: {$Caption} width", true, null, $Configuration["FieldCaptionWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Field" . ($Caption = "Content") . "InlineWidth", $NumberInputWidth, $Setting[$SettingName], null, INPUT_TYPE_NUMBER), "{$Caption} inline width", null, true, null) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Field" . ($Caption = "Content") . "FullWidth", $NumberInputWidth, $Setting[$SettingName], null, INPUT_TYPE_NUMBER), "{$Caption} full width", null, true, null) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Input" . ($Caption = "Width") . "", $NumberInputWidth, $Setting[$SettingName], true, INPUT_TYPE_NUMBER), "Input: {$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Input" . ($Caption = "Inline") . "Width", $NumberInputWidth, $Setting[$SettingName], true, INPUT_TYPE_NUMBER), "{$Caption} width", null, true, null) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Input" . ($Caption = "Full") . "Width", $NumberInputWidth, $Setting[$SettingName], true, INPUT_TYPE_NUMBER), "{$Caption} width", null, true, null) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Input" . ($Caption = "Date") . "Width", $NumberInputWidth, $Setting[$SettingName], true, INPUT_TYPE_NUMBER), "{$Caption} width", null, true, null) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "" . ($Caption = "Textarea") . "Height", $NumberInputWidth, $Setting[$SettingName], true, INPUT_TYPE_NUMBER), "{$Caption} height", true, null, $Configuration["FieldCaptionWidth"]) . "

			<div class=\"SectionTitle\">Security</div>
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "" . ($Caption = "Encryption") . "Key", $Configuration["InputFullWidth"], $Setting[$SettingName], true), "{$Caption} key*", true, null, $Configuration["FieldCaptionWidth"], null, null, null, "<span class=\"Warning\">Careful! Repeatative changes may adversly impact application security.</span>") . "
			" . HTML\UI\Field(HTML\UI\RadioGroup($SettingName = "" . ($Caption = "Use") . "SystemScript", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")], $Setting[$SettingName]), "{$Caption} system script", true, null, $Configuration["FieldCaptionWidth"], $Configuration["FieldContentInlineWidth"]) . "

			<div class=\"SectionTitle\">SMTP Email</div>
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "SMTP" . ($Caption = "Body") . "Style", $Configuration["InputFullWidth"], $Setting[$SettingName]), "{$Caption} style", true, null, $Configuration["FieldCaptionWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "SMTP" . ($Caption = "Host") . "", $Configuration["InputFullWidth"], $Setting[$SettingName]), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "SMTP" . ($Caption = "Port") . "", $NumberInputWidth, $Setting[$SettingName], null, INPUT_TYPE_NUMBER), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"], $Configuration["FieldContentWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "SMTP" . ($Caption = "User") . "", $Configuration["InputFullWidth"], $Setting[$SettingName]), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "SMTP" . ($Caption = "Password") . "", $Configuration["InputFullWidth"], $Setting[$SettingName]), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "

			<div class=\"SectionTitle\">Content</div>
			" . HTML\UI\Field(HTML\UI\RadioGroup($SettingName = "Content" . ($Caption = "Edit") . "Mode", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")], $Setting[$SettingName]), "{$Caption} mode", true, null, $Configuration["FieldCaptionWidth"], null, null, null, "<span class=\"Warning\">Overrides session if YES</span>") . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Content" . ($Caption = "Edit") . "ModeServer", $Configuration["InputFullWidth"], $Setting[$SettingName]), "{$Caption} mode server", true, null, $Configuration["FieldCaptionWidth"], null, null, null, "<span class=\"Warning\">Overrides session if evaluates TRUE</span>") . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Content" . ($Caption = "Edit") . "ModeClient", $Configuration["InputFullWidth"], $Setting[$SettingName]), "{$Caption} mode client", true, null, $Configuration["FieldCaptionWidth"], null, null, null, "<span class=\"Warning\">Overrides session if evaluates TRUE</span>") . "

			<div class=\"SectionTitle\">Debug</div>
			" . HTML\UI\Field(HTML\UI\RadioGroup($SettingName = "" . ($Caption = "Custom") . "Error", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")], $Setting[$SettingName]), "{$Caption} error", true, null, $Configuration["FieldCaptionWidth"]) . "
			" . HTML\UI\Field(HTML\UI\RadioGroup($SettingName = "Debug" . ($Caption = "Mode") . "", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")], $Setting[$SettingName]), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"], null, null, null, "<span class=\"Warning\">Overrides session if YES</span>") . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "DebugMode" . ($Caption = "Server") . "", $Configuration["InputFullWidth"], $Setting[$SettingName]), "{$Caption} list", true, null, $Configuration["FieldCaptionWidth"], null, null, null, "<span class=\"Warning\">Overrides session if evaluates TRUE</span>") . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "DebugMode" . ($Caption = "Client") . "", $Configuration["InputFullWidth"], $Setting[$SettingName]), "{$Caption} list", true, null, $Configuration["FieldCaptionWidth"], null, null, null, "<span class=\"Warning\">Overrides session if evaluates TRUE</span>") . "

			<div class=\"SectionTitle\">Session</div>
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Session" . ($Caption = "Life") . "time", $NumberInputWidth, $Setting[$SettingName], true, INPUT_TYPE_NUMBER), "{$Caption} time*", true, null, $Configuration["FieldCaptionWidth"], $Configuration["FieldContentWidth"]) . "
			" . HTML\UI\Field(HTML\UI\RadioGroup($SettingName = "Session" . ($Caption = "Isolate") . "", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")], $Setting[$SettingName]), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "

			<div class=\"SectionTitle\">Interface</div>
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "" . ($Caption = "Viewport") . "", $Configuration["InputFullWidth"], $Setting[$SettingName]), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "

			<div class=\"SectionTitle\">Language</div>
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Language" . ($Caption = "Name") . "", $Configuration["InputWidth"], $Setting[$SettingName], true), "{$Caption}*", true, null, $Configuration["FieldCaptionWidth"], $Configuration["FieldContentWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Language" . ($Caption = "Code") . "", $Configuration["InputWidth"], $Setting[$SettingName], true), "{$Caption}*", true, null, $Configuration["FieldCaptionWidth"], $Configuration["FieldContentWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Language" . ($Caption = "Region") . "Code", $Configuration["InputInlineWidth"], $Setting[$SettingName]), "{$Caption} code", null, true, $Configuration["FieldCaptionInlineWidth"], $Configuration["FieldContentInlineWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Language" . ($Caption = "Native") . "Name", $Configuration["InputWidth"], $Setting[$SettingName]), "{$Caption} name", true, null, $Configuration["FieldCaptionWidth"], $Configuration["FieldContentWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Language" . ($Caption = "Natively") . "Name", $Configuration["InputInlineWidth"], $Setting[$SettingName]), "{$Caption} name", null, true, $Configuration["FieldCaptionInlineWidth"], $Configuration["FieldContentInlineWidth"]) . "

			<div class=\"SectionTitle\">Guest</div>
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Guest" . ($Caption = "Email") . "", $Configuration["InputFullWidth"], $Setting[$SettingName], true, INPUT_TYPE_EMAIL), "{$Caption}*", true, null, $Configuration["FieldCaptionWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Guest" . ($Caption = "Name") . "", $Configuration["InputFullWidth"], $Setting[$SettingName], true), "{$Caption}*", true, null, $Configuration["FieldCaptionWidth"]) . "

			<div class=\"SectionTitle\">Template</div>
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "Template" . ($Caption = "Cache") . "Lifetime", $NumberInputWidth, $Setting[$SettingName], true, INPUT_TYPE_NUMBER), "{$Caption} life time*", true, null, $Configuration["FieldCaptionWidth"], $Configuration["FieldContentWidth"], null, null, "Seconds") . "

			<div class=\"SectionTitle\">Other</div>
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "" . ($Caption = "Time") . "zone", $Configuration["InputWidth"], $Setting[$SettingName], true), "{$Caption} zone*", true, null, $Configuration["FieldCaptionWidth"], $Configuration["FieldContentWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "" . ($Caption = "Document") . "Type", $Configuration["InputInlineWidth"], $Setting[$SettingName], true), "{$Caption} type*", null, true, $Configuration["FieldCaptionInlineWidth"], $Configuration["FieldContentInlineWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "" . ($Caption = "Default") . "Script", $Configuration["InputFullWidth"], $Setting[$SettingName], true), "{$Caption} script*", true, null, $Configuration["FieldCaptionWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "" . ($Caption = "Character") . "Set", $Configuration["InputWidth"], $Setting[$SettingName], true), "{$Caption} set*", true, null, $Configuration["FieldCaptionWidth"], $Configuration["FieldContentWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input($SettingName = "" . ($Caption = "Status") . "Code", $NumberInputWidth, $Setting[$SettingName], true, INPUT_TYPE_NUMBER), "{$Caption} code*", null, true, $Configuration["FieldCaptionInlineWidth"], $Configuration["FieldContentInlineWidth"]) . "
		",
		"Set", // Submit button caption
		$Application->EncryptionKey(), // Signature modifier
		"System settings", // Title
		null, // Header
		null, // Footer
		"*Required field(s)", // Status
		"frmConfiguration" // ID
	) . "</div>";
?>