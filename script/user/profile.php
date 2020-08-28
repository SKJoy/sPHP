<?php
namespace sPHP;

$EntityName = "User";
$LowercaseEntityName = strtolower($EntityName);

foreach($Table["{$EntityName}"]->Get("{$EntityName}ID = {$Session->User()->ID()}")[0] as $Field=>$Value)SetVariable($Field, $Value);

print "
	<div class=\"AlignCenter\">
		" . HTML\UI\Form(
			$Application->URL("{$EntityName}/ProfileUpdate"), // Submission URL
			"
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}" . ($Caption = "Email") . "", $Configuration["InputWidth"], null, true, INPUT_TYPE_EMAIL), "{$Caption}", null, null, $Configuration["FieldCaptionWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}" . ($Caption = "Password") . "", $Configuration["InputInlineWidth"], null, null, INPUT_TYPE_PASSWORD, null, null, ["OnChange"=>"CheckPassword();"], "{$EntityName}PasswordInput"), "{$Caption}", true, null, $Configuration["FieldCaptionInlineWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}Password" . ($Caption = "Again") . "", $Configuration["InputInlineWidth"], null, null, INPUT_TYPE_PASSWORD, null, null, ["OnKeyUp"=>"CheckPassword();"], "{$EntityName}PasswordAgainInput"), "{$Caption}", null, true, $Configuration["FieldCaptionInlineWidth"], null, null, null, "", null, null, null, null, null, null, "{$EntityName}Password{$Caption}") . "

				<div class=\"SectionTitle\">General</div>
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}Name" . ($Caption = "First") . "", $Configuration["InputWidth"], null, true, null), "Name: {$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}Name" . ($Caption = "Middle") . "", $Configuration["InputInlineWidth"], null, null, null), "{$Caption}", null, true, $Configuration["FieldCaptionInlineWidth"], null, null, null, "Optional") . "
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}Name" . ($Caption = "Last") . "", $Configuration["InputWidth"], null, true, null), "Name: {$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "

				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}" . ($Caption = "Picture") . "", $Configuration["InputFullWidth"], "{$Environment->UploadURL()}{$LowercaseEntityName}/" . (isset($_POST["{$EntityName}{$Caption}"]) ? $_POST["{$EntityName}{$Caption}"] : null) . "", null, INPUT_TYPE_FILE), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"], null, null, null, "Optional") . "

				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}" . ($Caption = "Birth") . "Date", $Configuration["InputDateWidth"], null, true, INPUT_TYPE_DATE), "Date: {$Caption}", true, null, $Configuration["FieldCaptionWidth"], null) . "
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}" . ($Caption = "Death") . "Date", $Configuration["InputDateWidth"], null, null, INPUT_TYPE_DATE), "{$Caption}", null, true, null, null, null, null, "Optional") . "
				" . HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Gender") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1", "{$OptionEntity}LookupCaption ASC"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"], null) . "
				" . HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Language") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1", "{$OptionEntity}LookupCaption ASC"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", null, true, null) . "

				<div class=\"SectionTitle\">Contact</div>
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}Phone" . ($Caption = "Mobile") . "", $Configuration["InputWidth"], null, true, null), "Phone: {$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}Phone" . ($Caption = "Home") . "", $Configuration["InputInlineWidth"], null, null, null), "{$Caption}", null, true, $Configuration["FieldCaptionInlineWidth"], null, null, null, "Optional") . "
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}Phone" . ($Caption = "Work") . "", $Configuration["InputWidth"], null, null, null), "Phone: {$Caption}", true, null, $Configuration["FieldCaptionWidth"], null, null, null, "Optional") . "
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}Phone" . ($Caption = "Other") . "", $Configuration["InputInlineWidth"], null, null, null), "{$Caption}", null, true, $Configuration["FieldCaptionInlineWidth"], null, null, null, "Optional") . "

				<div class=\"SectionTitle\">Address</div>
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}Address" . ($Caption = "Street") . "", $Configuration["InputFullWidth"], null, null, null), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"], null, null, null, "Optional") . "
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}Address" . ($Caption = "City") . "", $Configuration["InputWidth"], null, null, null), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"], null, null, null, "Optional") . "
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}Address" . ($Caption = "State") . "", $Configuration["InputInlineWidth"], null, null, null), "{$Caption}", null, true, $Configuration["FieldCaptionInlineWidth"], null, null, null, "Optional") . "
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}Address" . ($Caption = "ZIP") . "", $Configuration["InputWidth"], null, null, null), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"], null, null, null, "Optional") . "
				" . HTML\UI\Field(HTML\UI\Select("{$EntityName}Address" . ($Caption = "Country") . "ID", $Table[$OptionEntity = "{$Caption}"]->Get("{$Table["{$OptionEntity}"]->Alias()}.{$OptionEntity}IsActive = 1", "{$OptionEntity}LookupCaption ASC"), null, "{$OptionEntity}LookupCaption"), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "

				<div class=\"SectionTitle\">Other</div>
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}" . ($Caption = "URL") . "", $Configuration["InputFullWidth"], null, null, null), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"], null, null, null, "Optional") . "
				" . HTML\UI\Field(HTML\UI\Textarea("{$EntityName}" . ($Caption = "Quote") . "", $Configuration["InputFullWidth"], $Configuration["TextareaHeight"], null, null, null), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"], null, null, null, "Optional") . "
			",
			"Update", // Submit button caption
			$Application->EncryptionKey(), // Signature modifier
			"<img src=\"{$Environment->IconURL()}user.png\" alt=\"User\" class=\"Icon\">{$EntityName} profile", // Title
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