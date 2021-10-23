<?php
namespace sPHP\API\V2;

class User extends \sPHP\API\V2{
	#region Method
	#region System
	public function __construct(?string $OutputFormat = null, ?string $Comment = null){
		// Set initial property values upon object instantiation
		parent::{__FUNCTION__}($OutputFormat); 

		#region Property dependancy
		//* Add properties that will reset other properties when set/changed
		//parent::AddPropertyDependancy("FirstName", ["Name", "Caption", ]); //? Name & Caption will be reset when FirstName changes
		//parent::AddPropertyDependancy("LastName", ["Name", "Caption", ]); //? Keep adding dependant properties as array elements
		#endregion Property dependancy

		#region Set default property value
		$this->Entity = "User";
		$this->ID = intval(\sPHP::$User->ID());
		#endregion Set default property value
	}
	
	public function __destruct(){
		parent::{__FUNCTION__}();
	}
	#endregion System

	public function SignIn(string $Email, string $Password){
		$Response = $this->Response;
		$Response["Documentation"]["Description"] = "Authorize an user and set session for";
		//$Response["Documentation"]["Note"] = "Available for authenticated (not GUEST) user only";

		$Response["Documentation"]["Argument"] = [
			"Email" => ["Type" => "String", "Required" => true, "Default" => null, "Description" => "Email address or Sign in name to check for", "Sample" => "John.Doe@NoWhere.How", ], 
			"Password" => ["Type" => "String", "Required" => true, "Default" => null, "Description" => "Password to authenticate the user with", "Sample" => "MySecure#007passWORD", ], 
		];

		if(\sPHP::$Session->IsGuest()){
			if($Email){
				if($Password){
					$Recordset = \sPHP::$Table[$this->Entity]->Get("
							'" . \sPHP::$Database->Escape($Email) . "' IN (U.{$this->Entity}Email, U.{$this->Entity}SignInName)
						AND	U.{$this->Entity}PasswordHash = MD5(CONCAT('" . \sPHP::$Database->Escape($Password) . "', IF(U.{$this->Entity}PasswordHashSalt IS NULL, '', U.{$this->Entity}PasswordHashSalt)))
						AND	U.{$this->Entity}IsActive = 1
						AND	" . (\sPHP::$Configuration["AdministratorAccessOnly"] ? "U.{$this->Entity}ID IN ( # Administrator access only
								SELECT			UUG.{$this->Entity}ID 
								FROM			sphp_userusergroup AS UUG 
									LEFT JOIN	sphp_usergroup AS UG ON UG.{$this->Entity}GroupID = UUG.{$this->Entity}GroupID
								WHERE			UG.{$this->Entity}GroupIdentifier = 'ADMINISTRATOR' 
									AND			UUG.{$this->Entity}ID = U.{$this->Entity}ID
							)" : "TRUE # Allow any User"
						) . "
					");
	
					if(count($Recordset)){
						$Record = $Recordset[0];
						$UserGroupIdentifierHighest = explode("; ", $Record["{$this->Entity}GroupIdentifier"])[0];
	
						\sPHP::$Session->User(new \sPHP\User( //* It needs to be as sPHP\User, not API User
							$Record["{$this->Entity}Email"],
							$Record["{$this->Entity}PasswordHash"],
							$Record["{$this->Entity}Name"],
							$Record["{$this->Entity}Phone"],
							null,
							$Record["{$this->Entity}URL"],
							$Record["{$this->Entity}Picture"],
							$Record["{$this->Entity}PictureThumbnail"],
							$Record["{$this->Entity}ID"],
							$Record["{$this->Entity}GroupIdentifier"],
							$Record["{$this->Entity}GroupWeight"],
							max(explode("; ", $Record["{$this->Entity}GroupWeight"])),
							$Record["{$this->Entity}GroupName"],
							$UserGroupIdentifierHighest
						));

						$this->ID = intval(\sPHP::$User->ID());
		
						$Response["Response"] = [
							"ID"						=> $Record["{$this->Entity}ID"], 
							"Email"						=> $Record["{$this->Entity}Email"], 
							"SignInName"				=> $Record["{$this->Entity}SignInName"], 
							"NameFirst"					=> $Record["{$this->Entity}NameFirst"], 
							"NameMiddle"				=> $Record["{$this->Entity}NameMiddle"], 
							"NameLast"					=> $Record["{$this->Entity}NameLast"], 
							"Picture"					=> $Record["{$this->Entity}Picture"], 
							"PictureThumbnail"			=> $Record["{$this->Entity}PictureThumbnail"], 
							"GroupName"					=> $Record["{$this->Entity}GroupName"], 
							"GroupIdentifierHighest"	=> $UserGroupIdentifierHighest, 
						];
					}
					else{
						$Response["Error"][] = $this::ERROR_SUBJECT_NOT_FOUND;
					}
				}
				else{
					$Response["Error"][] = $this::ERROR_REQUIRED_PASSWORD;
				}
			}
			else{
				$Response["Error"][] = $this::ERROR_REQUIRED_EMAIL;
			}
		}
		else{
			$Response["Error"][] = $this::ERROR_UNAUTHORIZED;
		}

		$Response["Diagnostics"]["Time"]["End"] = date("r");
		$Response["Diagnostics"]["Time"]["DurationSecond"] = strtotime($Response["Diagnostics"]["Time"]["Begin"]) - strtotime($Response["Diagnostics"]["Time"]["End"]);
		
		return $this->Output($Response);
	}

	public function SignOut(){
		$Response = $this->Response;
		$Response["Documentation"]["Description"] = "Reset the session with GUEST user";
		//$Response["Documentation"]["Note"] = "Available for authenticated (not GUEST) user only";
		$Response["Documentation"]["Argument"] = [];

		\sPHP::$Session->Reset();
		$this->ID = intval(\sPHP::$User->ID());

		$Response["Response"] = "Session reset";
		$Response["Diagnostics"]["Time"]["End"] = date("r");
		$Response["Diagnostics"]["Time"]["DurationSecond"] = strtotime($Response["Diagnostics"]["Time"]["Begin"]) - strtotime($Response["Diagnostics"]["Time"]["End"]);
		
		return $this->Output($Response);
	}

	public function Profile(?array $Data = null){
		#region Validate arguments
		if(is_array($Data)){
			$Data = array_filter($Data);
			if(!count($Data))$Data = null;
		}
		#endregion Validate arguments

		$Response = $this->Response;
		$Response["Documentation"]["Description"] = "Get or update user profile Data";
		$Response["Documentation"]["Method"] = "Argument/POST";
		$Response["Documentation"]["Note"] = "Available for authenticated (not GUEST) user only";

		$Response["Documentation"]["Argument"] = [
			"Data" => ["Type" => "Array/JSON", "Required" => false, "Default" => null, "Description" => "Profile data. NULL = Get; Array/JSON = Update", "Sample" => "Array: [\"NameFirst\" => \"John\", \"NameLast\" => \"Doe\", ]; JSON: {\"NameFirst\":\"John\",\"NameLast\":\"Doe\"}", ], 
		];

		if(\sPHP::$Session->IsGuest()){ // Deny for GUEST user
			$Response["Error"][] = $this::ERROR_UNAUTHORIZED;
		}
		else{ // Process for authenticated user
			if(is_null($Data)){ // Get
				$Record = \sPHP::$Table[$this->Entity]->Get("U.{$this->Entity}ID = {$this->ID}");
	
				if(count($Record)){
					$Record = $Record[0];
					$UserGroupIdentifierHighest = explode("; ", $Record["{$this->Entity}GroupIdentifier"])[0];
	
					$Response["Response"] = [
						"ID"						=> $Record["{$this->Entity}ID"], 
						"Email"						=> $Record["{$this->Entity}Email"], 
						"SignInName"				=> $Record["{$this->Entity}SignInName"], 
						"NameFirst"					=> $Record["{$this->Entity}NameFirst"], 
						"NameMiddle"				=> $Record["{$this->Entity}NameMiddle"], 
						"NameLast"					=> $Record["{$this->Entity}NameLast"], 
						"Picture"					=> $Record["{$this->Entity}Picture"], 
						"PictureThumbnail"			=> $Record["{$this->Entity}PictureThumbnail"], 
						"GroupName"					=> $Record["{$this->Entity}GroupName"], 
						"GroupIdentifierHighest"	=> $UserGroupIdentifierHighest, 
					];
				}
				else{
					$Response["Error"][] = $this::ERROR_SUBJECT_NOT_FOUND;
				}
			}
			else{ // Update
				if(!is_array($Data))$Data = json_decode($Data, true); // Convert to array from JSON
				$Data = array_filter($Data); // Remove NULL valued fields
	
				if(count($Data)){ // There are some data to update with
					$Column = [];
					if(isset($Data[$Input = "Email"]))$Column["{$this->Entity}{$Input}"] = $Data[$Input];
					if(isset($Data[$Input = "NameFirst"]))$Column["{$this->Entity}{$Input}"] = $Data[$Input];
					if(isset($Data[$Input = "NameMiddle"]))$Column["{$this->Entity}{$Input}"] = $Data[$Input];
					if(isset($Data[$Input = "NameLast"]))$Column["{$this->Entity}{$Input}"] = $Data[$Input];
	
					if(count($Column)){ // Found some column to update with data
						\sPHP::$Table[$this->Entity]->Put($Column, "{$this->Entity}ID = {$this->ID}");
						$Record = \sPHP::$Table[$this->Entity]->Get("U.{$this->Entity}ID = {$this->ID}");
			
						if(count($Record)){
							$Record = $Record[0];
							$UserGroupIdentifierHighest = explode("; ", $Record["{$this->Entity}GroupIdentifier"])[0];
			
							$Response["Response"] = [
								"ID"						=> $Record["{$this->Entity}ID"], 
								"Email"						=> $Record["{$this->Entity}Email"], 
								"SignInName"				=> $Record["{$this->Entity}SignInName"], 
								"Picture"					=> $Record["{$this->Entity}Picture"], 
								"PictureThumbnail"			=> $Record["{$this->Entity}PictureThumbnail"], 
								"GroupName"					=> $Record["{$this->Entity}GroupName"], 
								"GroupIdentifierHighest"	=> $UserGroupIdentifierHighest, 
							];
						}
						else{
							$Response["Error"][] = $this::ERROR_SUBJECT_NOT_FOUND;
						}
					}
					else{
						$Response["Error"][] = $this::ERROR_INSUFFICIENT_DATA;
					}	
				}
				else{
					$Response["Error"][] = $this::ERROR_INSUFFICIENT_DATA;
				}	
			}			
		}

		$Response["Diagnostics"]["Time"]["End"] = date("r");
		$Response["Diagnostics"]["Time"]["DurationSecond"] = strtotime($Response["Diagnostics"]["Time"]["Begin"]) - strtotime($Response["Diagnostics"]["Time"]["End"]);
		
		return $this->Output($Response);
	}

	public function SetPassword(string $Password){
		$Response = $this->Response;		
		$Response["Documentation"]["Description"] = "Set/change password for the currently signed in user";
		$Response["Documentation"]["Note"] = "Available for authenticated (not GUEST) user only";

		$Response["Documentation"]["Argument"] = [
			"Password" => ["Type" => "String", "Required" => true, "Default" => null, "Description" => "Password to set", "Sample" => "MySecure#007passWORD", ], 
		];

		if(\sPHP::$Session->IsGuest()){
			$Response["Error"][] = $this::ERROR_UNAUTHORIZED;
		}
		else{
			if($Password){
				\sPHP::$Database->Query("
					SET @{$this->Entity}PasswordHashSalt := LEFT(MD5(RAND()), 8);
		
					UPDATE		sphp_user AS U
					SET			U.{$this->Entity}PasswordHashSalt = @{$this->Entity}PasswordHashSalt, 
								U.{$this->Entity}PasswordHash = MD5(CONCAT('{$Password}', @{$this->Entity}PasswordHashSalt))
					WHERE		U.{$this->Entity}ID = {$this->ID};
				");
		
				$Response["Response"] = "Password hash generated with new salt";
			}
			else{
				$Response["Error"][] = $this::ERROR_REQUIRED_PASSWORD;
			}
		}

		$Response["Diagnostics"]["Time"]["End"] = date("r");
		$Response["Diagnostics"]["Time"]["DurationSecond"] = strtotime($Response["Diagnostics"]["Time"]["Begin"]) - strtotime($Response["Diagnostics"]["Time"]["End"]);
		
		return $this->Output($Response);
	}

	public function Impersonate(string $Email){
		$Response = $this->Response;		
		$Response["Documentation"]["Description"] = "Switch session to another user";
		$Response["Documentation"]["Note"][] = "Available for authenticated (not GUEST) user only";
		$Response["Documentation"]["Note"][] = "Current user can impersonate another user with lower group weight only";

		$Response["Documentation"]["Argument"] = [
			"Email" => ["Type" => "String", "Required" => true, "Default" => null, "Description" => "Ideintifies the user to impersonate matching either the email or sign in name", "Sample" => "Jane.Doe@Where.How", ], 
		];

		if(\sPHP::$Session->IsGuest()){
			$Response["Error"][] = $this::ERROR_UNAUTHORIZED;
		}
		else{
			if($Email){
				$Recordset = \sPHP::$Table[$this->Entity]->Get("(U.{$this->Entity}Email = '" . \sPHP::$Database->Escape($Email) . "' OR U.{$this->Entity}SignInName = '" . \sPHP::$Database->Escape($Email) . "') AND U.{$this->Entity}IsActive = 1");

				if(count($Recordset)){
					$Record = $Recordset[0];
					$MaximumUserGroupWeight = max(explode("; ", $Record["{$this->Entity}GroupWeight"]));

					if(\sPHP::$User->UserGroupMaximumWeight() > $MaximumUserGroupWeight){
						$UserGroupIdentifierHighest = explode("; ", $Record["{$this->Entity}GroupIdentifier"])[0];
	
						\sPHP::$Session->Impersonate(new User(
							$Record["{$this->Entity}Email"],
							$Record["{$this->Entity}PasswordHash"],
							$Record["{$this->Entity}Name"],
							$Record["{$this->Entity}Phone"],
							null,
							$Record["{$this->Entity}URL"],
							$Record["{$this->Entity}Picture"],
							$Record["{$this->Entity}PictureThumbnail"],
							$Record["{$this->Entity}ID"],
							$Record["{$this->Entity}GroupIdentifier"],
							$Record["{$this->Entity}GroupWeight"],
							$MaximumUserGroupWeight,
							$Record["{$this->Entity}GroupName"],
							$UserGroupIdentifierHighest
						));

						$this->ID = intval(\sPHP::$User->ID());
		
						$Response["Response"] = [
							"ID"						=> $Record["{$this->Entity}ID"], 
							"Email"						=> $Record["{$this->Entity}Email"], 
							"SignInName"				=> $Record["{$this->Entity}SignInName"], 
							"NameFirst"					=> $Record["{$this->Entity}NameFirst"], 
							"NameMiddle"				=> $Record["{$this->Entity}NameMiddle"], 
							"NameLast"					=> $Record["{$this->Entity}NameLast"], 
							"Picture"					=> $Record["{$this->Entity}Picture"], 
							"PictureThumbnail"			=> $Record["{$this->Entity}PictureThumbnail"], 
							"GroupName"					=> $Record["{$this->Entity}GroupName"], 
							"GroupIdentifierHighest"	=> $UserGroupIdentifierHighest, 
						];
					}
					else{
						$Response["Error"][] = $this::ERROR_ACCESS_DENIED;
					}
				}
				else{
					$Response["Error"][] = $this::ERROR_SUBJECT_NOT_FOUND;
				}
			}
			else{
				$Response["Error"][] = $this::ERROR_REQUIRED_EMAIL;
			}
		}

		$Response["Diagnostics"]["Time"]["End"] = date("r");
		$Response["Diagnostics"]["Time"]["DurationSecond"] = strtotime($Response["Diagnostics"]["Time"]["Begin"]) - strtotime($Response["Diagnostics"]["Time"]["End"]);
		
		return $this->Output($Response);
	}
	#endregion Method

	#region Property
	#region Read only
	public function xName(){ parent::DebugLogCall();
		if(is_null($this->Property[__FUNCTION__])){ // Generate property value if not set
			$this->Property[__FUNCTION__] = "{$this->Property["FirstName"]} {$this->Property["LastName"]}";
		}

		return $this->Property[__FUNCTION__];
	}

	public function xCaption(){ parent::DebugLogCall();
		if(is_null($this->Property[__FUNCTION__])){ // Generate property value if not set
			$this->Property[__FUNCTION__] = "{$this->Property["Name"]} [{$this->Property["Email"]}]";
		}

		return $this->Property[__FUNCTION__];
	}
	#endregion Read only

	public function xComment($Value = null){ parent::DebugLogCall($Value);
		if(is_null($Value)){ // Get
			$Result = $this->Property[__FUNCTION__];
		}
		else{ // Set
			$this->Property[__FUNCTION__] = $Value;
			$Result = true;
		}

		return $Result;
	}
	#endregion Property
}
?>