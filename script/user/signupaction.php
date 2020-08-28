<?php
namespace sPHP;
$Entity = "User";

$Form = new HTML\UI\Form(null, null, null, $Application->EncryptionKey(), null, null, null, null,
	$_POST["_ID"],
	null, null, null,
	[
		new HTTP\InputValidation("{$Entity}Email", true, VALIDATION_TYPE_EMAIL, "Email address"),
		new HTTP\InputValidation("{$Entity}SignIn", null, null, null),
	]
);

$Result = false;

if($Form->Verify($Application->EncryptionKey())){
	$Table["{$Entity}"]->Remove("{$Entity}IsActive = 0 AND {$Entity}SignUpIsActivated = 0 AND {$Entity}SignUpActivationKey > '' AND DATEDIFF(NOW(), {$Entity}SignUpTime) > 1", null, null);

	// As Sign in name is optional to provider with, set it to Email if found empty
	if(!$_POST["{$Entity}SignInName"])$_POST["{$Entity}SignInName"] = $_POST["{$Entity}Email"];

	if(!count($Table["{$Entity}"]->Get("U.{$Entity}Email = '" . $Database->Escape($_POST["{$Entity}Email"]) . "' OR U.{$Entity}SignInName = '" . $Database->Escape($_POST["{$Entity}SignInName"]) . "'", null, null, null, null, null, null))){
		#region Custom data
		// Set First and Last names from Sign in name
		$Name = explode(" ", str_replace("  ", " ", $_POST["{$Entity}SignInName"]));
		$_POST["{$Entity}NameFirst"] = $Name[0];
		if(count($Name) > 1)$_POST["{$Entity}NameLast"] = $Name[count($Name) - 1];

		$_POST["{$Entity}SignUpTime"] = date("Y-m-d");
		$_POST["{$Entity}SignUpActivationKey"] = "" . GUID() . "" . GUID() . "";
		$_POST["{$Entity}SignUpIsActivated"] = false;
		$_POST["{$Entity}IsActive"] = false;
		#endregion Custom data

		$Table["{$Entity}"]->Put($_POST);

		$Table["{$Entity}{$Entity}Group"]->Put([
			"{$Entity}ID" => $Table["{$Entity}"]->Get("{$Entity}Email = '" . $Database->Escape($_POST["{$Entity}Email"]) . "'")[0]["{$Entity}ID"],
			"{$Entity}GroupID" => $Table[$OptionEntity = "{$Entity}Group"]->Get("{$OptionEntity}Identifier = 'MEMBER'")[0]["{$OptionEntity}ID"],
			"{$Entity}{$Entity}GroupIsActive" => 1,
		]);

		$SignUpActivationURL = $Application->URL("User/Activate", "{$Entity}SignUpActivationKey={$_POST["{$Entity}SignUpActivationKey"]}");

		Comm\Mail(new Comm\MailContact($_POST["{$Entity}Email"], $_POST["{$Entity}SignInName"]), "User sign up", "
			<style>
				{$Configuration["EmailCSS"]}
			</style>

			{$Configuration["EmailHeader"]}

			Dear <b>{$_POST["{$Entity}SignInName"]}</b>,<br>
			<br>
			Welcome to <strong>{$Application->Name()}</strong>! Please click the link below to activate your user account.<br>
			<br>
			<a href=\"{$SignUpActivationURL}\" style=\"display: inline-block; box-shadow: 0 0 5px 0 Black; border: 1px White solid; background-color: Black; padding: 15px; color: White; text-decoration: none;\">{$SignUpActivationURL}</a><br>
			<br>
			You must activate your sign up request within 24 hours, otherwise the activation will expire.<br>
			<br>
			Warm regards,<br>
			<br>
			<a href=\"{$Application->URL()}\" style=\"display: inline-block; color: Navy; font-weight: bold; text-decoration: none;\">{$Application->Name()}</a>

			{$Configuration["EmailFooter"]}
		", new Comm\MailContact($Configuration["EmailFromAddress"], $Application->Name()), $Configuration["SMTPBodyStyle"], $Environment->MailLogPath(), null, null, null, null, null, null, $Configuration["SMTPHost"], $Configuration["SMTPPort"], $Configuration["SMTPUser"], $Configuration["SMTPPassword"]);

		print HTML\UI\MessageBox("
			Your user account has successfully been created.<br>
			<br>
			Please check your email for an account activation link.<br>
			<br>
			The activation link is valid within 24 hours only.
		", $Application->Name());

		$Result = true;
	}
	else{
		$Form->ErrorMessage("Email address is already in use!");
	}
}

if(!$Result)require __DIR__ . "/signup.php";
?>