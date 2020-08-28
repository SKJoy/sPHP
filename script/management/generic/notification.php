<?php
namespace sPHP;

#region Entity management common configuration
$EM = new EntityManagement($Table[$Entity = "Notification"]);

$EM->ImportField([
	new Database\Field("{$Entity}" . ($Field = "Signature") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Event") . "Time", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Subject") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Message") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Type") . "ID", "{$Field}", null, $Table["{$Entity}{$Field}"], "{$Field}Name"),
	new Database\Field("{$Entity}" . ($Field = "Source") . "ID", "{$Field}", null, $Table["{$Entity}{$Field}"], "{$Field}Name"),
	new Database\Field("{$Entity}" . ($Field = "To") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Attempt") . "Time", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Attempt") . "", "{$Field}"),
	new Database\Field("{$Entity}" . ($Field = "Sent") . "Time", "{$Field}"),
	new Database\Field("{$Entity}Is" . ($Field = "Active") . "", "{$Field}"),
]);

$EM->InputValidation([
	new HTTP\InputValidation("{$Entity}Signature", true, null),
	new HTTP\InputValidation("{$Entity}EventTime", true, null),
	new HTTP\InputValidation("{$Entity}Subject", null, null),
	new HTTP\InputValidation("{$Entity}Message", true, null),
	new HTTP\InputValidation("{$Entity}TypeID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("{$Entity}SourceID", true, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("{$Entity}To", null, null),
	new HTTP\InputValidation("{$Entity}AttemptTime", null, null),
	new HTTP\InputValidation("{$Entity}Attempt", null, VALIDATION_TYPE_INTEGER),
	new HTTP\InputValidation("{$Entity}SentTime", null, null),
	new HTTP\InputValidation("{$Entity}IsActive", null, VALIDATION_TYPE_INTEGER),
]);

$EM->ValidateInput(function($Entity, $Database, $Table, $PrimaryKey, $ID){
	$Result = true;

	if($Table->Get( // Check for duplicate values for UNIQUE columns
		"
			(
					{$Table->Alias()}." . ($Column = "{$Entity}TypeID") . " = '" . intval($Database->Escape($_POST["{$Column}"])) . "'
				AND	{$Table->Alias()}." . ($Column = "{$Entity}EventTime") . " = '{$Database->Escape($_POST["{$Column}"])}'
				AND	{$Table->Alias()}." . ($Column = "{$Entity}To") . " = '{$Database->Escape($_POST["{$Column}"])}'
				AND	{$Table->Alias()}." . ($Column = "{$Entity}Subject") . " = '{$Database->Escape($_POST["{$Column}"])}'
				AND	{$Table->Alias()}." . ($Column = "{$Entity}Message") . " = '{$Database->Escape($_POST["{$Column}"])}'
			)
			AND	{$Table->Alias()}.{$PrimaryKey} <> {$ID}
		"
	, null, null, null, null, null, null))$Result = "Same configuration for the same " . strtolower($Table->FormalName()) . " exists!";

	return $Result;
});

$EM->ThumbnailColumn("x{$Entity}Picture");

$EM->BeforeInput(function($Entity, $Record){
	//$_POST[$Field = "{$Entity}PasswordHash"] = strlen($_POST["{$Entity}Password"]) ? md5($_POST["{$Entity}Password"]) : (is_null($Record) ? null : $Record["{$Field}"]);

	return true;
});

$EM->IntermediateEntity("Category, Event");
$EM->DefaultFromSearchColumn("ConsignmentID, {$Entity}FromAddressID, {$Entity}ToAddressID, TransportRouteTypeID, CargoHandlerID");

$EM->ListColumn([
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Type") . "LookupCaption", "{$Caption}", FIELD_TYPE_ICON, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "To") . "", "{$Caption}"),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Subject") . "", "{$Caption}"),
	//new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Message") . "", "{$Caption}"),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "From") . "", "{$Caption}"),
	//new HTML\UI\Datagrid\Column("{$Entity}Event" . ($Caption = "Time") . "", "{$Caption}", FIELD_TYPE_DATETIME, ALIGN_CENTER),
	//new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Attempt") . "Time", "{$Caption}ed", FIELD_TYPE_DATETIME),
	//new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Attempt") . "", "{$Caption}", FIELD_TYPE_NUMBER),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Source") . "LookupCaption", "{$Caption}", FIELD_TYPE_ICON, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("" . ($Caption = "") . "TimeInserted", "{$Caption}Created", FIELD_TYPE_DATETIME, ALIGN_CENTER),
	new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Sent") . "Time", "{$Caption}", FIELD_TYPE_DATETIME, ALIGN_CENTER),
	//new HTML\UI\Datagrid\Column("{$Entity}Send" . ($Caption = "Delay") . "Time", "{$Caption}", FIELD_TYPE_TIME, ALIGN_CENTER),
	//new HTML\UI\Datagrid\Column("{$Entity}" . ($Caption = "Signature") . "", "{$Caption}"),
	//new HTML\UI\Datagrid\Column("UserEmail" . ($Caption = "Inserted") . "", "{$Caption} by"),
	//new HTML\UI\Datagrid\Column("UserEmail" . ($Caption = "Updated") . "", "{$Caption} by"),
	new HTML\UI\Datagrid\Column("{$Entity}Is" . ($Caption = "Active") . "", "{$Caption}", FIELD_TYPE_BOOLEANICON),
]);

$EM->Action([
	//new HTML\UI\Datagrid\Action("{$Environment->IconURL()}listener.png", null, $Application->URL("{$Entity}/ListenerActivity"), null, null, null, "Listener activity"),
	//new HTML\UI\Datagrid\Action("{$Environment->IconURL()}" . strtolower($ActionEntity = "{$Entity}Assignment") . ".png", null, $Application->URL("management/generic/{$ActionEntity}&btnInput"), null, null, null, "{$Entity} assignment"),
	new HTML\UI\Datagrid\Action("{$Environment->IconURL()}edit.png", null, $Application->URL($_POST["_Script"], "btnInput"), null, null, null, "Edit"),
	//new HTML\UI\Datagrid\Action("{$Environment->IconURL()}delete.png", null, $Application->URL($_POST["_Script"], "btnDelete"), null, "return confirm('Are you sure to remove the information?');", null, "Delete"),
]);

$EM->BatchActionHTML([
	HTML\UI\Button("<img src=\"{$Environment->IconURL()}search.png\" alt=\"Search\" class=\"Icon\">Search", BUTTON_TYPE_SUBMIT, "btnSearch", true),
	//HTML\UI\Button("<img src=\"{$Environment->IconURL()}add.png\" alt=\"Add new\" class=\"Icon\">Add new", BUTTON_TYPE_SUBMIT, "btnInput", true),
	//HTML\UI\Button("<img src=\"{$Environment->IconURL()}delete.png\" alt=\"Remove\" class=\"Icon\">Remove", BUTTON_TYPE_SUBMIT, "btnDelete", true, "return confirm('Are you sure to remove the information?');"),
	//HTML\UI\Button("<img src=\"{$Environment->IconURL()}export.png\" alt=\"Export\" class=\"Icon\">Export", BUTTON_TYPE_SUBMIT, "btnExport", true),
	//HTML\UI\Button("<img src=\"{$Environment->IconURL()}import.png\" alt=\"Import\" class=\"Icon\">Import", BUTTON_TYPE_SUBMIT, "btnImport", true),
	//HTML\UI\Button("<img src=\"{$Environment->IconURL()}active.png\" alt=\"Set active\" class=\"Icon\">Set/toggle active", BUTTON_TYPE_SUBMIT, "btnSet{$Entity}IsActive", true),
]);

$EM->OrderBy("TimeInserted");
$EM->Order("DESC");
$EM->URL($Application->URL($_POST["_Script"]));
$EM->IconURL($Environment->IconURL());
$EM->EncryptionKey($Application->EncryptionKey());
$EM->FieldCaptionWidth($Configuration["FieldCaptionWidth"]);
$EM->FieldCaptionInlineWidth($Configuration["FieldCaptionInlineWidth"]);
$EM->FieldContentFullWidth($Configuration["FieldContentFullWidth"]);
$EM->InputWidth($Configuration["InputWidth"]);
$EM->InputInlineWidth($Configuration["InputInlineWidth"]);
$EM->InputFullWidth($Configuration["InputFullWidth"]);
$EM->InputDateWidth($Configuration["InputDateWidth"]);
$EM->InputTimeWidth($Configuration["InputTimeWidth"]);
$EM->TempPath($Environment->TempPath());
$EM->SearchInputPrefix($Configuration["SearchInputPrefix"]);
$EM->UploadPath($Environment->UploadPath());
$EM->ThumbnailMaximumDimension(48);
$EM->RecordsPerPage(2000); // $Configuration["DatagridRowsPerPage"]
$EM->BaseURL($Environment->URL()); // ???????????
$EM->ListRowExpandURL($Application->URL("{$Entity}MessageForDatagrid", "_NoHeader&_NoFooter"));
#endregion Entity management common configuration

if(isset($_POST["btnImport"])){
	if(isset($_POST["btnSubmit"])){
		$EM->Import();
		$Terminal->Redirect($_POST["_Referer"]);
	}

	print $EM->ImportHTML();
}

if(isset($_POST["btnDelete"])){
	$EM->Delete();
	$Terminal->Redirect($_SERVER["HTTP_REFERER"]);
}

if(isset($_POST["btnInput"])){
	$NewRecordMode = isset($_POST["{$Entity}ID"]) && intval($_POST["{$Entity}ID"]) ? false : true;

	if(isset($_POST["btnSubmit"])){
		#region Custom code
		$_POST["{$Entity}EventTime"] = "{$_POST["{$Entity}EventTimeDate"]} {$_POST["{$Entity}EventTimeTime"]}:00";
		if($_POST["{$Entity}AttemptTimeDate"])$_POST["{$Entity}AttemptTime"] = "{$_POST["{$Entity}AttemptTimeDate"]} {$_POST["{$Entity}AttemptTimeTime"]}:00";
		$_POST["{$Entity}SentTime"] = $_POST["{$Entity}SentTimeDate"] ? "{$_POST["{$Entity}SentTimeDate"]} {$_POST["{$Entity}SentTimeTime"]}:00" : null;
		if(!$_POST["{$Entity}Signature"])$_POST["{$Entity}Signature"] = md5($_POST["{$Entity}Signature"]);
		#endregion Custom code
//var_dump($_POST); exit;
		if($EM->Input())$Terminal->Redirect("{$_POST["_Referer"]}&SucceededAction=Input"); // Redirect to previous location
	}

	$EM->LoadExistingData();

	#region Custom code
	if($NewRecordMode){

	}
	else{
		// Part date time database value into seperate date and time fields
		$NotificationEventTime = strtotime($_POST["{$Entity}EventTime"]);
		$_POST["{$Entity}EventTimeDate"] = date("Y-m-d", $NotificationEventTime);
		$_POST["{$Entity}EventTimeTime"] = date("H:i", $NotificationEventTime);

		if($_POST["{$Entity}AttemptTime"]){ // If a valid value is present
			$NotificationAttemptTime = strtotime($_POST["{$Entity}AttemptTime"]);
			$_POST["{$Entity}AttemptTimeDate"] = date("Y-m-d", $NotificationAttemptTime);
			$_POST["{$Entity}AttemptTimeTime"] = date("H:i", $NotificationAttemptTime);
		}

		if($_POST["{$Entity}SentTime"]){ // If a valid value is present
			$NotificationSentTime = strtotime($_POST["{$Entity}SentTime"]);
			$_POST["{$Entity}SentTimeDate"] = date("Y-m-d", $NotificationSentTime);
			$_POST["{$Entity}SentTimeTime"] = date("H:i", $NotificationSentTime);
		}
	}
	#endregion Custom code

	$EM->InputUIHTML([
		HTML\UI\Field(
			HTML\UI\Select("{$Entity}" . ($Caption = "Type") . "ID", $Table[$OptionEntity = "{$Entity}{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1", "{$OptionEntity}LookupCaption ASC"), null, "{$OptionEntity}LookupCaption") .
			HTML\UI\Input("{$Entity}" . ($Caption = "To") . "", $EM->InputWidth(), null, true, null, null, "Required"),
		"{$Caption}", null, null, $EM->FieldCaptionWidth()),

		HTML\UI\Field(
			HTML\UI\Input("{$Entity}" . ($Caption = "Sent") . "TimeDate", null, null, null, INPUT_TYPE_DATE) .
			HTML\UI\Input("{$Entity}" . ($Caption = "Sent") . "TimeTime", null, date("H:i"), true, INPUT_TYPE_TIME),
		"{$Caption} on", null, true, null, null, null, null, "Optional"),

		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Subject") . "", $EM->InputFullWidth(), null, true, null, null, "Required"), "{$Caption}", true, null, $EM->FieldCaptionWidth()),

		HTML\UI\Field(
			HTML\UI\Textarea("{$Entity}" . ($Caption = "Message") . "", $EM->InputFullWidth(), $Configuration["TextareaHeight"], null, true, null, "Required", ["OnChange" => "document.getElementById('{$Entity}Signature').value = md5(this.value);", ], "{$Entity}Message") .
			"<br>" . HTML\UI\Button("Toggle editor", BUTTON_TYPE_BUTTON, null, null, ["OnClick" => "sJS.MakeTinyMCETextarea('{$Entity}Message');", ]),
		"{$Caption}", true, null, $EM->FieldCaptionWidth()),

		HTML\UI\Field(
			HTML\UI\Select("{$Entity}" . ($Caption = "Source") . "ID", $Table[$OptionEntity = "{$Entity}{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1", "{$OptionEntity}LookupCaption ASC"), null, "{$OptionEntity}LookupCaption") .
			HTML\UI\Input("{$Entity}" . ($Caption = "From") . "", $EM->InputWidth(), null, null),
		"{$Caption}", true, null, $EM->FieldCaptionWidth()),

		HTML\UI\Field(
			HTML\UI\Input("{$Entity}" . ($Caption = "Event") . "TimeDate", null, date("Y-m-d"), true, INPUT_TYPE_DATE) .
			HTML\UI\Input("{$Entity}" . ($Caption = "Event") . "TimeTime", null, date("H:i"), true, INPUT_TYPE_TIME),
		"{$Caption} on", null, true, null, null, null, null, "Required"),

		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Signature") . "", $EM->InputFullWidth(), null, null, null, null, "Automatically computed", null, "{$Entity}Signature"), "{$Caption}", true, null, $EM->FieldCaptionWidth(), null, null, null, null),

		HTML\UI\Field(
			HTML\UI\Input("{$Entity}" . ($Caption = "Attempt") . "TimeDate", $EM->InputDateWidth(), null, null, INPUT_TYPE_DATE) .
			HTML\UI\Input("{$Entity}" . ($Caption = "Attempt") . "TimeTime", $EM->InputTimeWidth(), date("H:i"), true, INPUT_TYPE_TIME),
		"{$Caption}: Time", true, null, $EM->FieldCaptionWidth(), null, null, null, "Optional"),

		HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "") . "Attempt", 50, 0, true, INPUT_TYPE_NUMBER), "{$Caption}Count", null, true, null),

		HTML\UI\Field(HTML\UI\RadioGroup("{$Entity}Is" . ($Caption = "Active") . "", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")]), "{$Caption}", true, null, $EM->FieldCaptionWidth()),
	]);

	print $EM->InputHTML();
}

if(isset($_POST["btnSet{$Entity}IsActive"])){
	$NotificationIsActive = strlen($_POST["{$EM->SearchInputPrefix()}{$Entity}IsActive"]) ? intval($_POST["{$EM->SearchInputPrefix()}{$Entity}IsActive"]) : "NOT N.{$Entity}IsActive";

	$Database->Query("
		UPDATE			sphp_notification AS N
		SET				N.{$Entity}IsActive = {$NotificationIsActive}
		WHERE			N.{$Entity}ID IN (" . implode(", ", is_array(SetVariable("{$Entity}ID")) ? $_POST["{$Entity}ID"] : [intval($_POST["{$Entity}ID"])]) . ")
	");

	print HTML\UI\MessageBox("Active set/toggle operation completed successfully.");
}

#region List
#region Custom code
//SetVariable("{$Configuration["SearchInputPrefix"]}{$Entity}EventTimeFromDate", SetVariable("{$Entity}EventTimeFromDate", date("Y-m-d")));
//SetVariable("{$Configuration["SearchInputPrefix"]}{$Entity}EventTimeFromTimeHHMM", SetVariable("{$Entity}EventTimeFromTimeHHMM", "00:00"));
//SetVariable("{$Configuration["SearchInputPrefix"]}{$Entity}EventTimeToDate", SetVariable("{$Entity}EventTimeToDate", date("Y-m-d")));
//SetVariable("{$Configuration["SearchInputPrefix"]}{$Entity}EventTimeToTimeHHMM", SetVariable("{$Entity}EventTimeToTimeHHMM", "23:59"));
$_POST[$Column = "{$Configuration["SearchInputPrefix"]}{$Entity}Message"] = trim(strip_tags(SetVariable("{$Configuration["SearchInputPrefix"]}{$Entity}Message")));
#endregion Custom code

$EM->SearchSQL([
	"1 = 1", // Custom fixed search condition
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}TypeID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}To") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Subject") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Message") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}From") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}SourceID") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "TimeInsertedFromDate") . "", SetVariable($Column, date("Y-m-d", strtotime("-1 day")))) ? "{$Table["{$Entity}"]->Alias()}.TimeInserted >= '{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])} " . SetVariable("{$Configuration["SearchInputPrefix"]}TimeInsertedFromTimeHHMM", "00:00") . ":00'" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "TimeInsertedToDate") . "", SetVariable($Column, date("Y-m-d"))) ? "{$Table["{$Entity}"]->Alias()}.TimeInserted <= '{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])} " . SetVariable("{$Configuration["SearchInputPrefix"]}TimeInsertedToTimeHHMM", "23:59") . ":59'" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}IsSent") . "", SetVariable($Column, "")) !== "" ? "{$Table["{$Entity}"]->Alias()}.{$Entity}SentTime IS" . ($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"] ? " NOT" : null) . " NULL" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}SentTimeFromDate") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Entity}SentTime >= '{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])} {$_POST["{$Configuration["SearchInputPrefix"]}{$Entity}EventTimeFromTimeHHMM"]}:00'" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}SentTimeToDate") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Entity}SentTime <= '{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])} {$_POST["{$Configuration["SearchInputPrefix"]}{$Entity}EventTimeToTimeHHMM"]}:59'" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}Signature") . "", SetVariable($Column)) ? "{$Table["{$Entity}"]->Alias()}.{$Column} LIKE '%{$Database->Escape($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"])}%'" : null,
	SetVariable("{$Configuration["SearchInputPrefix"]}" . ($Column = "{$Entity}IsActive") . "", SetVariable($Column, "")) !== "" ? "{$Table["{$Entity}"]->Alias()}.{$Column} = " . intval($_POST["{$Configuration["SearchInputPrefix"]}{$Column}"]) . "" : null,
]);
//DebugDump($EM->SearchSQL());
$EM->SearchUIHTML([
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Type") . "ID", $Table[$OptionEntity = "{$Entity}{$Caption}"]->Get(null, "" . ($OptionEntityOrderBy = "{$OptionEntity}LookupCaption") . " ASC"), new Option(), "{$OptionEntityOrderBy}"), "{$Caption}", null, null),
	HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "To") . "", 200), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Subject") . "", 200), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Message") . "", 200), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "From") . "", 200), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Source") . "ID", $Table[$OptionEntity = "{$Entity}{$Caption}"]->Get(null, "" . ($OptionEntityOrderBy = "{$OptionEntity}LookupCaption") . " ASC"), new Option(), "{$OptionEntityOrderBy}"), "{$Caption}", null, true),

	HTML\UI\Field(
		HTML\UI\Input("{$Configuration["SearchInputPrefix"]}TimeInserted" . ($Caption = "") . "FromDate", null, null, true, INPUT_TYPE_DATE) .
		HTML\UI\Input("{$Configuration["SearchInputPrefix"]}TimeInserted" . ($Caption = "") . "FromTimeHHMM", null, null, true, INPUT_TYPE_TIME) .
		HTML\UI\Input("{$Configuration["SearchInputPrefix"]}TimeInserted" . ($Caption = "") . "ToDate", null, null, true, INPUT_TYPE_DATE) .
		HTML\UI\Input("{$Configuration["SearchInputPrefix"]}TimeInserted" . ($Caption = "") . "ToTimeHHMM", null, null, true, INPUT_TYPE_TIME),
	"{$Caption}Created", null, true),

	HTML\UI\Field(
		HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}Is" . ($Caption = "Sent") . "", [new Option(), new Option(0, "No"), new Option(1, "Yes")]) .
		HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Sent") . "TimeFromDate", null, null, null, INPUT_TYPE_DATE) .
		HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Sent") . "TimeFromTimeHHMM", null, date("00:00"), true, INPUT_TYPE_TIME) .
		HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Sent") . "TimeToDate", null, null, null, INPUT_TYPE_DATE) .
		HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Sent") . "TimeToTimeHHMM", null, date("23:59"), true, INPUT_TYPE_TIME),
	"{$Caption}", null, true),

	//HTML\UI\Field(HTML\UI\Input("{$Configuration["SearchInputPrefix"]}{$Entity}" . ($Caption = "Signature") . "", 200), "{$Caption}", null, true),
	HTML\UI\Field(HTML\UI\Select("{$Configuration["SearchInputPrefix"]}{$Entity}Is" . ($Caption = "Active") . "", [new Option(), new Option(0, "No"), new Option(1, "Yes")]), "{$Caption}", null, true),
]);

print "
	{$EM->ListHTML()}
	" . (SetVariable("SucceededAction") == "Input" ? "<script>sJS.DHTML.Toast('{$Table["{$Entity}"]->FormalName()} input successful.');</script>" : null) . "
";
#region List
?>