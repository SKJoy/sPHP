<?php
namespace sPHP;

$Entity = "User";

if(count($xUser = $TBL[$Entity]->Get("{$Entity}IsActive = 0 AND {$Entity}SignUpIsActivated = 0 AND {$Entity}SignUpActivationKey = '" . $DTB->Escape($_POST["{$Entity}SignUpActivationKey"]). "'"))){
	$Password = RandomString();

	$TBL[$Entity]->Put([
		"{$Entity}PasswordHash" => md5($Password),
		"{$Entity}SignUpActivationKey" => null,
		"{$Entity}SignUpIsActivated" => true,
		"{$Entity}IsActive" => true,
	], "{$Entity}ID = {$xUser[0]["{$Entity}ID"]}");

	Comm\Mail(new Comm\MailContact($xUser[0]["{$Entity}Email"], "{$xUser[0]["{$Entity}NameFirst"]} {$xUser[0]["{$Entity}NameLast"]}"), "Account activation", "
		<style>
			{$CFG["EmailCSS"]}
		</style>

		{$CFG["EmailHeader"]}

		Dear <b>{$xUser[0]["{$Entity}Name"]}</b>,<br>
		<br>
		Welcome to <b>{$APP->Name()}</b>! Your user account has successfully been activated.<br>
		<br>
		Email: <b>{$xUser[0]["{$Entity}Email"]}</b><br>
		Username: <b>{$xUser[0]["{$Entity}SignInName"]}</b><br>
		Password: <b>{$Password}</b> (case sensitive)<br>
		<br>
		Please <a href=\"{$APP->URL("User/SignIn")}\" style=\"color: Navy; text-decoration: none;\">click here</a> to sign into your account.<br>
		<br>
		Warm regards,<br>
		<br>
		<a href=\"{$APP->URL()}\" style=\"color: Navy; font-weight: bold; text-decoration: none;\">{$APP->Name()}</a>

		{$CFG["EmailFooter"]}
	", new Comm\MailContact($CFG["EmailFromAddress"], $CFG["Name"]), $CFG["SMTPBodyStyle"], $ENV->MailLogPath(), null, null, null, null, null, null, $CFG["SMTPHost"], $CFG["SMTPPort"], $CFG["SMTPUser"], $CFG["SMTPPassword"]);

	print HTML\UI\MessageBox("
		Your user account has successfully been activated.<br>
		<br>
		An email with your sign in credentials been sent to the email address you used during sign up.<br>
		<br>
		Please <a href=\"" . $APP->URL("User/SignIn") . "\">click here</a> to sign into your account.
	", "System");
}
else{
	print HTML\UI\MessageBox("Sorry, no such activation request found!", "Security");
}
?>