<?php
namespace sPHP;

$EntityName = "User";
$LowercaseEntityName = strtolower($EntityName);

$Form = new HTML\UI\Form(null, null, null, $Application->EncryptionKey(), null, null, null, null,
	SetVariable("_ID"),
	null, null, null,
	[
		new HTTP\InputValidation("{$EntityName}Email", true, null, "Email address"),
		new HTTP\InputValidation("{$EntityName}Password", true, null, "Password"),
	]
);

$Result = false;

if($Form->Verify($Application->EncryptionKey())){
	$Result = false;
	//if(strpos($_POST["{$EntityName}Email"], "@") === false)$_POST["{$EntityName}Email"] = "{$_POST["{$EntityName}Email"]}@System.Dom";
	$UserPasswordHash = md5($_POST["{$EntityName}Password"]);

	if(is_null($Database->Connection())){
		if($_POST["{$EntityName}Email"] == $Application->Administrator()->Email() && $UserPasswordHash == $Application->Administrator()->PasswordHash()){
			$Result = true;
			$Session->User($Application->Administrator());
		}
		else{
			$Form->ErrorMessage("Sorry, email or password didn't match!");
		}
	}
	else{
		if(count($UserRecord = $Table["{$EntityName}"]->Get("
				(
						{$Table["{$EntityName}"]->Alias()}.{$EntityName}Email = '" . $Database->Escape($_POST["{$EntityName}Email"]) . "'
					OR	{$Table["{$EntityName}"]->Alias()}.{$EntityName}SignInName = '" . $Database->Escape($_POST["{$EntityName}Email"]) . "'
				)
			AND	{$Table["{$EntityName}"]->Alias()}.{$EntityName}PasswordHash = '{$UserPasswordHash}'
			AND	{$Table["{$EntityName}"]->Alias()}.{$EntityName}IsActive = 1
		", null, null, null, null, null, null))){
			$Result = true;

			$Session->User(new User(
				$UserRecord[0]["{$EntityName}Email"],
				$UserRecord[0]["{$EntityName}PasswordHash"],
				$UserRecord[0]["{$EntityName}Name"],
				$UserRecord[0]["{$EntityName}Phone"],
				null,
				$UserRecord[0]["{$EntityName}URL"],
				$UserRecord[0]["{$EntityName}Picture"],
				$UserRecord[0]["{$EntityName}PictureThumbnail"],
				$UserRecord[0]["{$EntityName}ID"],
				$UserRecord[0]["{$EntityName}GroupIdentifier"],
				$UserRecord[0]["{$EntityName}GroupWeight"],
				max(explode("; ", $UserRecord[0]["{$EntityName}GroupWeight"])),
				$UserRecord[0]["{$EntityName}GroupName"],
				explode("; ", $UserRecord[0]["{$EntityName}GroupIdentifier"])[0]
			));
		}
		else{
			$Form->ErrorMessage("Sorry, email or password didn't match!");
		}
	}
}
else{
	$Form->ErrorMessage("Sorry, {$LowercaseEntityName} couldn't be authenticated!");
}

if($Result){
	$Application->NotifyUserDevice("{$Session->User()->Name()} signed in on " . date("F d, Y H:i:s") . "", null, "User sign in", "ADMINISTRATOR");
	//$Terminal->Redirect($_POST["_Referer"]);
	$Terminal->Redirect($Application->URL());
}
else{
	require __DIR__ . "/signin.php";
}
?>