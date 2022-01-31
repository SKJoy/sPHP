<?php
namespace sPHP;

class Environment{
    private $Property = [
        #region Writable
        "Utility"				=>	null,
		"SMTPHost"				=>	null,
		"SMTPPort"				=>	null,
		"SMTPUser"				=>	null,
		"SMTPPassword"			=>	null,
		"TimeZone"				=>	null,
		"TimeLimit"				=>	30, // Maximum execution time in seconds
		"CustomError"			=>	false,
		"MemoryLimit"			=>	256, // Set memory limit in MB
		"Log"			        =>	null, 
        #endregion Writable

        #region Read only
        "Path"					=>	"./",
        "ImagePath"				=>	"./image/",
        "ContentPath"			=>	"./content/",
        "ContentUploadPath"		=>	"./content/upload/",
        "StylePath"				=>	"./style/",
        "ScriptPath"			=>	"./script/",
		"UploadPath"			=>	"./upload/",
		"SQLPath"				=>	"./database/sql/",
		"SQLSELECTPath"			=>	"./database/sql/select/",
		"TempPath"				=>	"./temp/",
		"DomainPath"			=>	"./localhost/",
		"SystemPath"			=>	null,
		"SystemScriptPath"		=>	null,
		"LogPath"				=>	"./log/",
		"MailLogPath"			=>	"./log/mail/",
        "Protocol"				=>	"HTTP",
        "URLPath"				=>	"./",
        "URL"					=>	null,
        "HTTPURL"				=>	null,
        "HTTPSURL"				=>	null,
        "ScriptURL"				=>	"./script/",
        "ImageURL"				=>	"./image/",
        "IconURL"				=>	"./image/",
        "UploadURL"				=>	"./upload/",
        "ContentUploadURL"		=>	"./content/upload/",
        "StyleURL"				=>	"./style/",
        "DomainURL"				=>	"./localhost/",
        "Name"					=>	"sPHP",
        "Version"				=>	null,
		"Client"				=>	null,
        "OSProcess"             =>  null, 
        "CLI"					=>  false, 
        "OperatingSystem"		=>  OS_OTHER, 
        #endregion Read only
    ];

    #region Variable
	private static $AlreadyInstantiated = false;
    #endregion Variable

    #region Method
    public function __construct($Utility = null){
		// Check if already instantiated and throw error if tried again
		if($this::$AlreadyInstantiated)trigger_error("Instantiation is prohibited for " . __CLASS__ .  " object.");
		$this::$AlreadyInstantiated = true;
		
        #region Detect Operating system
		if(\PHP_OS_FAMILY === "Windows"){ // Command for Windows
            $this->Property["OperatingSystem"] = OS_WINDOWS;
        }
        elseif(\PHP_OS_FAMILY === "Linux"){ // Command for Linux
            $this->Property["OperatingSystem"] = OS_LINUX;
        }
        else{ // Unknown OS
            $this->Property["OperatingSystem"] = OS_OTHER;
        }
		#endregion Detect Operating system
		
		ini_set("memory_limit", "{$this->Property["MemoryLimit"]}M"); // Set PHP memory limit before everything

        $this->Property["OSProcess"] = $Utility->OSProcess(); // Get own process information
        $this->Property["CLI"] = php_sapi_name() == "cli" ? true : false; // Detect if ran from command line interface
        $this->Property["Name"] = "sPHP";
        $this->Property["Version"] = new Version(10);

		#region Development environment
		$DevelopmentVersionMode = file_exists($DevelopmentVersionFile = __DIR__ . "/../../../../sphp_version.txt"); // Check if development version to use
		$VersionFile = __DIR__ . "/../../../sphp_version.txt";
		if($DevelopmentVersionMode)copy($DevelopmentVersionFile, $VersionFile); // Switch version file to development
		$this->Property["Version"]->File($VersionFile, $DevelopmentVersionMode); // Load version, increse counter only if development version
		if($DevelopmentVersionMode)copy($VersionFile, $DevelopmentVersionFile); // Put version file back as development version file
        #endregion Development environment

		#region Detect client terminal/browser
		/*
		Let us not create an overload to the framework/execution and use client detection
		on demand at the application development layer
		*/
		/*
		$ClientTerminal = new \BrowserDetection();

        $this->Property["Client"]["Terminal"] = [
			"Name"		=>						$ClientTerminal->getName(),
			"Version"	=>						$ClientTerminal->getVersion(),
			"Platform"	=>	[
								"Family"	=>	$ClientTerminal->getPlatform(),
								"Version"	=>	$ClientTerminal->getVersion(true),
								"Name"		=>	$ClientTerminal->getPlatformVersion(),
								"64Bit"		=>	$ClientTerminal->is64bitPlatform(),
							],
			"Mobile"	=>						$ClientTerminal->isMobile(),
			"Robot"		=>						$ClientTerminal->isRobot(),
			"AOL"		=>						$ClientTerminal->isAol(),
		];
		*/
		#endregion Detect client terminal/browser

		#region Fix missing server variables
		if(!isset($_SERVER[$VariableName = "COMPUTERNAME"]))$_SERVER["{$VariableName}"] = null;
		if(!isset($_SERVER[$VariableName = "HTTP_REFERER"]))$_SERVER["{$VariableName}"] = null;
		if(!isset($_SERVER[$VariableName = "HTTP_USER_AGENT"]))$_SERVER["{$VariableName}"] = null;
		if(!isset($_SERVER[$VariableName = "HTTPS"]))$_SERVER["{$VariableName}"] = null;

		#region Command line execution
		if(!isset($_SERVER[$VariableName = "REMOTE_ADDR"]))$_SERVER["{$VariableName}"] = "127.0.0.1";
		if(!isset($_SERVER[$VariableName = "SERVER_ADDR"]))$_SERVER["{$VariableName}"] = "127.0.0.1";
		if(!isset($_SERVER[$VariableName = "SERVER_NAME"]))$_SERVER["{$VariableName}"] = "LocalHost";
		if(!isset($_SERVER[$VariableName = "SERVER_PROTOCOL"]))$_SERVER["{$VariableName}"] = null;
		if(!isset($_SERVER[$VariableName = "REQUEST_METHOD"]))$_SERVER["{$VariableName}"] = null;
		if(!isset($_SERVER[$VariableName = "HTTP_HOST"]))$_SERVER["{$VariableName}"] = "LocalHost";
		if(!isset($_SERVER[$VariableName = "QUERY_STRING"]))$_SERVER["{$VariableName}"] = null;
        #endregion Command line execution
		#endregion Fix missing server variables

        #region Operating system specific server variable fix
        if(isset($_SERVER["LOCAL_ADDR"])){
            $_SERVER["SERVER_ADDR"] = $_SERVER["LOCAL_ADDR"];
        }
        else{
            $_SERVER["LOCAL_ADDR"] = $_SERVER["SERVER_ADDR"];
        }
        #endregion Operating system specific server variable fix

		#region Convert command line argument variables to GET parameters
		if(isset($_SERVER["argv"])){
			$argv = $_SERVER["argv"];
			array_shift($argv); // Assuming the first parameter is the PHP script to execute (PHP_SELF) and discard

			foreach($argv as $Argument){
				$Argument = explode("=", $Argument);
				if(!isset($_GET[$Argument[0]]))$_GET[$Argument[0]] = isset($Argument[1]) ? urldecode($Argument[1]) : "";
			}
		}
		#endregion Convert command line argument variables to GET parameters

		foreach($_GET as $Key => $Value)if(!isset($_POST[$Key]))$_POST[$Key] = $Value; // Create POST variable for each GET variable if not already exists

        #region Set path properties
        $this->Property["Path"] = pathinfo($_SERVER["SCRIPT_FILENAME"])["dirname"] . DIRECTORY_SEPARATOR;
        $this->Property["ImagePath"] = "{$this->Property["Path"]}image/";
        $this->Property["ContentPath"] = "{$this->Property["Path"]}content/";
        $this->Property["ContentUploadPath"] = "{$this->Property["ContentPath"]}upload/";
        $this->Property["StylePath"] = "{$this->Property["Path"]}style/";
        $this->Property["ScriptPath"] = "{$this->Property["Path"]}script/";
        $this->Property["UploadPath"] = "{$this->Property["Path"]}upload/";
        $this->Property["SQLPath"] = "{$this->Property["Path"]}database/sql/";
        $this->Property["SQLSELECTPath"] = "{$this->Property["SQLPath"]}select/";
        $this->Property["TempPath"] = "{$this->Property["Path"]}temp/";
        $this->Property["DomainPath"] = "{$this->Property["Path"]}domain/" . strtolower($_SERVER["SERVER_NAME"]) . DIRECTORY_SEPARATOR;
		$this->Property["SystemPath"] = realpath(__DIR__ . "/../../..") . DIRECTORY_SEPARATOR;
		$this->Property["SystemScriptPath"] = "{$this->Property["SystemPath"]}script/";
        $this->Property["LogPath"] = "{$this->Property["Path"]}log/";
        $this->Property["MailLogPath"] = "{$this->Property["LogPath"]}mail/";
        #endregion Set path properties

        $this->Property["Protocol"] = substr($_SERVER["SERVER_PROTOCOL"], 0, strpos($_SERVER["SERVER_PROTOCOL"], "/"));
        $this->Property["URLPath"] = substr($_SERVER["PHP_SELF"], 1, strlen($_SERVER["PHP_SELF"]) - strlen(basename($_SERVER["PHP_SELF"])) - 1);

        #region Set up URLs
        $this->Property["URL"] = "http" . (strtoupper($_SERVER["HTTPS"]) == "ON" ? "s" : null) . "://{$_SERVER["SERVER_NAME"]}/{$this->Property["URLPath"]}";
        $this->Property["HTTPURL"] = "http://{$_SERVER["SERVER_NAME"]}/{$this->Property["URLPath"]}";
        $this->Property["HTTPSURL"] = "https://{$_SERVER["SERVER_NAME"]}/{$this->Property["URLPath"]}";
        $this->Property["ScriptURL"] = "{$this->Property["URL"]}script/";
        $this->Property["ImageURL"] = "{$this->Property["URL"]}image/";
        $this->Property["IconURL"] = "{$this->Property["ImageURL"]}icon/";
        $this->Property["UploadURL"] = "{$this->Property["URL"]}upload/";
        $this->Property["ContentUploadURL"] = "{$this->Property["URL"]}content/upload/";
        $this->Property["StyleURL"] = "{$this->Property["URL"]}style/";
        $this->Property["DomainURL"] = "{$this->Property["URL"]}domain/" . strtolower($_SERVER["SERVER_NAME"]) . "/";
        #endregion Set up URLs

		#region Error configuration
		//ini_set("log_errors_max_length", 2 * 1024 * 1024);

		$ErrorLogFile = "{$this->Property["Path"]}error.php.log"; // Custom error log file at application root
		ini_set("error_log", $ErrorLogFile); // Set custom error log file

		// Don't let the error log file grow bigger than N MB
		if(file_exists($ErrorLogFile) && filesize($ErrorLogFile) > 3 * 1024 * 1024)@unlink($ErrorLogFile);

		error_reporting(E_ALL); // Show all errors
		#endregion Error configuration

        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

		$this->Property["Utility"]->Debug()->BasePath($this->Property["Path"]);

		#region Set up global Log
        $this->Property["Log"] = new Log();
        $this->Property["Log"]->File("{$this->Property["Path"]}log/application.log");
        $this->Property["Log"]->BasePath([$this->Property["SystemPath"], $this->Property["Path"], ]);
		#endregion Set up global Log

        return true;
    }

    public function __destruct(){
		return true;
    }

    public function SetVariable($Name, $DefaultValue = null, $GET = true, $POST = true, $SESSION = false, $COOKIE = false){
		if($GET){
			if(isset($_GET[$Name])){
				$DefaultValue = $_GET[$Name];
			}
			else{
				$_GET[$Name] = $DefaultValue;
			}
		}

		if($POST){
			if(isset($_POST[$Name])){
				$DefaultValue = $_POST[$Name];
			}
			else{
				$_POST[$Name] = $DefaultValue;
			}
		}

		if($SESSION){
			if(isset($_SESSION[$Name])){
				$DefaultValue = $_SESSION[$Name];
			}
			else{
				$_SESSION[$Name] = $DefaultValue;
			}
		}

		if($COOKIE){
			if(isset($_COOKIE[$Name])){
				$DefaultValue = $_COOKIE[$Name];
			}
			else{
				$_COOKIE[$Name] = $DefaultValue;
			}
		}

        return $DefaultValue;
    }

    public function ShellCommand(
		string $Command, // Command to execute
		bool $NoWait = false // Should wait for the command execution to finish; Does not work for Windows
	){
		if($this->Property["OperatingSystem"] == \sPHP\OS_WINDOWS){
			$ExecStatus = exec($Command, $ExecInformation, $ExecReturn);
		}
		elseif($this->Property["OperatingSystem"] == \sPHP\OS_LINUX){
			$ExecStatus = exec($Command . ($NoWait ? " > /dev/null &" : null), $ExecInformation, $ExecReturn);
		}
		else{
			$ExecStatus = false;
		}

		return $ExecStatus === false ? false : [
			"Information" => is_array($ExecInformation) ? $ExecInformation : [], 
			"ReturnCode" => $ExecReturn, 
		];
    }
    #endregion Method

    #region Property
    public function Utility($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			#region GeoLocation property
			/*
			// DISABLED FOR PERFORMANCE ENHANCEMENT
			if(($Data = $this->Property[__FUNCTION__]->IP2Geo($_SERVER["REMOTE_ADDR"])) !== false)$this->Property["Client"]["Location"] = [
				"Geo"					=>	[
													"Accuracy"		=>	$Data->location->accuracyRadius,
													"Latitude"		=>	$Data->location->latitude,
													"Longitude"		=>	$Data->location->longitude,
											],
				"Address"				=>	[
					"Metro"					=>	[
													"Code"			=>	$Data->location->metroCode,
												],
					"City"					=>	[
													"Confidence"	=>	$Data->city->confidence,
													"Name"			=>	$Data->city->names["en"],
													"ID"			=>	$Data->city->geonameId,
												],
					"Postal"				=>	[
													"Confidence"	=>	$Data->postal->confidence,
													"Code"			=>	$Data->postal->code,
												],
					"Country"				=>	[
													"Confidence"	=>	$Data->country->confidence,
													"ISOCode"		=>	$Data->country->isoCode,
													"ID"			=>	$Data->country->geonameId,
													"Name"			=>	$Data->country->names["en"],
												],
					"RegisteredCountry"		=>	[
													"Confidence"	=>	$Data->registeredCountry->confidence,
													"ISOCode"		=>	$Data->registeredCountry->isoCode,
													"ID"			=>	$Data->registeredCountry->geonameId,
													"Name"			=>	$Data->registeredCountry->names["en"],
												],
					"RepresentingCountry"	=>	[
													"Confidence"	=>	$Data->representedCountry->confidence,
													"ISOCode"		=>	$Data->representedCountry->isoCode,
													"ID"			=>	$Data->representedCountry->geonameId,
													"Name"			=>	$Data->representedCountry->names["en"],
												],
					"Continent"				=>	[
													"Code"			=>	$Data->continent->code,
													"ID"			=>	$Data->continent->geonameId,
													"Name"			=>	$Data->continent->names["en"],
												],
											],
				"Timezone"				=>	[
													"Name"			=>	$Data->location->timeZone,
											],
				"Locale"				=>	[
													"Code"			=>	$Data->locales[0],
											],
			];
			*/
			#endregion GeoLocation property

            $Result = true;
        }

        return $Result;
    }

    public function SMTPHost($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function SMTPPort($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function SMTPUser($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function SMTPPassword($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function TimeZone($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			ini_set("date.timezone", $this->Property[__FUNCTION__]);

            $Result = true;
        }

        return $Result;
    }

    public function TimeLimit($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			set_time_limit(intval($this->Property[__FUNCTION__])); // Allow the script to run for the specified time in seconds

            $Result = true;
        }

        return $Result;
    }

    public function CustomError($Value){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			if($this->Property[__FUNCTION__]){
				set_error_handler(function($Number, $Message, $File, $Line){
					___ErrorPage($Message, $File, $Line, $Number);
				}, E_ALL); // E_ALL

				set_exception_handler(function(\Throwable $Exception){
					___ErrorPage($Exception->getMessage(), $Exception->getFile(), $Exception->getLine());
				});
			}
			else{
				restore_error_handler();
			}

            $Result = true;
        }

        return $Result;
    }

    public function MemoryLimit($Value){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			ini_set("memory_limit", "{$this->Property[__FUNCTION__]}M"); // Set memory limit

            $Result = true;
        }

        return $Result;
    }

    public function Log($Value = null){
        if(is_null($Value))return $this->Property[__FUNCTION__];
        $this->Property[__FUNCTION__] = $Value;
    }

    public function Path(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function ImagePath(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function ContentPath(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function ContentUploadPath(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function StylePath(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function ScriptPath(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function UploadPath(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function SQLPath(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function SQLSELECTPath(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function TempPath(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function DomainPath(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function SystemPath(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function SystemScriptPath(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function LogPath(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function MailLogPath(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function Protocol(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function URLPath(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function URL(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function HTTPURL(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function HTTPSURL(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function ScriptURL(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function ImageURL(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function IconURL(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function UploadURL(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function ContentUploadURL(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function StyleURL(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function DomainURL(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function Name(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function Version(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function Client(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function OSProcess(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function CLI(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function OperatingSystem(){
        return $this->Property[__FUNCTION__];
    }

    function OSProcesses(?string $SortOn = null, ?string $CommandFilter = null, ?string $CommandFilterLeft = null){
        if(PHP_OS_FAMILY === "Windows"){ // Command for Windows
            $ExecStatus = false;
        }
        elseif(PHP_OS_FAMILY === "Linux"){ // Command for Linux
            try{
                $ExecStatus = @exec("ps -aux", $ExecInformation, $ExecReturn);
            }
            catch(\Exception $Error){
                $ExecStatus = false;
            }
        }
        else{ // Unknown OS
            $ExecStatus = false;
        }
        
        if($ExecStatus !== false && is_array($ExecInformation) && count($ExecInformation) > 1){
            //DebugDump($ExecInformation[0]); // Header
            //DebugDump($ExecInformation[1]); // First data
            $Process = [];
            $SelfProcessID = getmypid();
    
            if(PHP_OS_FAMILY === "Windows"){
                $Result = false;
            }
            elseif(PHP_OS_FAMILY === "Linux"){
                //file_put_contents(__DIR__ . "/Debug-Environment-OSProcesses.txt", implode(PHP_EOL, $ExecInformation));
                array_shift($ExecInformation); // Remove header row
                $Field = array_filter(explode(",", str_replace(" ", "", "User, ID, CPU, Memory, VSZ, RSS, TTY, Stat, Start, Time, Command")));
                $LastFieldIndex = count($Field) - 1;
            
                foreach($ExecInformation as $ProcessData){
                    $ProcessData = array_values(array_filter(explode(" ", $ProcessData)));
                    $ProcessDataPartCount = count($ProcessData);

                    if($ProcessDataPartCount >= ($LastFieldIndex + 1)){ // Process parts contain at least all fields
                        $ProcessCommand = [];
                        for($ElementCounter = $LastFieldIndex; $ElementCounter < $ProcessDataPartCount; $ElementCounter++)$ProcessCommand[] = $ProcessData[$ElementCounter];
                        $ProcessData[$LastFieldIndex] = implode(" ", $ProcessCommand);
                        //DebugDump($ProcessData);
                
                        if($ProcessData[1] != $SelfProcessID){
                            if(
                                    (!$CommandFilter || strpos($ProcessData[$LastFieldIndex], $CommandFilter) !== false)
                                &&	(!$CommandFilterLeft || substr($ProcessData[$LastFieldIndex], 0, strlen($CommandFilterLeft)) == $CommandFilterLeft)
                            ){
                                for($FieldCounter = 0; $FieldCounter <= $LastFieldIndex; $FieldCounter++)$ThisProcess[$Field[$FieldCounter]] = $ProcessData[$FieldCounter];
                                $Process[] = $ThisProcess;
                            }
                        }
                    }
                }
    
                $Result = true;
            }
            else{
                $Result = false;
            } //DebugDump($Process);
            
            if($Result && $SortOn){ // Sort on argument field
                $Sorted = [];
                foreach($Process as $ThisProcess)$Sorted[$ThisProcess[$SortOn]] = $ThisProcess;
                ksort($Sorted);
                $Result = array_values($Sorted);
            }
            else{
                $Result = $Process;
            }
        }
        else{
            $Result = false;
        }
    
        return $Result;
    }
    #endregion Property

    #region Function
    #endregion Function
}
?>