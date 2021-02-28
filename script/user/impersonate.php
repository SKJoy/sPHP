<?php
namespace sPHP;

if(isset($_POST["btnSubmit"])){
	$Result = Impersonate($USR, $APP, SetVariable("UserEmail"), $SSN);

	if($Result === true){
		print HTML\UI\MessageBox("Impersonation authenticated and intiated. Please <a href=\"{$APP->URL()}\">click here</a> to proceed.");
		$APP->NotifyUserDevice("{$USR->Name()} impersonated \"{$_POST["UserEmail"]}\" on " . date("F d, Y H:i:s") . "", null, "Impersonation", "ADMINISTRATOR");
		//$APP->Terminal()->Redirect($APP->URL());
	}
	else{
		print HTML\UI\MessageBox("<ul><li>" . implode("</li><li>", array_column($Result, "Message")) . "</li></ul>", "Error");
	}
}
else{
	print HTML\UI\Form(
		$APP->URL($_POST["_Script"]),
		implode(null, [
			HTML\UI\Field(HTML\UI\Input("User" . ($Caption = "Email") . "", $CFG["InputWidth"], null, true, null, null, "Required"), "Username / {$Caption}", null, null, $CFG["FieldCaptionWidth"]),
		]),
		"Impersonate",
		null,
		"Impersonate another user",
		"Type in the sign in name or email address of another user to impersonate for.",
		"Your own session will end and a new session will be started for the impersonated user.",
		"You will need to sign back in for your own account."
	);
}
?>