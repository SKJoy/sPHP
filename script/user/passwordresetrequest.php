<?php
namespace sPHP;

$Entity = "User";

if($Database->Connection()){
	print "
		<div class=\"AlignCenter\">
			" . HTML\UI\Form(
				$Application->URL("{$Entity}/PasswordResetRequestAction"), // Submission URL
				"
					" . HTML\UI\Field(HTML\UI\Input("{$Entity}" . ($Caption = "Email") . "", $Configuration["InputWidth"], null, true, INPUT_TYPE_EMAIL), "{$Caption}", null, null, $Configuration["FieldCaptionWidth"], null, null, null, null) . "
				",
				"Recover", // Submit button caption
				$Application->EncryptionKey(), // Signature modifier
				"<img src=\"{$Environment->IconURL()}password.png\" alt=\"Password\" class=\"Icon\">{$Entity} password recovery", // Title
				"Fill in with your email address", // Header
				null, // Footer
				null, // Status
				"frm{$Entity}PasswordRecover" // ID
			) . "
		</div>
	";
}
else{
	print HTML\UI\MessageBox("Sorry, this feature is not available at this moment!", null, "MessageBox_Error");
}
?>