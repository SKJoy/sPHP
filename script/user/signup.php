<?php
namespace sPHP;

$Entity = "User";
$LowercaseEntityName = strtolower($Entity);

print "
	<div class=\"AlignCenter\">
		" . HTML\UI\Form(
			$Application->URL("{$Entity}/SignUpAction"), // Submission URL
			"
				" . HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Email") . "", $Configuration["InputWidth"], null, true, INPUT_TYPE_EMAIL), "{$Caption}", null, null, $Configuration["FieldCaptionWidth"], null, null, null, null) . "
				" . HTML\UI\Field(HTML\UI\Input("{$Entity}SignIn" . ($Caption = "") . "Name", $Configuration["InputWidth"], null, null, null), "{$Caption}Username", true, null, $Configuration["FieldCaptionWidth"], null, null, null, "Optional") . "
			",
			"Sign up", // Submit button caption
			$Application->EncryptionKey(), // Signature modifier
			"<img src=\"{$Environment->IconURL()}user.png\" alt=\"User\" class=\"Icon\">{$Entity} sign up", // Title
			"Fill in your desired Username and Email address", // Header
			null, // Footer
			null, // Status
			"frmUserSignUp" // ID
		) . "
	</div>
";
?>