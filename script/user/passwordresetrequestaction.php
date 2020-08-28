<?php
namespace sPHP;
$Entity = "User";
$Record = $Table[$Entity]->Get("U.{$Entity}Email = '{$Database->Escape(SetVariable("{$Entity}Email"))}' AND U.{$Entity}IsActive = 1");

if(count($Record)){ // User record found
	$Record = $Record[0];
	$PasswordResetKey = "" . GUID() . "" . GUID() . "";

	$Table[$Entity]->Put([
		"{$Entity}PasswordResetKey" => $PasswordResetKey,
		"{$Entity}PasswordResetAttemptTime" => date("Y-m-d H:i:s"),
		"{$Entity}PasswordResetAttemptCount" => $Record["{$Entity}PasswordResetAttemptCount"] + 1,
	], "{$Entity}ID = {$Record["{$Entity}ID"]}");

	$PasswordResetURL = $Application->URL("{$Entity}/PasswordReset", "{$Entity}Email={$Record["{$Entity}Email"]}&{$Entity}PasswordResetKey={$PasswordResetKey}");

	Comm\Mail(new Comm\MailContact($Record["{$Entity}Email"], $Record["{$Entity}SignInName"]), "Account password reset request", "
		<style>
			{$Configuration["EmailCSS"]}
		</style>

		{$Configuration["EmailHeader"]}

		Dear <b>{$Record["{$Entity}Name"]}</b>,<br>
		<br>
		You (or someone else) has requested a password reset for your user account with us. Please ignore this email if you did not request this.<br>
		<br>
		Click the password reset link below to reset your account password.<br>
		<br>
		<a href=\"{$PasswordResetURL}\" style=\"display: inline-block; box-shadow: 0 0 5px 0 Black; border: 1px White solid; background-color: Black; padding: 15px; color: White; text-decoration: none;\">{$PasswordResetURL}</a><br>
		<br>
		You must complete your password reset request within 5 minutes, otherwise the request will expire.<br>
		<br>
		Warm regards,<br>
		<br>
		<a href=\"{$Application->URL()}\" style=\"display: inline-block; color: Navy; font-weight: bold; text-decoration: none;\">{$Application->Name()}</a>

		{$Configuration["EmailFooter"]}
	", new Comm\MailContact($Configuration["EmailFromAddress"], $Application->Name()), $Configuration["SMTPBodyStyle"], $Environment->MailLogPath(), null, null, null, null, null, null, $Configuration["SMTPHost"], $Configuration["SMTPPort"], $Configuration["SMTPUser"], $Configuration["SMTPPassword"]);
}
else{ // User record not found

}

print HTML\UI\MessageBox("
	We just sent you an email with the password recovery instruction.<br>
	<br>
	Please check your email and follow.
", $Application->Name());
?>