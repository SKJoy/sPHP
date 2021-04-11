<?php
/*
    Name:           Function
    Purpose:        System object shortcut/alias function library
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Created:  		May 30, 2018 05:30 PM
    Modified:  		June 7, 2018 01:45 AM
*/

namespace sPHP{
	function SetVariable($Name, $DefaultValue = null, $GET = true, $POST = true, $SESSION = false, $COOKIE = false){
		global $Environment;

		return $Environment->SetVariable($Name, $DefaultValue, $GET, $POST, $SESSION, $COOKIE);
	}

	function SetVar($Name, $DefaultValue = null, $GET = true, $POST = true, $SESSION = false, $COOKIE = false){
		return SetVariable($Name, $DefaultValue, $GET, $POST, $SESSION, $COOKIE);
	}

	function Upload($Path, $Field = null, $SetPOST = null, $MustRename = null, $AllowedExtension = null, $ForbiddenExtension = null){
		global $Environment;

		return $Environment->Utility()->Upload($Path, $Field, $SetPOST, $MustRename, $AllowedExtension, $ForbiddenExtension);
	}

	function ValidFileName($Name, $ReplacementCharacter = "_"){
		global $Environment;

		return $Environment->Utility()->ValidFileName($Name, $ReplacementCharacter);
	}

	function ValidVariableName($Name, $ReplacementCharacter = "_"){
		global $Environment;

		return $Environment->Utility()->ValidVariableName($Name, $ReplacementCharacter);
	}

	function UniqueFileName($File){
		global $Environment;

		return $Environment->Utility()->UniqueFileName($File);
	}

	function GUID($Hyphen = false, $CurlyBrace = false){
		global $Environment;

		return $Environment->Utility()->GUID($Hyphen, $CurlyBrace);
	}

	function ListToArray($List, $Separator = null, $DiscardSpace = null, $IgnoreEmpty = null){
		global $Environment;

		return $Environment->Utility()->ListToArray($List, $Separator, $DiscardSpace, $IgnoreEmpty);
	}

	function NumberInWord($Number = false){
		global $Environment;

		return $Environment->Utility()->NumberInWord($Number);
	}

	function RandomString($Length = 8, $Number = true, $Lowercase = true, $Uppercase = true, $Symbol = true, $TagSafe = true){
		global $Environment;

		return $Environment->Utility()->RandomString($Length, $Number, $Lowercase, $Uppercase, $Symbol, $TagSafe);
	}

	function Redirect($URL, $Message = null){
		global $Terminal;

		return $Terminal->Redirect($URL, $Message);
	}

    function OpenGraphMetaTag($URL = null, $Title = null, $Descroption = null, $Image = null, $ImageTitle = null, $UpdateTime = null, $Type = null, $Locale = null){
        $OpenGraphMetaTag = new OpenGraphMetaTag($URL, $Title, $Descroption, $Image, $ImageTitle, $UpdateTime, $Type, $Locale);

        return $OpenGraphMetaTag->MetaHTML();
    }

	function DebugDump($Value, $Name = null, $Output = true, $CallerDepth = 1){
		global $Environment;

		// Control where to start the debug from, framework layer or application layer
		//$CallerDepth = 0; // Framework layer
		//$CallerDepth = 1; // Application layer

		return $Environment->Utility()->Debug()->Dump($Value, $Name, $Output, $CallerDepth);
	}

	function Impersonate($CurrentUser, $Application, $Email, $Session){
		$Database = $Application->Database();
		$EntityName = "User";
		$Error = [];

		if($CurrentUser->UserGroupIdentifierHighest() == "ADMINISTRATOR"){
			$UserRecord = $Database->Query("
				SELECT			U.UserID, U.UserEmail, U.UserPasswordHash, U.UserPicture, U.UserPictureThumbnail, U.UserURL,

								@UserName := CONCAT(
									U.UserNameFirst,
									IF(U.UserNameMiddle > '', CONCAT(' ', U.UserNameMiddle), ''),
									IF(U.UserNameLast > '', CONCAT(' ', U.UserNameLast), '')
								) AS UserName,

								IF(U.UserPhoneMobile IS NULL, IF(U.UserPhoneWork IS NULL, IF(U.UserPhoneHome IS NULL, IF(U.UserPhoneOther IS NULL, '', U.UserPhoneOther), U.UserPhoneHome), U.UserPhoneWork), U.UserPhoneMobile) AS UserPhone,

								(
									SELECT			GROUP_CONCAT(UG.UserGroupIdentifier ORDER BY UG.UserGroupWeight DESC SEPARATOR '; ')
									FROM			sphp_userusergroup AS UUG
										LEFT JOIN	sphp_usergroup AS UG ON UG.UserGroupID = UUG.UserGroupID
									WHERE			UUG.UserID = U.UserID
										AND			UUG.UserUserGroupIsActive = 1
										AND			UG.UserGroupIsActive = 1
									ORDER BY		UG.UserGroupWeight DESC
								) AS UserGroupIdentifier,

								(
									SELECT			GROUP_CONCAT(UG.UserGroupWeight ORDER BY UG.UserGroupWeight DESC SEPARATOR '; ')
									FROM			sphp_userusergroup AS UUG
										LEFT JOIN	sphp_usergroup AS UG ON UG.UserGroupID = UUG.UserGroupID
									WHERE			UUG.UserID = U.UserID
										AND			UUG.UserUserGroupIsActive = 1
										AND			UG.UserGroupIsActive = 1
									ORDER BY		UG.UserGroupWeight DESC
								) AS UserGroupWeight,

								(
									SELECT			GROUP_CONCAT(UG.UserGroupName ORDER BY UG.UserGroupWeight DESC SEPARATOR '; ')
									FROM			sphp_userusergroup AS UUG
										LEFT JOIN	sphp_usergroup AS UG ON UG.UserGroupID = UUG.UserGroupID
									WHERE			UUG.UserID = U.UserID
										AND			UUG.UserUserGroupIsActive = 1
										AND			UG.UserGroupIsActive = 1
									ORDER BY		UG.UserGroupWeight DESC
								) AS UserGroupName,

								'' AS _Other

				FROM			sphp_user AS U

				WHERE			TRUE

					AND			(
										U.UserEmail = '" . $Database->Escape($Email) . "'
									OR	U.UserSignInName = '" . $Database->Escape($Email) . "'
								)

					AND			U.UserIsActive = 1

				LIMIT			1
			");

			if(isset($UserRecord[0])){
				$UserRecord = $UserRecord[0][0];
				$UserGroupIdentifier = explode("; ", $UserRecord["{$EntityName}GroupIdentifier"]);

				if($UserGroupIdentifier[0] == "ADMINISTRATOR"){
					$Error[] = ["Code" => ERROR_CODE_INVALID, "Message" => "Administrator users cannot be impersonated", ];
				}
				else{
					$Session->Impersonate(new User(
						$UserRecord["{$EntityName}Email"],
						$UserRecord["{$EntityName}PasswordHash"],
						$UserRecord["{$EntityName}Name"],
						$UserRecord["{$EntityName}Phone"],
						null,
						$UserRecord["{$EntityName}URL"],
						$UserRecord["{$EntityName}Picture"],
						$UserRecord["{$EntityName}PictureThumbnail"],
						$UserRecord["{$EntityName}ID"],
						$UserRecord["{$EntityName}GroupIdentifier"],
						$UserRecord["{$EntityName}GroupWeight"],
						max(explode("; ", $UserRecord["{$EntityName}GroupWeight"])),
						$UserRecord["{$EntityName}GroupName"],
						$UserGroupIdentifier[0]
					));
				}
			}
			else{
				$Error[] = ["Code" => ERROR_CODE_NOT_FOUND, "Message" => "User not found to impersonate", ];
			}
		}
		else{
			$Error[] = ["Code" => ERROR_CODE_ADMINISTRATOR_ONLY, "Message" => "Only Administrators can impersonate other non Administrator users", ];
		}

		return count($Error) ? $Error : true;
	}

	function CreatePath($Path){
		global $Environment;

		return $Environment->Utility()->CreatePath($Path);
	}
	
	function SecondToTime($Second = 0){
		global $Environment;

		return $Environment->Utility()->SecondToTime($Second);
	}
}

namespace sPHP\Content{
	/*
		Shortcut for Value (GET) property of Content object
	*/function Get($Name = null, $DefaultValue = null, $Path = null, $Language = null, $FileName = null, $Debug = null){
		$Content = new \sPHP\Content($Name, $DefaultValue, $Path, null, $Language, $FileName);

		return $Content->Value(null, $Debug);
	}

	/*
		Shortcut for Value (SET) property of Content object
	*/function Set($Name = null, $Value = null, $Path = null, $Language = null, $FileName = null, $Debug = null){
		$Content = new \sPHP\Content($Name, null, $Path, null, $Language, $FileName);

		return $Content->Value($Value, $Debug);
	}

	/*
		Shortcut for EditAnchor property of Content object
	*/function EditAnchor($Name = null, $Path = null, $Language = null, $FileName = null, $Hide = false, $NewWindow = false){
		$Content = new \sPHP\Content($Name, null, $Path, null, $Language, $FileName);

		return $Content->EditAnchor($Hide, $NewWindow);
	}
}

namespace sPHP\Comm{
	function Mail($To = null, $Subject = null, $Message = null, $From = null, $BodyStyle = null, $LogPath = null, $Cc = null, $Bcc = null, $Attachement = null, $ReplyTo = null, $HTML = null, $Header = null, $Host = null, $Port = null, $User = null, $Password = null){
		$Mail = new Mail($To, $Subject, $Message, $From, $BodyStyle, $LogPath, $Cc, $Bcc, $Attachement, $ReplyTo, $HTML, $Header, $Host, $Port, $User, $Password);

		return $Mail->Send();
	}
}

namespace sPHP\HTML{
    function Meta($Name = null, $Content = null, $HTTPEquivalent = null, $Property = null, $CharacterSet = null){
        $Meta = new Meta($Name, $Content, $HTTPEquivalent, $Property, $CharacterSet);

        return $Meta->HTML();
    }
}

namespace sPHP\HTML\UI{
	function Progressbar($Maximum = null, $Value = null, $URL = null, $Target = null, $CSSSelector = null, $Tooltip = null, $Width = null, $Prefix = null, $Suffix = null, $EventHandlerJavaScript = null, $ID = null){
		$Object = new Progressbar($Maximum, $Value, $URL, $Target, $CSSSelector, $Tooltip, $Width, $Prefix, $Suffix, $EventHandlerJavaScript, $ID);

		return $Object->HTML();
    }

	function ChartJS($Recordset = null, $Dataset = null, $Label = null, $XAxes = null, $YAxes = null, $ID = null, $Type = null, $Title = null, $TitleFontColor = null, $TitleFontSize = null, $TitleFontStyle = null, $LegendFontColor = null, $LegendFontSize = null, $LegendFontStyle = null, $AspectRatio = null, $MaintainAspectRatio = null, $Responsive = null){
		$Object = new ChartJS($Recordset, $Dataset, $Label, $XAxes, $YAxes, $ID, $Type, $Title, $TitleFontColor, $TitleFontSize, $TitleFontStyle, $LegendFontColor, $LegendFontSize, $LegendFontStyle, $AspectRatio, $MaintainAspectRatio, $Responsive);

		return $Object->HTML();
    }

	function Datagrid($Data = null, $URL = null, $RecordCount = null, $Field = null, $Title = null, $RowPerPage = null, $DataIDColumn = null, $Action = null, $BaseURL = null, $IconBaseURL = null, $Footer = null, $PreHTML = null, $BatchAction = null, $ExpandURL = null, $Serial = null, $Selectable = null, $ID = null, $CSSSelector = null, $SerialCaption = null, $PaginatorPageCaption = null, $PaginatorRecordsCaption = null){
		$Object = new Datagrid($Data, $URL, $RecordCount, $Field, $Title, $RowPerPage, $DataIDColumn, $Action, $BaseURL, $IconBaseURL, $Footer, $PreHTML, $BatchAction, $ExpandURL, $Serial, $Selectable, $ID, $CSSSelector, $SerialCaption, $PaginatorPageCaption, $PaginatorRecordsCaption);

		return $Object->HTML();
    }

	function MessageBox($Content, $Title = null, $CSSSelector = null){
		$Object = new MessageBox($Content, $Title, $CSSSelector);

		return $Object->HTML();
    }

	function Button($Caption = null, $Type = null, $Name = null, $Value = null, $EventHandlerJavaScript = null, $Width = null, $CSSSelector = null, $ID = null, $Tooltip = null, $Icon = null){
		$Object = new Button($Caption, $Type, $Name, $Value, $EventHandlerJavaScript, $Width, $CSSSelector, $ID, $Tooltip, $Icon);

		return $Object->HTML();
	}

	function Input($Name = null, $Width = null, $DefaultValue = null, $Required = null, $Type = null, $CSSSelector = null, $Placeholder = null, $EventHandlerJavaScript = null, $ID = null, $Step = null, $Minimum = null, $Maximum = null, $ReadOnly = null){
		$Object = new Input($Name, $Width, $DefaultValue, $Required, $Type, $CSSSelector, $Placeholder, $EventHandlerJavaScript, $ID, $Step, $Minimum, $Maximum, $ReadOnly);

		return $Object->HTML();
	}

	function Textarea($Name = null, $Width = null, $Height = null, $DefaultValue = null, $Required = null, $CSSSelector = null, $Placeholder = null, $EventHandlerJavaScript = null, $ID = null){
		$Object = new Textarea($Name, $Width, $Height, $DefaultValue, $Required, $CSSSelector, $Placeholder, $EventHandlerJavaScript, $ID);

		return $Object->HTML();
	}

	function Select($Name = null, $Option = null, $PrependOption = null, $CaptionField = null, $AppendOption = null, $ValueField = null, $DefaultValue = null, $CSSSelector = null, $EventHandlerJavaScript = null, $ID = null){
		$Object = new Select($Name, $Option, $PrependOption, $CaptionField, $AppendOption, $ValueField, $DefaultValue, $CSSSelector, $EventHandlerJavaScript, $ID);

		return $Object->HTML();
	}

	function Radio($Value = null, $Caption = null, $Name = null, $CSSSelector = null, $EventHandlerJavaScript = null, $ID = null){
		$Object = new Radio($Value, $Caption, $Name, $CSSSelector, $EventHandlerJavaScript, $ID);

		return $Object->HTML();
	}

	function RadioGroup($Name = null, $Option = null, $DefaultValue = null, $CSSSelector = null){
        $Object = new RadioGroup($Name, $Option, $DefaultValue, $CSSSelector);

		return $Object->HTML();
    }

	function Checkbox($Value = null, $Caption = null, $Name = null, $CSSSelector = null, $EventHandlerJavaScript = null, $ID = null){
		$Object = new Checkbox($Value, $Caption, $Name, $CSSSelector, $EventHandlerJavaScript, $ID);

		return $Object->HTML();
	}

	function CheckboxGroup($Name = null, $Option = null, $PrependOption = null, $CaptionField = null, $AppendOption = null, $ValueField = null, $CSSSelector = null, $EventHandlerJavaScript = null, $Array = null, $ID = null){
        $Object = new CheckboxGroup($Name, $Option, $PrependOption, $CaptionField, $AppendOption, $ValueField, $CSSSelector, $EventHandlerJavaScript, $Array, $ID);

		return $Object->HTML();
    }

	function Field($Content = null, $Caption = null, $NewLine = null, $Separate = null, $CaptionWidth = null, $ContentWidth = null, $CSSSelector = null, $Header = null, $Footer = null, $Prefix = null, $Suffix = null, $ContentName = null, $ContentPath = null, $ContentAnchor = null, $ContentLanguage = null, $ID = null){
		$Object = new Field($Content, $Caption, $NewLine, $Separate, $CaptionWidth, $ContentWidth, $CSSSelector, $Header, $Footer, $Prefix, $Suffix, $ContentName, $ContentPath, $ContentAnchor, $ContentLanguage, $ID);

		return $Object->HTML();
	}

	function Form($Action = null, $Content = null, $SubmitCaption = null, $SignatureModifier = null, $Title = null, $Header = null, $Footer = null, $Status = null, $ID = null, $Reset = null, $ButtonContent = null, $EventHandlerJavaScript = null, $InputValidation = null, $CSSSelector = null){
		$Object = new Form($Action, $Content, $SubmitCaption, $SignatureModifier, $Title, $Header, $Footer, $Status, $ID, $Reset, $ButtonContent, $EventHandlerJavaScript, $InputValidation, $CSSSelector);

		return $Object->HTML();
	}

	function DropdownMenu($Item = null, $CSSSelector = null){
		$Object = new DropdownMenu($Item, $CSSSelector);

		return $Object->HTML();
	}

	function Toast($Content = null, $CSSSelector = null, $LifeTime = null, $Container = null, $HTML = null){
		$Object = new Toast($Content, $CSSSelector, $LifeTime, $Container, $HTML);

		return $Object->HTML();
	}

	function Accordion($Name = null, $Pad = null, $IconBaseURL = null, $CSSSelector = null, $SinglePad = null){
		$Object = new Accordion($Name, $Pad, $IconBaseURL, $CSSSelector, $SinglePad);

		return $Object->HTML();
	}
}

namespace sPHP\Graphic{
	function Resample($PictureFile, $MaximumWidth = null, $MaximumHeight = null, $SavePath = null, $Percent = null, $Width = null, $Height = null, $Type = null){
		$Object = new \sPHP\Graphic();

		return $Object->Resample($PictureFile, $MaximumWidth, $MaximumHeight, $SavePath, $Percent, $Width, $Height, $Type);
	}
}
?>