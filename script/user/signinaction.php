<?php
namespace sPHP;

$EntityName = "User";
$LowercaseEntityName = strtolower($EntityName);

$Form = new HTML\UI\Form(null, null, null, $APP->EncryptionKey(), null, null, null, null,
	SetVariable("_ID"),
	null, null, null,
	[
		new HTTP\InputValidation("{$EntityName}Email", true, null, "Email address"),
		new HTTP\InputValidation("{$EntityName}Password", true, null, "Password"),
	]
);

$Result = false;

if($Form->Verify($APP->EncryptionKey())){
	$UserPasswordHash = md5($_POST["{$EntityName}Password"]);

	if(is_null($DTB->Connection())){ // Static system without a database back end
		if($_POST["{$EntityName}Email"] == $APP->Administrator()->Email() && $UserPasswordHash == $APP->Administrator()->PasswordHash()){
			$Result = true;
			$SSN->User($APP->Administrator());
		}
		else{
			$Form->ErrorMessage("Sorry, email or password didn't match!");
		}
	}
	else{ // Dynamic system with a database back end
		if(count($UserRecord = $TBL["{$EntityName}"]->Get($SQL_WHERE = "
				(
						{$TBL["{$EntityName}"]->Alias()}.{$EntityName}Email = '" . $DTB->Escape($_POST["{$EntityName}Email"]) . "'
					OR	{$TBL["{$EntityName}"]->Alias()}.{$EntityName}SignInName = '" . $DTB->Escape($_POST["{$EntityName}Email"]) . "'
				)
			AND	{$TBL["{$EntityName}"]->Alias()}.{$EntityName}PasswordHash = '{$UserPasswordHash}'
			AND	{$TBL["{$EntityName}"]->Alias()}.{$EntityName}IsActive = 1
			AND	" . ($CFG["AdministratorAccessOnly"] ? "(
					SELECT			UG.UserGroupIdentifier
					FROM			sphp_userusergroup AS UUG
						LEFT JOIN	sphp_usergroup AS UG ON UG.UserGroupID = UUG.UserGroupID
						LEFT JOIN	sphp_user AS U2 ON U2.UserID = UUG.UserID
					WHERE			(U2.UserEmail = '" . $DTB->Escape($_POST["{$EntityName}Email"]) . "' OR U2.UserSignInName = '" . $DTB->Escape($_POST["{$EntityName}Email"]) . "')
						AND			U2.UserPasswordHash = '{$UserPasswordHash}'
					ORDER BY		UG.UserGroupWeight DESC
					LIMIT			1
				) = 'ADMINISTRATOR'" : "TRUE") . " # AdministratorAccessOnly = " . ($CFG["AdministratorAccessOnly"] ? "TRUE" : "FALSE") . "
		", null, null, null, null, null, null))){ //DebugDump($SQL_WHERE);
			$Result = true;

			$SSN->User(new User(
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
			if($CFG["AdministratorAccessOnly"] && $CFG["RestrictedAccessMessage"]){
				print HTML\UI\MessageBox($CFG["RestrictedAccessMessage"], "Security", "MessageBoxError");
				$Form->ErrorMessage($CFG["RestrictedAccessMessage"]);
			}
			else{
				$Form->ErrorMessage("Sorry, email or password didn't match!");
			}
		}
	}
}
else{
	$Form->ErrorMessage("Sorry, {$LowercaseEntityName} couldn't be authenticated!");
}

if($Result){
	if($CFG["UserSignInNotification"])$APP->NotifyUserDevice("{$SSN->User()->Name()} signed in on " . date("F d, Y H:i:s") . "", null, "User sign in", "ADMINISTRATOR");
	//$TRM->Redirect($_POST["_Referer"]);
	$TRM->Redirect($APP->URL());

	print HTML\UI\MessageBox("<a href=\"{$APP->URL()}\">Click here<a/> to continue.", "Security");
}
else{
	require __DIR__ . "/signin.php";
}
?>