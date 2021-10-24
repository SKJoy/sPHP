<?php
namespace sPHP;

class Session{
    private $Property = [
        "Environment"			=>	null,
        "Guest"					=>	null,
        "Lifetime"				=>	20 * 60, // In seconds
        "Isolate"				=>	true,
        "User"					=>	null, // Not used, served from session store
        "Name"					=>	null,
		"ContentEditMode"		=>	false,
        "DebugMode"				=>	false,
        "IgnoreActivity"        =>  false, 
        "Language"              =>  null, 
        
        // Read only
		"LastActivityTime"		=>	null,
		"ID"					=>	null,
        "IsFresh"				=>	false,
        "IsReset"				=>	false,
        "IsGuest"				=>	null,
		"UserSetTime"			=>	null,
		"Impersonated"			=>	false,
    ];

    #region Variable
	private static $AlreadyInstantiated = false;
    #endregion Variable

    #region Method
    public function __construct($Environment = null, $Guest = null, $Lifetime = null, $Isolate = null, $User = null, $Name = null, $ContentEditMode = null, $DebugMode = null, $IgnoreActivity = null, $Language = null){
		// Check if already instantiated and throw error if tried again
		if($this::$AlreadyInstantiated)trigger_error("Instantiation is prohibited for " . __CLASS__ .  " object.");
        $this::$AlreadyInstantiated = true;

        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName => $ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){
        if(!$this->Property["IgnoreActivity"])$_SESSION["LastActivityTime"] = time();

        return true;
    }

    public function Start(){
		// Customize session name than the PHP default
		session_name($this->Property["Environment"]->Utility()->ValidVariableName($this->Property["Name"]));
        session_start(); // Start the session

        if(!isset($_SESSION["LastActivityTime"])){ // Session doesn't exist, it is Fresh
            $this->Property["IsFresh"] = true;
            $this->Reset(); // Reset session to set up guest properties
        }
        else{ // Session exists, Old            
            if((time() - $_SESSION["LastActivityTime"]) > $this->Property["Lifetime"]){ // Reset session upon activity time out
				$this->Reset(); // Reset session to set up guest properties
			}
			else{ // Existing valid session
                // Reflect session values to external resources
                $this->Property["Environment"]->Log()->UserID($this->User()->ID()); // Update global Log UserID
				$this->Property["Environment"]->Utility()->Debug()->Enabled($_SESSION["DebugMode"]);
			}
        }

        return true;
    }

	public function Reset(){
		$Result = true;

		$this->Property["IsReset"] = true;
		
        $this->User($this->Property["Guest"]); // Set user through method to take user change related actions
        $this->Language(new Language());

		return $Result;
	}

	public function Impersonate($User){
        $this->Property["Environment"]->Log()->Put("{$this->User()->Name()} ({$this->User()->UserGroupIdentifierHighest()})", ["User" => ["Name" => $User->Name(), "Email" => $User->Email(), "UserGroupIdentifierHighest" => $User->UserGroupIdentifierHighest(), ], ], null, LOG_TYPE_SECURITY, "Impersonate", "User", "Session");

		$this->Reset();
		$this->User($User);

		$_SESSION["Impersonated"] = true;

		return true;
	}
    #endregion Method

    #region Property
    public function Environment($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Guest($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Lifetime($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Isolate($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            session_save_path("{$this->Property["Environment"]->Path()}session");

            $Result = true;
        }

        return $Result;
    }

    public function User($Value = null){
        if(is_null($Value)){
            $Result = isset($_SESSION[__FUNCTION__]) ? $_SESSION[__FUNCTION__] : $this->Property["Guest"]; // Return Guest in case if User is not set
        }
        else{
            $_SESSION[__FUNCTION__] = $Value;

			$this->Property["IsGuest"] = null;

			$_SESSION["ContentEditMode"] = false;
			$_SESSION["DebugMode"] = false;
			$_SESSION["UserSetTime"] = time();
			$_SESSION["Impersonated"] = false;
        
            $this->Property["Environment"]->Log()->UserID($this->User()->ID()); // Update global Log UserID

            // This causes flood with GUEST and all User set messages!
            //$this->Property["Environment"]->Log()->Put("{$this->User()->Name()} ($this->User()->UserGroupIdentifierHighest())", null, null, LOG_TYPE_APPLICATION, "Set", "User", "Session");

            $Result = true;
        }

        return $Result;
    }

    public function Name($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function ContentEditMode($Value = null){
        if(is_null($Value)){
            $Result = $_SESSION[__FUNCTION__];
        }
        else{
            $_SESSION[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function DebugMode($Value = null){
        if(is_null($Value)){
            $Result = $_SESSION[__FUNCTION__];
        }
        else{
            $_SESSION[__FUNCTION__] = $Value;

			$this->Property["Environment"]->Utility()->Debug()->Enabled($_SESSION[__FUNCTION__]);

            $Result = true;
        }

        return $Result;
    }

    public function IgnoreActivity($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Language($Value = null){
        if(is_null($Value)){
            //$Result = $_SESSION[__FUNCTION__];
            // Above is correct, following is just to save the day, enable above once all panels are okay
            $Result = isset($_SESSION[__FUNCTION__]) ? $_SESSION[__FUNCTION__] : new Language();
        }
        else{
            $_SESSION[__FUNCTION__] = $Value;

			$Result = true;
        }

        return $Result;
    }

    public function LastActivityTime(){
        $Result = $_SESSION[__FUNCTION__];

        return $Result;
    }

    public function ID(){
        $Result = session_id();

        return $Result;
    }

    public function IsFresh(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function IsReset(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function IsGuest(){
		if(is_null($this->Property[__FUNCTION__]))$this->Property[__FUNCTION__] = $_SESSION["User"]->Email() == $this->Property["Guest"]->Email() ? true : false;

        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function UserSetTime(){
		$Result = $_SESSION["UserSetTime"];

        return $Result;
    }

    public function Impersonated(){
		$Result = $_SESSION["Impersonated"];

        return $Result;
    }
    #endregion Property
}
?>