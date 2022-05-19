<?php
namespace sPHP;
$Entity = "User";

$Record = $Table["{$Entity}"]->Get($SQL = "
		U.{$Entity}Email = '{$Database->Escape(SetVariable("{$Entity}Email"))}'
	AND	U.{$Entity}PasswordResetKey = '{$Database->Escape(SetVariable("{$Entity}PasswordResetKey"))}'
	AND	TIME_TO_SEC(TIMEDIFF(NOW(), U.{$Entity}PasswordResetAttemptTime)) < 5 * 60
	AND	U.{$Entity}IsActive = 1
"); //DebugDump($SQL);

if(count($Record)){ // User record found
	$Record = $Record[0];
	$Password = RandomString();
	$PasswordHashSalt = RandomString();

	$Table["{$Entity}"]->Put([
		"UserPasswordHashSalt" => $PasswordHashSalt,
		"{$Entity}PasswordHash" => md5($Password . $PasswordHashSalt),
		"{$Entity}PasswordResetKey" => null,
		"{$Entity}PasswordResetAttemptTime" => null,
		"{$Entity}PasswordResetAttemptCount" => null,
	], "{$Entity}ID = {$Record["{$Entity}ID"]}");

	Comm\Mail(new Comm\MailContact($Record["{$Entity}Email"], $Record["{$Entity}SignInName"]), "Account password reset", "
		<style>
			{$Configuration["EmailCSS"]}
		</style>

		{$Configuration["EmailHeader"]}

		Dear <b>{$Record["{$Entity}Name"]}</b>,<br>
		<br>
		Your account password has successfully been reset. Find below your new password, and change it from your user profile area if required.<br>
		<br>
		<span style=\"display: inline-block; box-shadow: 0 0 5px 0 Black; border: 1px White solid; padding: 15px; color: Red; font-size: 150%; letter-spacing: 5px; user-select: all;\">{$Password}</span><br>
		<br>
		Warm regards,<br>
		<br>
		<a href=\"{$Application->URL()}\" style=\"display: inline-block; color: Navy; font-weight: bold; text-decoration: none;\">{$Application->Name()}</a>

		{$Configuration["EmailFooter"]}
	", new Comm\MailContact($Configuration["EmailFromAddress"], $Application->Name()), $Configuration["SMTPBodyStyle"], $Environment->MailLogPath(), null, null, null, null, null, null, $Configuration["SMTPHost"], $Configuration["SMTPPort"], $Configuration["SMTPUser"], $Configuration["SMTPPassword"]);

	print HTML\UI\MessageBox("
		Your account password has succefully been reset.<br>
		<br>
		We have sent you an email with the new password.<br>
		<br>
		<a href=\"{$Application->URL("User/SignIn")}\">Click here</a> to sign into your user account.
	", $Application->Name(), null);
}
else{ // User record not found
	print HTML\UI\MessageBox("Sorry, no such request could be found!", "Error", "MessageBox_Error");
}
?>