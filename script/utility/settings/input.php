<?php
	namespace sPHP;

	// Load user saved settings values
	$CustomSetting = new Content("System settings", null, $Environment->ContentPath());

	// Merge custom settings values with default configuration values to let new setting value be set
	foreach($CFG as $Key=>$Value)$Setting[$Key] = isset($CustomSetting->Value()[$Key]) ? $CustomSetting->Value()[$Key] : $Value;

	$NumberInputWidth = 80;

	#region Timezone options
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
	#endregion Timezone options

	print "<div class=\"AlignCenter\">" . HTML\UI\Form(
		$Application->URL("Utility/Settings/Action"), // Action URL
		// Content
		"
			<div class=\"SectionTitle\">Administrator</div>
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Administrator" . ($Caption = "Email") . "", $CFG["InputFullWidth"], $Setting[$SettingName], true, INPUT_TYPE_EMAIL), "{$Caption}*", null, null, $CFG["FieldCaptionWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Administrator" . ($Caption = "Password") . "", $CFG["InputFullWidth"]), "{$Caption}", true, null, $CFG["FieldCaptionWidth"], null, null, null, "Leave blank to retain existing password") . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Administrator" . ($Caption = "Name") . "", $CFG["InputFullWidth"], $Setting[$SettingName], true), "{$Caption}*", true, null, $CFG["FieldCaptionWidth"]) . "

			<div class=\"SectionTitle\">Company</div>
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Company" . ($Caption = "Name") . "", $CFG["InputFullWidth"], $Setting[$SettingName]), "{$Caption}", true, null, $CFG["FieldCaptionWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Company" . ($Caption = "Address") . "", $CFG["InputFullWidth"], $Setting[$SettingName]), "{$Caption}", true, null, $CFG["FieldCaptionWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Company" . ($Caption = "Phone") . "", $CFG["InputWidth"], $Setting[$SettingName], null, INPUT_TYPE_PHONE), "{$Caption}", true, null, $CFG["FieldCaptionWidth"], $CFG["FieldContentWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Company" . ($Caption = "Email") . "", $CFG["InputFullWidth"], $Setting[$SettingName], null, INPUT_TYPE_EMAIL), "{$Caption}", true, null, $CFG["FieldCaptionWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Company" . ($Caption = "URL") . "", $CFG["InputFullWidth"], $Setting[$SettingName], null, INPUT_TYPE_URL), "{$Caption}", true, null, $CFG["FieldCaptionWidth"]) . "

			<div class=\"SectionTitle\">Application</div>
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "" . ($Caption = "Name") . "", $CFG["InputFullWidth"], $Setting[$SettingName], true), "{$Caption}*", true, null, $CFG["FieldCaptionWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "" . ($Caption = "Short") . "Name", $CFG["InputWidth"], $Setting[$SettingName]), "{$Caption} name", true, null, $CFG["FieldCaptionWidth"], $CFG["FieldContentWidth"]) . "

			<div class=\"SectionTitle\">Title</div>
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "" . ($Caption = "Title") . "", $CFG["InputWidth"], $Setting[$SettingName]), "{$Caption}", true, null, $CFG["FieldCaptionWidth"], $CFG["FieldContentWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Title" . ($Caption = "Prefix") . "", $CFG["InputInlineWidth"], $Setting[$SettingName]), "{$Caption}", null, true, $CFG["FieldCaptionWidth"], $CFG["FieldContentInlineWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Title" . ($Caption = "Suffix") . "", $CFG["InputWidth"], $Setting[$SettingName]), "{$Caption}", true, null, $CFG["FieldCaptionWidth"], $CFG["FieldContentWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Title" . ($Caption = "Seperator") . "", $CFG["InputInlineWidth"], $Setting[$SettingName]), "{$Caption}", null, true, $CFG["FieldCaptionWidth"], $CFG["FieldContentInlineWidth"]) . "

			<div class=\"SectionTitle\">Meta</div>
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "" . ($Caption = "Description") . "", $CFG["InputFullWidth"], $Setting[$SettingName]), "{$Caption}", true, null, $CFG["FieldCaptionWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "" . ($Caption = "Keyword") . "", $CFG["InputFullWidth"], $Setting[$SettingName]), "{$Caption}", true, null, $CFG["FieldCaptionWidth"]) . "

			<div class=\"SectionTitle\">Database</div>
				" . HTML\UI\Field(HTML\UI\Select($SettingName = "Database" . ($Caption = "Type") . "", [new Option("MYSQL", "MySQL"), new Option("MSSQL", "Microsoft SQL Server 2014+")], null, null, null, null, $Setting[$SettingName]), "{$Caption}", true, null, $CFG["FieldCaptionWidth"], null, null, null, null) . "
				" . HTML\UI\Field(HTML\UI\Select($SettingName = "Database" . ($Caption = "Time") . "zone", $TimeZoneOption, null, null, null, null, $Setting[$SettingName]), "{$Caption} zone", null, true, null, null, null, null, "Should match application time zone") . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Database" . ($Caption = "Host") . "", $CFG["InputInlineWidth"], $Setting[$SettingName]), "{$Caption}", true, null, $CFG["FieldCaptionWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Database" . ($Caption = "Name") . "", $CFG["InputInlineWidth"], $Setting[$SettingName]), "{$Caption}", null, true, $CFG["FieldCaptionInlineWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Database" . ($Caption = "User") . "", $CFG["InputWidth"], $Setting[$SettingName]), "{$Caption}", true, null, $CFG["FieldCaptionWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Database" . ($Caption = "Password") . "", $CFG["InputInlineWidth"], $Setting[$SettingName]), "{$Caption}", null, true, $CFG["FieldCaptionInlineWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Database" . ($Caption = "ODBC") . "Driver", $CFG["InputFullWidth"], $Setting[$SettingName]), "{$Caption} driver", true, null, $CFG["FieldCaptionWidth"], null, null, null, "e.g.: SQL Server Native Client 11.0. Doesn't work with MySQL database type") . "

			<div class=\"SectionTitle\">Head</div>
				" . HTML\UI\Field(HTML\UI\Textarea($SettingName = "" . ($Caption = "HTML") . "HeadCode", $CFG["InputFullWidth"], 100, $Setting[$SettingName]), "{$Caption} head code", true, null, $CFG["FieldCaptionWidth"]) . "

			<div class=\"SectionTitle\">Interface</div>
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Field" . ($Caption = "Caption") . "Width", $NumberInputWidth, $Setting[$SettingName], true, INPUT_TYPE_NUMBER), "Field: {$Caption} width", true, null, $CFG["FieldCaptionWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Field" . ($Caption = "Caption") . "InlineWidth", $NumberInputWidth, $Setting[$SettingName], true, INPUT_TYPE_NUMBER), "{$Caption} inline width", null, true, null) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Field" . ($Caption = "Content") . "Width", $NumberInputWidth, $Setting[$SettingName], null, INPUT_TYPE_NUMBER), "Field: {$Caption} width", true, null, $CFG["FieldCaptionWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Field" . ($Caption = "Content") . "InlineWidth", $NumberInputWidth, $Setting[$SettingName], null, INPUT_TYPE_NUMBER), "{$Caption} inline width", null, true, null) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Field" . ($Caption = "Content") . "FullWidth", $NumberInputWidth, $Setting[$SettingName], null, INPUT_TYPE_NUMBER), "{$Caption} full width", null, true, null) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Input" . ($Caption = "Width") . "", $NumberInputWidth, $Setting[$SettingName], true, INPUT_TYPE_NUMBER), "Input: {$Caption}", true, null, $CFG["FieldCaptionWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Input" . ($Caption = "Inline") . "Width", $NumberInputWidth, $Setting[$SettingName], true, INPUT_TYPE_NUMBER), "{$Caption} width", null, true, null) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Input" . ($Caption = "Full") . "Width", $NumberInputWidth, $Setting[$SettingName], true, INPUT_TYPE_NUMBER), "{$Caption} width", null, true, null) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Input" . ($Caption = "Date") . "Width", $NumberInputWidth, $Setting[$SettingName], true, INPUT_TYPE_NUMBER), "{$Caption} width", null, true, null) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "" . ($Caption = "Textarea") . "Height", $NumberInputWidth, $Setting[$SettingName], true, INPUT_TYPE_NUMBER), "{$Caption} height", true, null, $CFG["FieldCaptionWidth"]) . "

			<div class=\"SectionTitle\">Security</div>
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "" . ($Caption = "Encryption") . "Key", $CFG["InputFullWidth"], $Setting[$SettingName], true), "{$Caption} key*", true, null, $CFG["FieldCaptionWidth"], null, null, null, "<span class=\"Warning\">Careful! Repeatative changes may adversly impact application security.</span>") . "
				" . HTML\UI\Field(HTML\UI\RadioGroup($SettingName = "" . ($Caption = "Use") . "SystemScript", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")], $Setting[$SettingName]), "{$Caption} system script", true, null, $CFG["FieldCaptionWidth"], $CFG["FieldContentInlineWidth"]) . "

			<div class=\"SectionTitle\">SMTP Email</div>
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "SMTP" . ($Caption = "Body") . "Style", $CFG["InputFullWidth"], $Setting[$SettingName]), "{$Caption} style", true, null, $CFG["FieldCaptionWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "SMTP" . ($Caption = "Host") . "", $CFG["InputFullWidth"], $Setting[$SettingName]), "{$Caption}", true, null, $CFG["FieldCaptionWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "SMTP" . ($Caption = "Port") . "", $NumberInputWidth, $Setting[$SettingName], null, INPUT_TYPE_NUMBER), "{$Caption}", true, null, $CFG["FieldCaptionWidth"], $CFG["FieldContentWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "SMTP" . ($Caption = "User") . "", $CFG["InputFullWidth"], $Setting[$SettingName]), "{$Caption}", true, null, $CFG["FieldCaptionWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "SMTP" . ($Caption = "Password") . "", $CFG["InputFullWidth"], $Setting[$SettingName]), "{$Caption}", true, null, $CFG["FieldCaptionWidth"]) . "

			<div class=\"SectionTitle\">Content</div>
				" . HTML\UI\Field(HTML\UI\RadioGroup($SettingName = "Content" . ($Caption = "Edit") . "Mode", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")], $Setting[$SettingName]), "{$Caption} mode", true, null, $CFG["FieldCaptionWidth"], null, null, null, "<span class=\"Warning\">Overrides session if YES</span>") . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Content" . ($Caption = "Edit") . "ModeServer", $CFG["InputFullWidth"], $Setting[$SettingName]), "{$Caption} mode server", true, null, $CFG["FieldCaptionWidth"], null, null, null, "<span class=\"Warning\">Overrides session if evaluates TRUE</span>") . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Content" . ($Caption = "Edit") . "ModeClient", $CFG["InputFullWidth"], $Setting[$SettingName]), "{$Caption} mode client", true, null, $CFG["FieldCaptionWidth"], null, null, null, "<span class=\"Warning\">Overrides session if evaluates TRUE</span>") . "

			<div class=\"SectionTitle\">Debug</div>
				" . HTML\UI\Field(HTML\UI\RadioGroup($SettingName = "" . ($Caption = "Custom") . "Error", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")], $Setting[$SettingName]), "{$Caption} error", true, null, $CFG["FieldCaptionWidth"]) . "
				" . HTML\UI\Field(HTML\UI\RadioGroup($SettingName = "Debug" . ($Caption = "Mode") . "", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")], $Setting[$SettingName]), "{$Caption}", true, null, $CFG["FieldCaptionWidth"], null, null, null, "<span class=\"Warning\">Overrides session if YES</span>") . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "DebugMode" . ($Caption = "Server") . "", $CFG["InputFullWidth"], $Setting[$SettingName]), "{$Caption} list", true, null, $CFG["FieldCaptionWidth"], null, null, null, "<span class=\"Warning\">Overrides session if evaluates TRUE</span>") . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "DebugMode" . ($Caption = "Client") . "", $CFG["InputFullWidth"], $Setting[$SettingName]), "{$Caption} list", true, null, $CFG["FieldCaptionWidth"], null, null, null, "<span class=\"Warning\">Overrides session if evaluates TRUE</span>") . "

			<div class=\"SectionTitle\">Session</div>
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Session" . ($Caption = "Life") . "time", $NumberInputWidth, $Setting[$SettingName], true, INPUT_TYPE_NUMBER), "{$Caption} time*", true, null, $CFG["FieldCaptionWidth"], $CFG["FieldContentWidth"]) . "
				" . HTML\UI\Field(HTML\UI\RadioGroup($SettingName = "Session" . ($Caption = "Isolate") . "", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")], $Setting[$SettingName]), "{$Caption}", true, null, $CFG["FieldCaptionWidth"]) . "

			<div class=\"SectionTitle\">Interface</div>
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "" . ($Caption = "Viewport") . "", $CFG["InputFullWidth"], $Setting[$SettingName]), "{$Caption}", true, null, $CFG["FieldCaptionWidth"]) . "

			<div class=\"SectionTitle\">Language</div>
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Language" . ($Caption = "Name") . "", $CFG["InputWidth"], $Setting[$SettingName], true), "{$Caption}*", true, null, $CFG["FieldCaptionWidth"], $CFG["FieldContentWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Language" . ($Caption = "Code") . "", $CFG["InputWidth"], $Setting[$SettingName], true), "{$Caption}*", true, null, $CFG["FieldCaptionWidth"], $CFG["FieldContentWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Language" . ($Caption = "Region") . "Code", $CFG["InputInlineWidth"], $Setting[$SettingName]), "{$Caption} code", null, true, $CFG["FieldCaptionInlineWidth"], $CFG["FieldContentInlineWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Language" . ($Caption = "Native") . "Name", $CFG["InputWidth"], $Setting[$SettingName]), "{$Caption} name", true, null, $CFG["FieldCaptionWidth"], $CFG["FieldContentWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Language" . ($Caption = "Natively") . "Name", $CFG["InputInlineWidth"], $Setting[$SettingName]), "{$Caption} name", null, true, $CFG["FieldCaptionInlineWidth"], $CFG["FieldContentInlineWidth"]) . "

			<div class=\"SectionTitle\">Guest</div>
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Guest" . ($Caption = "Email") . "", $CFG["InputFullWidth"], $Setting[$SettingName], true, INPUT_TYPE_EMAIL), "{$Caption}*", true, null, $CFG["FieldCaptionWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Guest" . ($Caption = "Name") . "", $CFG["InputFullWidth"], $Setting[$SettingName], true), "{$Caption}*", true, null, $CFG["FieldCaptionWidth"]) . "

			<div class=\"SectionTitle\">Template</div>
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "Template" . ($Caption = "Cache") . "Lifetime", $NumberInputWidth, $Setting[$SettingName], true, INPUT_TYPE_NUMBER), "{$Caption} life time*", true, null, $CFG["FieldCaptionWidth"], $CFG["FieldContentWidth"], null, null, "Seconds") . "

			<div class=\"SectionTitle\">Other</div>
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "" . ($Caption = "Time") . "zone", $CFG["InputWidth"], $Setting[$SettingName], true), "{$Caption} zone*", true, null, $CFG["FieldCaptionWidth"], $CFG["FieldContentWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "" . ($Caption = "Default") . "Script", $CFG["InputFullWidth"], $Setting[$SettingName], true), "{$Caption} script*", true, null, $CFG["FieldCaptionWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "" . ($Caption = "Character") . "Set", $CFG["InputWidth"], $Setting[$SettingName], true), "{$Caption} set*", true, null, $CFG["FieldCaptionWidth"], $CFG["FieldContentWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input($SettingName = "" . ($Caption = "Status") . "Code", $NumberInputWidth, $Setting[$SettingName], true, INPUT_TYPE_NUMBER), "{$Caption} code*", null, true, $CFG["FieldCaptionInlineWidth"], $CFG["FieldContentInlineWidth"]) . "
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