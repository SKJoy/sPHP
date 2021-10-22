<?php
namespace sPHP;

$EntityName = "User";
$EntityAlias = $TBL["{$EntityName}"]->Alias();
$LowercaseEntityName = strtolower($EntityName);

#region Make sure arguments are available for processing
$UserEmail = SetVariable("{$EntityName}Email");
$UserPassword = SetVariable("{$EntityName}Password");
#endregion Make sure arguments are available for processing

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
	$UserPasswordHash = md5($UserPassword);

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
						{$EntityAlias}.{$EntityName}Email = '{$DTB->Escape($UserEmail)}'
					OR	{$EntityAlias}.{$EntityName}SignInName = '{$DTB->Escape($UserEmail)}'
				)
			AND	{$EntityAlias}.{$EntityName}PasswordHash = MD5(CONCAT('{$DTB->Escape($UserPassword)}', IF(U.{$EntityName}PasswordHashSalt IS NULL, '', U.{$EntityName}PasswordHashSalt)))
			AND	{$EntityAlias}.{$EntityName}IsActive = 1
			AND	" . ($CFG["AdministratorAccessOnly"] ? "{$EntityAlias}.{$EntityName}ID IN ( # Administrator access only
					SELECT			{$EntityAlias}{$EntityAlias}G.{$EntityName}ID 
					FROM			sphp_userusergroup AS {$EntityAlias}{$EntityAlias}G 
						LEFT JOIN	sphp_usergroup AS {$EntityAlias}G ON {$EntityAlias}G.{$EntityName}GroupID = {$EntityAlias}{$EntityAlias}G.{$EntityName}GroupID
					WHERE			{$EntityAlias}G.{$EntityName}GroupIdentifier = 'ADMINISTRATOR' 
						AND			{$EntityAlias}{$EntityAlias}G.{$EntityName}ID = {$EntityAlias}.{$EntityName}ID
				)" : "TRUE"
			) . "
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
	$LOG->Put("{$SSN->User()->Name()} ({$SSN->User()->UserGroupIdentifierHighest()})", ["User" => ["Email" => $SSN->User()->Email(), ], "IP" => $_SERVER["REMOTE_ADDR"], ], null, LOG_TYPE_SECURITY, "Sign in", "User", "Application");
	if($CFG["UserSignInNotification"])$APP->NotifyUserDevice("{$SSN->User()->Name()} signed in on " . date("F d, Y H:i:s") . "", null, "User sign in", "ADMINISTRATOR");

	//$TRM->Redirect($_POST["_Referer"]);
	$TRM->Redirect($APP->URL());

	print HTML\UI\MessageBox("<a href=\"{$APP->URL()}\">Click here<a/> to continue.", "Security");	
}
else{
	$LOG->Put("Authentication failed", ["Email" => $UserEmail, "IP" => $_SERVER["REMOTE_ADDR"], ], null, LOG_TYPE_SECURITY, "Sign in", "User", "Application");
	
	require __DIR__ . "/signin.php";
}
?>