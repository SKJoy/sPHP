<?php
namespace sPHP;

$EntityName = "User";
$LowercaseEntityName = strtolower($EntityName);

foreach($TBL["{$EntityName}"]->Get("{$EntityName}ID = {$SSN->User()->ID()}")[0] as $Field => $Value)SetVariable($Field, $Value);

print "
	<div class=\"AlignCenter\">
		" . HTML\UI\Form(
			$APP->URL("{$EntityName}/ProfileUpdate"), // Submission URL
			"
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}" . ($Caption = "Email") . "", $CFG["InputWidth"], null, true, INPUT_TYPE_EMAIL), "{$Caption}", null, null, $CFG["FieldCaptionWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}" . ($Caption = "Password") . "", $CFG["InputInlineWidth"], null, null, INPUT_TYPE_PASSWORD, null, null, ["OnChange"=>"CheckPassword();"], "{$EntityName}PasswordInput"), "{$Caption}", true, null, $CFG["FieldCaptionInlineWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}Password" . ($Caption = "Again") . "", $CFG["InputInlineWidth"], null, null, INPUT_TYPE_PASSWORD, null, null, ["OnKeyUp"=>"CheckPassword();"], "{$EntityName}PasswordAgainInput"), "{$Caption}", null, true, $CFG["FieldCaptionInlineWidth"], null, null, null, "", null, null, null, null, null, null, "{$EntityName}Password{$Caption}") . "

				<div class=\"SectionTitle\">General</div>
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}Name" . ($Caption = "First") . "", $CFG["InputWidth"], null, true, null), "Name: {$Caption}", true, null, $CFG["FieldCaptionWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}Name" . ($Caption = "Middle") . "", $CFG["InputInlineWidth"], null, null, null), "{$Caption}", null, true, $CFG["FieldCaptionInlineWidth"], null, null, null, "Optional") . "
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}Name" . ($Caption = "Last") . "", $CFG["InputWidth"], null, true, null), "Name: {$Caption}", true, null, $CFG["FieldCaptionWidth"]) . "

				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}" . ($Caption = "Picture") . "", $CFG["InputFullWidth"], "{$ENV->UploadURL()}{$LowercaseEntityName}/" . (isset($_POST["{$EntityName}{$Caption}"]) ? $_POST["{$EntityName}{$Caption}"] : null) . "", null, INPUT_TYPE_FILE), "{$Caption}", true, null, $CFG["FieldCaptionWidth"], null, null, null, "Optional") . "

				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}" . ($Caption = "Birth") . "Date", $CFG["InputDateWidth"], null, true, INPUT_TYPE_DATE), "Date: {$Caption}", true, null, $CFG["FieldCaptionWidth"], null) . "
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}" . ($Caption = "Death") . "Date", $CFG["InputDateWidth"], null, null, INPUT_TYPE_DATE), "{$Caption}", null, true, null, null, null, null, "Optional") . "
				" . HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Gender") . "ID", $TBL[$OptionEntity = "{$Caption}"]->Get("{$TBL["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1", "{$OptionEntity}LookupCaption ASC"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", true, null, $CFG["FieldCaptionWidth"], null) . "
				" . HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Language") . "ID", $TBL[$OptionEntity = "{$Caption}"]->Get("{$TBL["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1", "{$OptionEntity}LookupCaption ASC"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", null, true, null) . "

				<div class=\"SectionTitle\">Contact</div>
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}Phone" . ($Caption = "Mobile") . "", $CFG["InputWidth"], null, true, null), "Phone: {$Caption}", true, null, $CFG["FieldCaptionWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}Phone" . ($Caption = "Home") . "", $CFG["InputInlineWidth"], null, null, null), "{$Caption}", null, true, $CFG["FieldCaptionInlineWidth"], null, null, null, "Optional") . "
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}Phone" . ($Caption = "Work") . "", $CFG["InputWidth"], null, null, null), "Phone: {$Caption}", true, null, $CFG["FieldCaptionWidth"], null, null, null, "Optional") . "
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}Phone" . ($Caption = "Other") . "", $CFG["InputInlineWidth"], null, null, null), "{$Caption}", null, true, $CFG["FieldCaptionInlineWidth"], null, null, null, "Optional") . "

				<div class=\"SectionTitle\">Address</div>
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}Address" . ($Caption = "Street") . "", $CFG["InputFullWidth"], null, null, null), "{$Caption}", true, null, $CFG["FieldCaptionWidth"], null, null, null, "Optional") . "
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}Address" . ($Caption = "City") . "", $CFG["InputWidth"], null, null, null), "{$Caption}", true, null, $CFG["FieldCaptionWidth"], null, null, null, "Optional") . "
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}Address" . ($Caption = "State") . "", $CFG["InputInlineWidth"], null, null, null), "{$Caption}", null, true, $CFG["FieldCaptionInlineWidth"], null, null, null, "Optional") . "
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}Address" . ($Caption = "ZIP") . "", $CFG["InputWidth"], null, null, null), "{$Caption}", true, null, $CFG["FieldCaptionWidth"], null, null, null, "Optional") . "
				" . HTML\UI\Field(HTML\UI\Select("{$EntityName}Address" . ($Caption = "Country") . "ID", $TBL[$OptionEntity = "{$Caption}"]->Get("{$TBL["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1", "{$OptionEntity}LookupCaption ASC"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", true, null, $CFG["FieldCaptionWidth"]) . "

				<div class=\"SectionTitle\">Other</div>
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}" . ($Caption = "URL") . "", $CFG["InputFullWidth"], null, null, null), "{$Caption}", true, null, $CFG["FieldCaptionWidth"], null, null, null, "Optional") . "
				" . HTML\UI\Field(HTML\UI\Textarea("{$EntityName}" . ($Caption = "Quote") . "", $CFG["InputFullWidth"], $CFG["TextareaHeight"], null, null, null), "{$Caption}", true, null, $CFG["FieldCaptionWidth"], null, null, null, "Optional") . "
			",
			"Update", // Submit button caption
			$APP->EncryptionKey(), // Signature modifier
			"<img src=\"{$ENV->IconURL()}user.png\" alt=\"User\" class=\"Icon\">{$EntityName} profile", // Title
			"Use the form below to update your {$LowercaseEntityName} profile.", // Header
			"Password is case sensitive.", // Footer
			"All except optional are required.", // Status
			"frmUserProfile" // ID
		) . "
	</div>

	<script>
		objUserPasswordAgainFieldFooter = document.getElementById('{$EntityName}PasswordAgain_Footer');
		PreviousUserPasswordAgainFieldFooter = document.getElementById('{$EntityName}PasswordAgain_Footer').innerHTML;

		function CheckPassword(){
			objUserPasswordAgainFieldFooter.innerHTML = document.getElementById('{$EntityName}PasswordInput').value == document.getElementById('{$EntityName}PasswordAgainInput').value ? PreviousUserPasswordAgainFieldFooter : '<span style=\"color: Red;\">Passwords must match</span>';

			return true;
		}
	</script>
";
?>