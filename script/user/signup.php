<?php
namespace sPHP;

$Entity = "User";
$LowercaseEntityName = strtolower($Entity);

print "
	<div class=\"AlignCenter\">
		" . HTML\UI\Form(
			$APP->URL("{$Entity}/SignUpAction"), // Submission URL
			"
				" . HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Email") . "", $CFG["InputWidth"], null, true, INPUT_TYPE_EMAIL), "{$Caption}", null, null, $CFG["FieldCaptionWidth"], null, null, null, null) . "
				" . HTML\UI\Field(HTML\UI\Input("{$Entity}SignIn" . ($Caption = "") . "Name", $CFG["InputWidth"], null, null, null), "{$Caption}Username", true, null, $CFG["FieldCaptionWidth"], null, null, null, "Optional") . "
			",
			"Sign up", // Submit button caption
			$APP->EncryptionKey(), // Signature modifier
			"<img src=\"{$ENV->IconURL()}user.png\" alt=\"User\" class=\"Icon\">{$Entity} sign up", // Title
			"Fill in your desired Username and Email address", // Header
			null, // Footer
			null, // Status
			"frmUserSignUp" // ID
		) . "
	</div>
";
?>