<?php
namespace sPHP;

$EntityName = "User";
$LowercaseEntityName = strtolower($EntityName);

print "
	<div class=\"AlignCenter\">
		" . HTML\UI\Form(
			$Application->URL("{$EntityName}/SignInAction"), // Submission URL
			"
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}" . ($Caption = "Email") . "", $Configuration["InputWidth"], null, true, INPUT_TYPE_EMAIL), "Username / {$Caption}", null, null, $Configuration["FieldCaptionWidth"]) . "
				" . HTML\UI\Field(HTML\UI\Input("{$EntityName}" . ($Caption = "Password") . "", $Configuration["InputWidth"], null, true, INPUT_TYPE_PASSWORD), "{$Caption}", true, null, $Configuration["FieldCaptionWidth"]) . "
			",
			"Sign in", // Submit button caption
			$Application->EncryptionKey(), // Signature modifier
			"<img src=\"{$Environment->IconURL()}user.png\" alt=\"User\" class=\"Icon\">{$EntityName} sign in", // Title
			"Use the form below to sign into your {$LowercaseEntityName} account.", // Header
			"Password is case sensitive.", // Footer
			"Both email address and password are required.", // Status
			"frmUserSignIn" // ID
		) . "
	</div>
";
?>