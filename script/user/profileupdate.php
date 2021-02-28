<?php
namespace sPHP;

$Entity = "User";
$LowercaseEntity = strtolower($Entity);

$Form = new HTML\UI\Form(null, null, null, $APP->EncryptionKey(), null, null, null, null,
	SetVariable("_ID"),
	null, null, null,
	[
		new HTTP\InputValidation("{$Entity}Email", true, VALIDATION_TYPE_EMAIL, "Email address"),
		new HTTP\InputValidation("{$Entity}NameFirst", true, null, null),
		new HTTP\InputValidation("{$Entity}NameLast", true, null, null),
		new HTTP\InputValidation("{$Entity}PhoneMobile", true, null, null),
	]
);

if($Form->Verify($APP->EncryptionKey())){
	if($_POST["{$Entity}Password"] == $_POST["{$Entity}PasswordAgain"]){
		if(!count($TBL["{$Entity}"]->Get("{$Entity}Email = '" . $DTB->Escape($_POST["{$Entity}Email"]) . "' AND {$Entity}ID <> {$SSN->User()->ID()}"))){
			if(!count($TBL["{$Entity}"]->Get("{$Entity}PhoneMobile = '" . $DTB->Escape($_POST["{$Entity}PhoneMobile"]) . "' AND {$Entity}ID <> {$SSN->User()->ID()}"))){
				Upload($EntityUploadPath = "{$ENV->UploadPath()}{$LowercaseEntity}/");
				$FilePOSTKey = array_keys($_FILES); // Key list of file POST variables

				// Load existing record
				$Record = $TBL["{$Entity}"]->Get("{$TBL["{$Entity}"]->Structure()["Primary"][0]} = {$SSN->User()->ID()}")[0];

				// Delete existing files for uploaded new file or when asked to delete
				foreach($FilePOSTKey as $Column){
					// Set POST value for no file uploaded
					if(!isset($_POST["{$Column}"]) || $_POST["{$Column}"] === false)$_POST["{$Column}"] = null;

					if($Record["{$Column}"]){ // File data exists
						if(
								// New file uploaded or asked to delete existing file
								($_POST["{$Column}"] || isset($_POST["__DeleteExistingFile_{$Column}"]))
							&&	file_exists($ExistingFile = "{$EntityUploadPath}{$Record["{$Column}"]}") // File exists
						)unlink($ExistingFile); // Delete existing file

						if( // No file uploaded and didn't ask to delete existing file either
							!$_POST["{$Column}"] && !isset($_POST["__DeleteExistingFile_{$Column}"])
						)$_POST["{$Column}"] = $Record["{$Column}"]; // Set existing value to the POST variable
					}
				}

				// Create thumbnail
				foreach(ListToArray("{$Entity}Picture") as $Column){
					$_POST[$ThumbnailField = "{$Column}Thumbnail"] = $_POST[$Column] ? ($_POST[$Column] != $Record[$Column] ? Graphic\Resample("{$EntityUploadPath}{$_POST[$Column]}", $Configuration["ThumbnailMaximumDimension"], $Configuration["ThumbnailMaximumDimension"]) : $Record[$ThumbnailField]) : null;
					if(isset($Record) && $_POST[$ThumbnailField] != $Record[$ThumbnailField] && $Record[$ThumbnailField] && file_exists($ExistingFile = "{$EntityUploadPath}{$Record[$ThumbnailField]}"))unlink($ExistingFile);
				}

				#region Custom data
				if($_POST["{$Entity}Password"])$_POST["{$Entity}PasswordHash"] = md5($_POST["{$Entity}Password"]);
				#endregion Custom data

				$TBL["{$Entity}"]->Put($_POST, "{$Entity}ID = {$SSN->User()->ID()}");

				print HTML\UI\MessageBox("Profile information updated.", "System");

				$SSN->User()->Picture($_POST["{$Entity}Picture"]);
				$SSN->User()->PictureThumbnail($_POST["{$Entity}PictureThumbnail"]);
			}
			else{
				$Form->ErrorMessage("Mobile phone number is already associated with another user!");
			}
		}
		else{
			$Form->ErrorMessage("Email address is already associated with another user!");
		}
	}
	else{
		$Form->ErrorMessage("Passwords must match");
	}
}

require __DIR__ . "/profile.php";
?>