<?php
/*
    Name:           Class
    Purpose:        Framework specific core object definition class library
    Author:         Broken Arrow (SKJoy2001@GMail.Com)
    Created:  		May, 30, 2018 05:30 PM
    Modified:  		Mon, 30 Jul 2018 17:40:00 GMT+06:00
*/

namespace sPHP;

class Environment{
    #region Property
    private $Property = [
        "Utility"				=>	null,
		"SMTPHost"				=>	null,
		"SMTPPort"				=>	null,
		"SMTPUser"				=>	null,
		"SMTPPassword"			=>	null,
		"TimeZone"				=>	null,
		"TimeLimit"				=>	30, // Maximum execution time in seconds
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
        "Name"					=>	"sPHP",
        "Version"				=>	null,
		"Client"				=>	null,
		"CustomError"			=>	false,
    ];
    #endregion Property

    #region Variable
	private static $AlreadyInstantiated = false;
    #endregion Variable

    #region Method
    public function __construct($Utility = null){
		// Check if already instantiated and throw error if tried again
		if($this::$AlreadyInstantiated)trigger_error("Instantiation is prohibited for " . __CLASS__ .  " object.");
		$this::$AlreadyInstantiated = true;

        $this->Property["Name"] = "sPHP";
        $this->Property["Version"] = new Version(10);

		// sPHP development environment
		$DevelopmentVersionMode = file_exists($DevelopmentVersionFile = __DIR__ . "/../sphp_version.txt"); // Check if development version to use
		$VersionFile = __DIR__ . "/sphp_version.txt";
		if($DevelopmentVersionMode)copy($DevelopmentVersionFile, $VersionFile); // Switch version file to development
		$this->Property["Version"]->File($VersionFile, $DevelopmentVersionMode); // Load version, increse counter only if development version
		if($DevelopmentVersionMode)copy($VersionFile, $DevelopmentVersionFile); // Put version file back as development version file

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

		// When run by command line
		if(!isset($_SERVER[$VariableName = "REMOTE_ADDR"]))$_SERVER["{$VariableName}"] = "127.0.0.1";
		if(!isset($_SERVER[$VariableName = "SERVER_ADDR"]))$_SERVER["{$VariableName}"] = "127.0.0.1";
		if(!isset($_SERVER[$VariableName = "SERVER_NAME"]))$_SERVER["{$VariableName}"] = "LocalHost";
		if(!isset($_SERVER[$VariableName = "SERVER_PROTOCOL"]))$_SERVER["{$VariableName}"] = null;
		if(!isset($_SERVER[$VariableName = "REQUEST_METHOD"]))$_SERVER["{$VariableName}"] = null;
		if(!isset($_SERVER[$VariableName = "HTTP_HOST"]))$_SERVER["{$VariableName}"] = "LocalHost";
		if(!isset($_SERVER[$VariableName = "QUERY_STRING"]))$_SERVER["{$VariableName}"] = null;
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

        // Create POST variable for each GET variable id not already exists
		foreach($_GET as $Key=>$Value)if(!isset($_POST[$Key]))$_POST[$Key] = $Value;

        $this->Property["Path"] = pathinfo($_SERVER["SCRIPT_FILENAME"])["dirname"] . "/";
        $this->Property["ImagePath"] = "{$this->Property["Path"]}image/";
        $this->Property["ContentPath"] = "{$this->Property["Path"]}content/";
        $this->Property["ContentUploadPath"] = "{$this->Property["ContentPath"]}upload/";
        $this->Property["StylePath"] = "{$this->Property["Path"]}style/";
        $this->Property["ScriptPath"] = "{$this->Property["Path"]}script/";
        $this->Property["UploadPath"] = "{$this->Property["Path"]}upload/";
        $this->Property["SQLPath"] = "{$this->Property["Path"]}database/sql/";
        $this->Property["SQLSELECTPath"] = "{$this->Property["SQLPath"]}select/";
        $this->Property["TempPath"] = "{$this->Property["Path"]}temp/";
		$this->Property["SystemPath"] = realpath(__DIR__) . "/";
		$this->Property["SystemScriptPath"] = "{$this->Property["SystemPath"]}script/";
        $this->Property["LogPath"] = "{$this->Property["Path"]}log/";
        $this->Property["MailLogPath"] = "{$this->Property["LogPath"]}mail/";

        $this->Property["Protocol"] = substr($_SERVER["SERVER_PROTOCOL"], 0, strpos($_SERVER["SERVER_PROTOCOL"], "/"));

        $this->Property["URLPath"] = substr($_SERVER["PHP_SELF"], 1, strlen($_SERVER["PHP_SELF"]) - strlen(basename($_SERVER["PHP_SELF"])) - 1);

        //$this->Property["URL"] = "" . strtolower($this->Property["Protocol"]) . "://{$_SERVER["SERVER_NAME"]}/{$this->Property["URLPath"]}";
        $this->Property["URL"] = "http" . (strtoupper($_SERVER["HTTPS"]) == "ON" ? "s" : null) . "://{$_SERVER["SERVER_NAME"]}/{$this->Property["URLPath"]}";

        $this->Property["HTTPURL"] = "http://{$_SERVER["SERVER_NAME"]}/{$this->Property["URLPath"]}";
        $this->Property["HTTPSURL"] = "https://{$_SERVER["SERVER_NAME"]}/{$this->Property["URLPath"]}";

        $this->Property["ScriptURL"] = "{$this->Property["URL"]}script/";
        $this->Property["ImageURL"] = "{$this->Property["URL"]}image/";
        $this->Property["IconURL"] = "{$this->Property["ImageURL"]}icon/";
        $this->Property["UploadURL"] = "{$this->Property["URL"]}upload/";
        $this->Property["ContentUploadURL"] = "{$this->Property["URL"]}content/upload/";
        $this->Property["StyleURL"] = "{$this->Property["URL"]}style/";

		#region Error configuration
		//ini_set("log_errors_max_length", 2 * 1024 * 1024);

		$ErrorLogFile = "{$this->Property["Path"]}error.php.log"; // Custom error log file at application root
		ini_set("error_log", $ErrorLogFile); // Set custom error log file
		// Don't let the error log file grow bigger than 3 MB
		if(file_exists($ErrorLogFile) && filesize($ErrorLogFile) > 3 * 1024 * 1024)@unlink($ErrorLogFile);

		error_reporting(E_ALL); // Show all errors
		#endregion Error configuration

        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

		$this->Property["Utility"]->Debug()->BasePath($this->Property["Path"]);

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
    #endregion Property

    #region Function
    #endregion Function
}

class Terminal{
    #region Property
    private $Property = [
        "Environment"		=>	null,
        "Title"				=>	"My application",
        "DocumentType"		=>	DOCUMENT_TYPE_HTML,
        "Language"			=>	null,
        "CharacterSet"		=>	CHARACTER_SET_UTF8,
        "HTMLHeadCode"		=>	null,
        "Mode"				=>	OUTPUT_BUFFER_MODE_MAIN,
        "DocumentName"		=>	null,
        "Suspended"         =>  false,
        "Icon"              =>  "favicon",
        "BackgroundColor"   =>  "Black",
        "ThemeColor"        =>  "Grey",
        "Manifest"          =>  "manifest.json",
    ];
    #endregion Property

    #region Variable
	private static $AlreadyInstantiated = false;
    private $HTML_Head_META = [];
    private $HTML_Head_Link = [];
    private $HTML_Head_JavaScript = [];
    private $Buffer = [];
    #endregion Variable

    #region Method
    public function __construct($Environment = null, $Mode = null){
		// Check if already instantiated and throw error if tried again
		if($this::$AlreadyInstantiated)trigger_error("Instantiation is prohibited for " . __CLASS__ .  " object.");
		$this::$AlreadyInstantiated = true;

		#region Rename built in functions not to let use by the developer
		//rename_function("ob_start", "___ob_start");
		#endregion Rename built in functions not to let use by the developer

        ob_start(array($this, "Send")); // Redirect all output to this function than the standard output terminal

        $this->Property["Language"] = new Language();

        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        $this->Property["Manifest"] = "{$Environment->URL()}{$this->Property["Manifest"]}";

        return true;
    }

    public function __destruct(){
        ob_end_clean(); // Empty and close output buffer to compress the output

        // Compress output only if not in debug mode
        if(!$this->Property["Environment"]->Utility()->Debug()->Enabled()){
            ini_set("zlib.output_handler", ""); // Must be set to empty to allow the compression work
            ini_set("zlib.output_compression", 131072); // Default is 4096 (4 KB)
            ini_set("zlib.output_compression_level", 9); // 0 to 9
        }

        #region sPHP custom header
        header("XX-Powered-By: {$this->Property["Environment"]->Name()}/{$this->Property["Environment"]->Version()->Full()}");
        //header("XX-sPHP-Developer: Binary Men");
        //header("XX-sPHP-Developer-URL: http://Binary.Men");
        #endregion sPHP custom header

        header("Content-Type: " . EXTENSION_MIMETYPE[strtolower($this->Property["DocumentType"])] . (!is_null($this->Property["CharacterSet"]) ? "; charset={$this->Property["CharacterSet"]}" : null));

        if($this->Property["DocumentType"] == DOCUMENT_TYPE_HTML && !isset($_POST["_MainContentOnly"])){
            $HTML_Head_META[] = HTML\Meta(null, null, null, null, strtolower($this->Property["CharacterSet"]));

            #region Filter duplicate META by name
            $HTMLHeadMETAByName = [];

            foreach($this->HTML_Head_META as $Item)if($Item["Name"]){
                $HTMLHeadMETAByName[$Item["Name"]] = $Item;
            }
            else{
                $HTMLHeadMETAByName[] = $Item;
            }

            foreach($HTMLHeadMETAByName as $Item)$HTML_Head_META[] = HTML\Meta($Item["Name"], $Item["Content"], $Item["HTTPEquivalent"], $Item["Property"], $Item["CharacterSet"]);
            $HTML_Head_META[] = HTML\Meta("theme-color", $this->Property["ThemeColor"]);
            #endregion Filter duplicate META by name

            foreach($this->HTML_Head_Link as $Item)$HTML_Head_Link[] = "<link" . ($Item["Relation"] ? " rel=\"{$Item["Relation"]}\"" : null) . ($Item["Type"] ? " type=\"{$Item["Type"]}\"" : null) . ($Item["URL"] ? " href=\"{$Item["URL"]}\"" : null) . ">";
            $HTML_Head_Link[] = "<link rel=\"manifest\" href=\"{$this->Property["Manifest"]}\">";

            foreach($this->HTML_Head_JavaScript as $Item)$HTML_Head_JavaScript[] = "<script" . ($Item["URL"] ? " src=\"{$Item["URL"]}\"" : null) . "></script>";

            print "<!DOCTYPE html>
<html lang=\"{$this->Property["Language"]->HTMLCode()}\">
<head>
    " . implode(null, $HTML_Head_META) . "
    <link rel=\"shortcut icon\" href=\"{$this->Property["Environment"]->ImageURL()}{$this->Property["Icon"]}.ico\">
    <title>{$this->Property["Title"]}</title>
    " . (isset($HTML_Head_Link) ? implode(null, $HTML_Head_Link) : null) . "
    " . (isset($HTML_Head_JavaScript) ? implode(null, $HTML_Head_JavaScript) : null) . "
    {$this->Property["HTMLHeadCode"]}
</head>

<body" . (isset($_POST["_NoHeader"]) && isset($_POST["_NoFooter"]) ? " class=\"ContentView\"" : null) . ">
    <a id=\"PageLocation_Top\"></a>";
        }

        foreach(array_merge(isset($this->Buffer[OUTPUT_BUFFER_MODE_HEADER]) ? $this->Buffer[OUTPUT_BUFFER_MODE_HEADER] : [], isset($this->Buffer[OUTPUT_BUFFER_MODE_MAIN]) ? $this->Buffer[OUTPUT_BUFFER_MODE_MAIN] : []) as $Content)print $Content;

        if($this->Property["DocumentType"] == DOCUMENT_TYPE_HTML && !isset($_POST["_MainContentOnly"])){
            print "{$this->Property["Environment"]->Utility()->Debug()->CheckpointHTML()}</body></html>";
        }

        return true;
    }

    public function Flush(){
        ob_clean(); // Fire the output handler for internal buffer management
        //DebugDump("Terminal->Flush()");
        return null;
    }

    public function Clear($Mode = OUTPUT_BUFFER_MODE_MAIN){
		$Result = true;

        $this->Flush(); // Flush/transfer contents to the buffer before clearing the buffer -- March 31, 2020

        if($Mode == OUTPUT_BUFFER_MODE_ALL){
			$this->Buffer = [];
		}
		else{
			$this->Buffer[$Mode] = [];
		}
        //var_dump("Terminal->Clear({$Mode})");
        return $Result;
    }

	public function Content($Mode = OUTPUT_BUFFER_MODE_MAIN){
		$Result = $this->Buffer[$Mode];

		return $Result;
	}

    public function Header($Name, $Value = null){
        header("{$Name}: {$Value}");

        return true;
    }

    public function META($Name = null, $Content = null, $HTTPEquivalent = null, $Property = null, $CharacterSet = null){
        $this->HTML_Head_META[] = ["Name" => $Name, "Content" => $Content, "HTTPEquivalent" => $HTTPEquivalent, "Property" => $Property, "CharacterSet" => $CharacterSet];

        return true;
    }

    public function Link($Relation = null, $Type = null, $URL = null){
        $this->HTML_Head_Link[] = ["Relation"=>$Relation, "Type"=>$Type, "URL"=>$URL];

        return true;
    }

    public function JavaScript($URL = null){
        if(!in_array($URL, $this->HTML_Head_JavaScript))$this->HTML_Head_JavaScript[] = ["URL"=>$URL];

        return true;
    }

	public function Redirect($URL, $Message = null){
		if(!$URL)$URL = $_SERVER["HTTP_REFERER"];

		if(!$this->Property["Environment"]->Utility()->Debug()->Enabled()){
			$this->Header("Location", $URL);
			$this->META(null, "0; {$URL}", "refresh");
			print "<script>window.location = '{$URL}';</script>";
		}/**/

		print HTML\UI\MessageBox("
			<p>" . ($Message ? $Message : "You are being redirected to the requested location, please wait while we redirect you.") . "</p>
			<p>Please click <a href=\"{$URL}\">here</a> if you are not redirected within 3 seconds.</p>
		", "System");

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

    public function Title($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function DocumentType($Value = null, $ClearBuffer = false){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			if($ClearBuffer)$this->Clear();

            $Result = true;
        }

        return $Result;
    }

    public function Language($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function CharacterSet($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function HTMLHeadCode($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Mode($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function DocumentName($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Header("Content-Disposition", " inline; filename={$this->Property[__FUNCTION__]}");

            $Result = true;
        }

        return $Result;
    }

    public function Suspended($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            //DebugDump("Terminal->Suspended({$Value})");
            //if($Value)$this->Flush(); // Flush current output to apply suspension to the next/rest
            $this->Flush(); // We should flush regardless suspension state, otherise buffer won't clear!

            $this->Property[__FUNCTION__] = $Value;

			$Result = true;
        }

        return $Result;
    }

    public function Icon($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$Result = true;
        }

        return $Result;
    }

    public function BackgroundColor($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function ThemeColor($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Manifest($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$Result = true;
        }

        return $Result;
    }
    #endregion Property

    #region Function
    // This function is called with each PRINT/ECHO by the PHP's built in output handler
    private function Send($Content){
        if(!$this->Property["Suspended"]){
			$this->Buffer[$this->Property["Mode"]][] = $Content;

			// We should not send any return value here because we are managing the output/buffering by ourselves
			//return null;
			return $Content; // This actually turned out to be compatible with both custom output buffering and error output!
			// WARNING!!! Fatal error is not shown if we don't return any output here!
		}
    }
    #endregion Function
}

class Session{
    #region Property variable
    private $Property = [
        "Environment"			=>	null,
        "Guest"					=>	null,
        "Lifetime"				=>	20 * 60, // In seconds
        "Isolate"				=>	true,
        "User"					=>	null,
        "Name"					=>	null,
		"ContentEditMode"		=>	false,
        "DebugMode"				=>	false,
        "IgnoreActivity"        =>  false, 
        
        // Read only
		"LastActivityTime"		=>	null,
		"ID"					=>	null,
        "IsFresh"				=>	false,
        "IsReset"				=>	false,
        "IsGuest"				=>	null,
		"UserSetTime"			=>	null,
		"Impersonated"			=>	false,
    ];
    #endregion Property variable

    #region Variable
	private static $AlreadyInstantiated = false;
    #endregion Variable

    #region Method
    public function __construct($Environment = null, $Guest = null, $Lifetime = null, $Isolate = null, $User = null, $Name = null, $ContentEditMode = null, $DebugMode = null, $IgnoreActivity = null){
		// Check if already instantiated and throw error if tried again
		if($this::$AlreadyInstantiated)trigger_error("Instantiation is prohibited for " . __CLASS__ .  " object.");
		$this::$AlreadyInstantiated = true;

        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

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
            // Reset session upon activity time out
            if((time() - $_SESSION["LastActivityTime"]) > $this->Property["Lifetime"]){
				$this->Reset(); // Reset session to set up guest properties
			}
			else{ // Existing valid session
				// Reflect session values to external resources
				$this->Property["Environment"]->Utility()->Debug()->Enabled($_SESSION["DebugMode"]);
			}
        }

        return true;
    }

	public function Reset(){
		$Result = true;

		$this->Property["IsReset"] = true;

		// Set user through method to take user change related actions
		$this->User($this->Property["Guest"]);

		return $Result;
	}

	public function Impersonate($User){
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
            $Result = $_SESSION[__FUNCTION__];
        }
        else{
            $_SESSION[__FUNCTION__] = $Value;

			$this->Property["IsGuest"] = null;

			$_SESSION["ContentEditMode"] = false;
			$_SESSION["DebugMode"] = false;
			$_SESSION["UserSetTime"] = time();
			$_SESSION["Impersonated"] = false;

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

class Application{
    #region Property
    private $Property = [
        "Terminal"				=>	null,
        "Session"				=>	null,
        "Guest"					=>	null,
        "Administrator"			=>	null,
        "Company"				=>	null,
        "Name"					=>	null,
        "ShortName"				=>	null,
        "Title"					=>	null,
        "TitlePrefix"			=>	"My application",
        "TitleSuffix"			=>	null,
        "TitleSeperator"		=>	": ",
        "Description"			=>	null,
		"DateFormat"			=>	"D, M j, Y",
		"ShortDateFormat"		=>	"M j, Y",
		"LongDateFormat"		=>	"l, F j, Y",
		"TimeFormat"			=>	"H:i:s",
        "Keyword"				=>	null,
		"Database"				=>	null,
        "EncryptionKey"			=>	null,
        "UseSystemScript"		=>	true,
        "SMTPBodyStyle"			=>	null,
        "Viewport"				=>	null,
        "DocumentType"			=>	DOCUMENT_TYPE_HTML,
        "Language"				=>	null,
        "DefaultScript"			=>	"Home",
        "CharacterSet"			=>	CHARACTER_SET_UTF8,
        "Version"				=>	null,
        "StatusCode"			=>	HTTP_STATUS_CODE_OK,
		"Data"					=>	[],
        "OpenGraph"             =>  null,
    ];
    #endregion Property

    #region Variable
	private static $AlreadyInstantiated = false;
	private $NotificationType = [];
	private $NotificationSource = [];
    #endregion Variable

    #region Method
    public function __construct($Terminal = null, $Session = null){
		// Check if already instantiated and throw error if tried again
		if($this::$AlreadyInstantiated)trigger_error("Instantiation is prohibited for " . __CLASS__ .  " object.");
		$this::$AlreadyInstantiated = true;

		$this->Property["Database"] = new Database();
		$this->Property["EncryptionKey"] = $_SERVER["SERVER_NAME"];
		$this->Property["OpenGraph"] = new OpenGraph(null, null, null, null, null, time() - (24 * 60 * 60));

        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){
        #region Load configuration
        $Configuration = ___LoadConfiguration($this); // Load default configuration

		// Load configuration saved by the user
		$SavedConfiguration = new Content("System settings", [], $this->Property["Terminal"]->Environment()->ContentPath());
		foreach($SavedConfiguration->Value() as $Key=>$Value)$Configuration[$Key] = $Value;

        #region Set configuration
		// Set up configuration with global vital effect
		$this->Property["Terminal"]->Environment()->TimeZone($Configuration["Timezone"]);

		// Loop for direct properties for configuration items
        foreach($Configuration as $Key => $Value)if(!is_null($Value) && array_key_exists($Key, $this->Property))$this->$Key($Value);

		//if(!$Configuration["CustomError"])restore_error_handler(); // Use PHP's error reporting if wanted
		//if($Configuration["CustomError"])___SetErrorHandler(); // Custom error tuggle is incorporated in the Environment object
		$this->Property["Terminal"]->Environment()->CustomError($Configuration["CustomError"]);

		// Set indirect properties from configuration items
        $this->Property["Guest"] = new User($Configuration["GuestEmail"], null, $Configuration["GuestName"], $Configuration["CompanyPhone"], null, null, null, null, null, "GUEST", null, null, null, "GUEST");
        $this->Property["Administrator"] = new User($Configuration["AdministratorEmail"], $Configuration["AdministratorPasswordHash"], $Configuration["AdministratorName"]);
        $this->Property["Company"] = new User($Configuration["CompanyEmail"], null, $Configuration["CompanyName"], $Configuration["CompanyPhone"], $Configuration["CompanyAddress"], $Configuration["CompanyURL"]);
		$this->Property["Version"] = new Version($Configuration["VersionMajor"], $Configuration["VersionMinor"], $Configuration["VersionRevision"]);

        // Other static assignments from the configuration
        $this->Property["Terminal"]->META("author", $Configuration["CompanyName"]);
        $this->Property["Terminal"]->Icon($Configuration["Icon"]);

        // Set indirect properties by calling to execute inner operations inside the set method
        $this->Language(new Language($Configuration["LanguageName"], $Configuration["LanguageCode"], $Configuration["LanguageRegionCode"], $Configuration["LanguageNativeName"], $Configuration["LanguageNativelyName"]));

		// Keep assigning the HEAD HTML here, modify it later assuming user can alter in in the script level
        require __DIR__ . "/tinymce_head_html.php";
        $this->Property["Terminal"]->HTMLHeadCode("{$Configuration["HTMLHeadCode"]}\n\n{$TinyMCEHTMLHeadCode}");

		// Set environment SMTP configuration
		$this->Property["Terminal"]->Environment()->SMTPHost($Configuration["SMTPHost"]);
		$this->Property["Terminal"]->Environment()->SMTPPort($Configuration["SMTPPort"]);
		$this->Property["Terminal"]->Environment()->SMTPUser($Configuration["SMTPUser"]);
		$this->Property["Terminal"]->Environment()->SMTPPassword($Configuration["SMTPPassword"]);

        // Assume default script to execute if no script is requested
        // Moved here from below to allow ignoring session activity
        // for specific scripts
		$_POST["_Script"] = strtolower(SetVariable("_Script", $this->Property["DefaultScript"]));

        // Set session configuration
		$this->Property["Session"]->Name($Configuration["SessionName"]);
		$this->Property["Session"]->Lifetime($Configuration["SessionLifetime"]);
        $this->Property["Session"]->Isolate($Configuration["SessionIsolate"]);
        $this->Property["Session"]->Guest($this->Property["Guest"]);

        // Ignore session activity for specific scripts as set with Configuration
        // Special care taken in case if this Configuration parameter is not set!
        // Remove the special care in future release once confirmed all applications have it
        foreach(isset($Configuration["SessionIgnoreScript"]) ? $Configuration["SessionIgnoreScript"] : [] as $SessionIgnoreScript){
            if($_POST["_Script"] == strtolower($SessionIgnoreScript)){ // Match found
                $this->Property["Session"]->IgnoreActivity(true);
                break; // Match found, no need to check anymore
            }
        }

		// Set up database
		if(
				isset($Configuration["DatabaseType"])
			&&	$Configuration["DatabaseType"]
			&&	$Configuration["DatabaseHost"]
			&&	$Configuration["DatabaseUser"]
			&&	$Configuration["DatabaseName"]
		){
			$this->Property["Database"] = new Database($Configuration["DatabaseType"], $Configuration["DatabaseHost"], $Configuration["DatabaseUser"], $Configuration["DatabasePassword"], $Configuration["DatabaseName"], $Configuration["DatabaseODBCDriver"], $Configuration["DatabaseTablePrefix"], $Configuration["DatabaseTimezone"], $Configuration["CharacterSet"], $Configuration["DatabaseStrictMode"]);
			$this->Property["Database"]->ErrorLogPath("{$this->Property["Terminal"]->Environment()->LogPath()}error/database/");
			$this->Property["Database"]->Connect();

			#region Add generic tables
			$Configuration["DatabaseTable"]["" . ($Entity = "Language") . ""] = new Database\Table("{$Entity}");
			$Configuration["DatabaseTable"]["" . ($Entity = "Country") . ""] = new Database\Table("{$Entity}");
			$Configuration["DatabaseTable"]["" . ($Entity = "Gender") . ""] = new Database\Table("{$Entity}");
			$Configuration["DatabaseTable"]["" . ($Entity = "User") . ""] = new Database\Table("{$Entity}");
			$Configuration["DatabaseTable"]["" . ($Entity = "User") . "Group"] = new Database\Table("{$Entity} group");
			$Configuration["DatabaseTable"]["" . ($Entity = "User") . "UserGroup"] = new Database\Table("{$Entity} user group");
			$Configuration["DatabaseTable"]["" . ($Entity = "Application") . "Traffic"] = new Database\Table("{$Entity} traffic", "ATr");
			$Configuration["DatabaseTable"]["" . ($Entity = "Measure") . "Type"] = new Database\Table("{$Entity} type");
			$Configuration["DatabaseTable"]["" . ($Entity = "Measure") . ""] = new Database\Table("{$Entity}");
			#endregion Add generic tables

			foreach($Configuration["DatabaseTable"] as $Table){ // Set dynamic & common table properties
				$Table->UploadPath("{$this->Property["Terminal"]->Environment()->UploadPath()}{$Table->Name()}/");
				$Table->SQLSELECTPath("{$this->Property["Terminal"]->Environment()->SQLSELECTPath()}" . strtolower($this->Property["Database"]->Type()) . "/");
				$Table->Database($this->Property["Database"]);
			}
		}
        #endregion Set configuration
        #endregion Load configuration

        $this->Property["Session"]->Start(); // Start the session after configuring accordingly

		#region Create session log upon each session reset
		if($Configuration["DatabaseLogTraffic"] && !is_null($this->Property["Database"]->Connection())){
			$Configuration["DatabaseTable"]["ApplicationTraffic"]->Put([ // Traffic
				"ApplicationTrafficServer"			=>	$_SERVER["SERVER_NAME"],
				"ApplicationTrafficHost"			=>	$_SERVER["HTTP_HOST"],
				"ApplicationTrafficSessionCode"		=>	$this->Property["Session"]->ID(),
				"ApplicationTrafficTime"			=>	date("Y-m-d H:i:s"),
				"ApplicationTrafficIP"				=>	$_SERVER["REMOTE_ADDR"],
				"ApplicationTrafficMethod"			=>	$_SERVER["REQUEST_METHOD"],
				"ApplicationTrafficProtocol"		=>	explode("/", $_SERVER["SERVER_PROTOCOL"])[0],
				"ApplicationTrafficURL"				=>	"{$this->Property["Terminal"]->Environment()->URLPath()}",
				"ApplicationTrafficQuery"			=>	$_SERVER["QUERY_STRING"],
				"ApplicationTrafficReferer"			=>	$_SERVER["HTTP_REFERER"],
				"ApplicationTrafficUserAgent"		=>	substr($_SERVER["HTTP_USER_AGENT"], 0, 255),
				"ApplicationTrafficExecutionBegin"	=>	date("Y-m-d H:i:s", $this->Property["Terminal"]->Environment()->Utility()->Debug()->BeginTime()),
				"UserID"							=>	$this->Property["Session"]->User()->ID(),
				"ApplicationTrafficIsActive"		=>	1,
			]);

			$ApplicationTrafficID = $this->Property["Database"]->Query("SELECT @@IDENTITY AS IDValue")[0][0]["IDValue"];
		}
		#endregion Create session log upon each session reset

		// Following session configuration needs to be set after the session starts
		// and will always overwrite session's current value in case of TRUE evaluation
		if($Configuration["ContentEditMode"] || in_array(strtoupper($_SERVER["SERVER_NAME"]), array_filter(explode(",", str_replace(" ", null, strtoupper($Configuration["ContentEditModeServer"]))))) || in_array(strtoupper($_SERVER["REMOTE_ADDR"]), array_filter(explode(",", str_replace(" ", null, strtoupper($Configuration["ContentEditModeClient"]))))))$this->Property["Session"]->ContentEditMode(true);
		if($Configuration["DebugMode"] || in_array(strtoupper($_SERVER["SERVER_NAME"]), array_filter(explode(",", str_replace(" ", null, strtoupper($Configuration["DebugModeServer"]))))) || in_array(strtoupper($_SERVER["REMOTE_ADDR"]), array_filter(explode(",", str_replace(" ", null, strtoupper($Configuration["DebugModeClient"]))))))$this->Property["Session"]->DebugMode(true);

        //
        // Assuming default script has been move above from here
        //

		// Configure stylesheet inclusion
        if(file_exists("{$this->Property["Terminal"]->Environment()->StylePath()}script/{$_POST["_Script"]}.css"))$Configuration["Stylesheet"][] = "{$this->Property["Terminal"]->Environment()->StyleURL()}script/{$_POST["_Script"]}.css";
        if(file_exists("{$this->Property["Terminal"]->Environment()->StylePath()}{$_SERVER["SERVER_NAME"]}/loader.css"))$Configuration["Stylesheet"][] = "{$this->Property["Terminal"]->Environment()->StyleURL()}{$_SERVER["SERVER_NAME"]}/loader.css";
        if(file_exists("{$this->Property["Terminal"]->Environment()->StylePath()}{$_SERVER["SERVER_NAME"]}/script/{$_POST["_Script"]}.css"))$Configuration["Stylesheet"][] = "{$this->Property["Terminal"]->Environment()->StyleURL()}{$_SERVER["SERVER_NAME"]}/script/{$_POST["_Script"]}.css";
		foreach($Configuration["Stylesheet"] as $URL)$this->Property["Terminal"]->Link("stylesheet", "text/css", $URL);

        // Moved here from above to allow including the script specific JavaScript
        if(file_exists("{$this->Property["Terminal"]->Environment()->Path()}javascript/script/{$_POST["_Script"]}.js"))$Configuration["JavaScript"][] = "{$this->Property["Terminal"]->Environment()->URL()}javascript/script/{$_POST["_Script"]}.js";
        foreach($Configuration["JavaScript"] as $URL)$this->Property["Terminal"]->JavaScript($URL);

		// Execute the requested script
		___ExecuteApplicationScript($this, new Template($this, $Configuration, $Configuration["TemplateCacheLifetime"], $Configuration["TemplateCacheActionMarker"]), $Configuration);

        // Add to the final HEAD HTML, assuming user has changed it in the script level
        $this->Property["Terminal"]->HTMLHeadCode("{$this->Property["Terminal"]->HTMLHeadCode()}\n\n<!-- OpenGraph meta tag: BEGIN -->\n{$this->Property["OpenGraph"]->MetaHTML()}\n<!-- OpenGraph meta tag: END -->");

		#region Update traffic log with additional information upon each request
		if($Configuration["DatabaseLogTraffic"] && !is_null($this->Property["Database"]) && !is_null($this->Property["Database"]->Connection())){
			$ResourceUsage = function_exists("getrusage") ? getrusage() : ["ru_utime.tv_usec" => null, "ru_stime.tv_usec" => null, ];

			$Configuration["DatabaseTable"]["ApplicationTraffic"]->Put([
				"ApplicationTrafficScript"						=>	$_POST["_Script"],
				"ApplicationTrafficResourceUsageDurationUser"	=>	$ResourceUsage["ru_utime.tv_usec"],
				"ApplicationTrafficResourceUsageDurationSystem"	=>	$ResourceUsage["ru_stime.tv_usec"],
				"ApplicationTrafficExecutionEnd"				=>	date("Y-m-d H:i:s", time()),
			], "ApplicationTrafficID = {$ApplicationTrafficID}");
		}
		#endregion Update traffic log with additional information upon each request

        return true;
    }

    public function URL($Script = null, $Argument = null, $Anchor = null, $Secure = false, $Full = false, $IgnorePersistence = false){
        $Argument = $Argument ? [$Argument] : [];
        if($Script)$Argument[] = "_Script={$Script}";

		if(!$IgnorePersistence){
			if(isset($_POST["_NoHeader"]))$Argument[] = "_NoHeader";
			if(isset($_POST["_NoFooter"]))$Argument[] = "_NoFooter";
			if(isset($_POST["_MainContentOnly"]))$Argument[] = "_MainContentOnly";
		}

        //$Result = "" . strtolower($this->Property["Terminal"]->Environment()->Protocol()) . ($Secure ? "s" : null) . "://{$_SERVER["SERVER_NAME"]}/{$this->Property["Terminal"]->Environment()->URLPath()}". ($Full ? "index.php" : null) . (count($Argument) ? "?" . implode("&", $Argument) : null) . ($Anchor ? "#{$Anchor}" : null);
        $Result = "{$this->Property["Terminal"]->Environment()->URL()}". ($Full ? "index.php" : null) . (count($Argument) ? "?" . implode("&", $Argument) : null) . ($Anchor ? "#{$Anchor}" : null);

        return $Result;
    }

	public function Notify( // Create notification records to be processed by the notifcation dispatcher
		$Notification = [
			"To" => null, // Email address | Mobile phone number
			"Subject" => null,
			"Message" => null,
			"EventTime" => null, // Date time literal YYYY-MM-DD HH:MM:SS
			"Signature" => "AN_UNIQUE_SIGNATURE_TO_FILTER_OUT_DUPLICATE_NOTIFICATION",
			"Type" => null, // NOTIFICATION_TYPE_EMAIL | NOTIFICATION_TYPE_MOBILE_SMS
			"Source" => null, // NOTIFICATION_SOURCE_SYSTEM | NOTIFICATION_SOURCE_MANUAL
			"From" => null, // Typically the FROM email address
		],
		$Active = false
	){
		if(!is_array($Notification))$Notification = [];
		if(!isset($Notification[0]))$Notification = [$Notification];

		$Database = $this->Property["Database"];

		if(!count($this->NotificationType)){
			$Recordset = $Database->Query("
				SELECT * FROM sphp_notificationtype;
				SELECT * FROM sphp_notificationsource;
			");

			foreach($Recordset[0] as $NotificationType){
				$this->NotificationType[$NotificationType["NotificationTypeIdentifier"]] = [
					"ID" => $NotificationType["NotificationTypeID"],
				];
			}

			foreach($Recordset[1] as $NotificationSource){
				$this->NotificationSource[$NotificationSource["NotificationSourceIdentifier"]] = [
					"ID" => $NotificationSource["NotificationSourceID"],
				];
			}
		}

		$SQL_INSERT_VALUE = [];

		foreach($Notification as $ThisNotification){
			// Make sure all attributes for the notification item is available
			foreach(explode(",", str_replace(" ", null, "To, Subject, Message, EventTime, Signature, Type, Source, From")) as $Attribute){
				if(!isset($ThisNotification[$Attribute])){
					$ThisNotification[$Attribute] = null;
				}
			}

			if($ThisNotification["To"] && $ThisNotification["Message"]){ // Potential notification item
				if(!$ThisNotification["Type"]){ // Set type if not available
					if($ThisNotification["Subject"]){ // Decice to be EMAIL if there is a subject available
						$ThisNotification["Type"] = NOTIFICATION_TYPE_EMAIL;
					}
					else{ // Decice to be MOBILE_SMS if there is no subject available
						$ThisNotification["Type"] = NOTIFICATION_TYPE_MOBILE_SMS;
					}
				}

				if(!$ThisNotification["EventTime"])$ThisNotification["EventTime"] = date("Y-m-d H:i:s"); // Set current time as event time
				if(!$ThisNotification["Source"])$ThisNotification["Source"] = NOTIFICATION_SOURCE_SYSTEM; // Assume SYSTEM if source is not available
				if(!$ThisNotification["Signature"])$ThisNotification["Signature"] = md5($ThisNotification["Message"]);

				if(isset($this->NotificationType[$ThisNotification["Type"]]) && isset($this->NotificationSource[$ThisNotification["Source"]])){
					foreach(array_filter(explode(",", str_replace(" ", null, $ThisNotification["To"]))) as $To){
						$SQL_INSERT_VALUE[] = "(" . implode(", ", [
							"'{$Database->Escape($ThisNotification["Signature"])}'",
							"'{$Database->Escape($ThisNotification["EventTime"])}'",
							$ThisNotification["Subject"] ? "'{$Database->Escape($ThisNotification["Subject"])}'" : "NULL",
							"'{$Database->Escape($ThisNotification["Message"])}'",
							$this->NotificationType[$ThisNotification["Type"]]["ID"],
							$this->NotificationSource[$ThisNotification["Source"]]["ID"],
							"'{$Database->Escape($To)}'",
							$ThisNotification["From"] ? "'{$Database->Escape($ThisNotification["From"])}'" : "NULL",
							$Active ? 1 : 0,
							"NOW()",
						]) . ")";
					}
				}
			}
		}

		if(count($SQL_INSERT_VALUE)){
			$SQL = "INSERT IGNORE INTO sphp_notification (
				NotificationSignature,
				NotificationEventTime,
				NotificationSubject,
				NotificationMessage,
				NotificationTypeID,
				NotificationSourceID,
				NotificationTo,
				NotificationFrom,
				NotificationIsActive,
				TimeInserted
			) VALUES " . implode(", ", $SQL_INSERT_VALUE) . ";";
            //DebugDump("<pre>{$SQL}</pre>");
			$Recordset = $Database->Query($SQL);
		}

		return true;
    }
    
    public function NotifyUserDevice($Message, $UserID = null, $Subject = null, $UserGroupIdentifier = null, $EventTime = null){
        if(!is_array($UserID))$UserID = is_null($UserID) ? [] : [$UserID];
        if(!$Subject)$Subject = $this->Property["Name"];
        if(!is_array($UserGroupIdentifier))$UserGroupIdentifier = is_null($UserGroupIdentifier) ? [] : [$UserGroupIdentifier];
        if(is_null($EventTime))$EventTime = date("Y-m-d H:i:s");

        $UserIDFrom = intval($this->Property["Session"]->User()->ID()); // Needed to exlude current user + Signature
    
        $SQL = "
            # Create notification
            INSERT IGNORE INTO sphp_notification (
                NotificationSignature, 
                NotificationEventTime, 
                NotificationSubject, 
                NotificationMessage, 
                NotificationTypeID, 
                NotificationSourceID, 
                NotificationTo, 
                UserIDFrom, # Should we really use this
                NotificationSentTime, 
                NotificationIsActive, 
                TimeInserted
            ) VALUES (
                MD5(CONCAT('{$UserIDFrom}.{$Message}')), 
                '{$EventTime}', # NotificationEventTime
                '{$this->Property["Database"]->Escape($Subject)}', # NotificationSubject
                '{$this->Property["Database"]->Escape($Message)}', # NotificationMessage
                (SELECT NT.NotificationTypeID FROM sphp_notificationtype AS NT WHERE NT.NotificationTypeIdentifier = 'PUSH'), # NotificationTypeID
                (SELECT NS.NotificationSourceID FROM sphp_notificationsource AS NS WHERE NS.NotificationSourceIdentifier = 'SYSTEM'), # NotificationSourceID
                '', # NotificationTo
                {$UserIDFrom}, # UserIDFrom # Should we really use this
                NOW(), # NotificationSentTime, 
                1, # NotificationIsActive
                NOW() # TimeInserted
            );
    
            # Tag Notification to UserUserDevice
            INSERT IGNORE INTO sphp_useruserdevicenotification (UserUserDeviceID, NotificationID, UserUserDeviceNotificationIsRead, UserUserDeviceNotificationIsActive, TimeInserted)
            SELECT			UUD.UserUserDeviceID, 
                            @@IDENTITY, 
                            0, 1, NOW()
                            #, UUG.UserID, UUG.UserGroupID, UUD.UserDeviceID
            FROM			sphp_useruserdevice AS UUD
                LEFT JOIN	sphp_user AS U ON U.UserID = UUD.UserID
                LEFT JOIN	sphp_userusergroup AS UUG ON UUG.UserID = U.UserID
                LEFT JOIN	sphp_usergroup AS UG ON UG.UserGroupID = UUG.UserGroupID
            WHERE			TRUE
                AND			" . (count($UserID) ? "UUG.UserID IN (" . implode(", ", $UserID) . ")" : "TRUE") . " # Filter User
                AND			" . (count($UserGroupIdentifier) ? "UG.UserGroupIdentifier IN ('" . implode("', '", $UserGroupIdentifier) . "')" : "TRUE") . " # Filter UserGroupIdentifier
                AND         U.UserID != {$UserIDFrom} # Exclude the User generating the notification
                AND			U.UserIsActive = 1
                AND			UG.UserGroupIsActive = 1
                AND			UUG.UserUserGroupIsActive = 1
                AND			UUD.UserUserDeviceID IS NOT NULL # Must have a device to notify on
            ;
    
            SELECT 'DONE' AS Status;
        "; //DebugDump("<pre>{$SQL}</pre>");

        if(!is_null($this->Property["Database"]->Connection())){ // We have a working database
            if(isset($this->Property["Database"]->Query($SQL)[0][0]["Status"])){ // Database query succeeded
                $Result = true;
                //print HTML\UI\MessageBox("Notification created successfully", "System");
            }
            else{ // Database query failed
                $Result = false;
                print HTML\UI\MessageBox("Failed creating notification!", "System", "MessageBoxError");
            }
        }
        else{ // Database is not available
            $Result = true;
        }
    
        return $Result;    
    }
    #endregion Method

    #region Property
    public function Terminal($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $this->Property["OpenGraph"]->URL($this->Property[__FUNCTION__]->Environment()->URL());
            $this->Property["OpenGraph"]->Image("{$this->Property[__FUNCTION__]->Environment()->ImageURL()}logo.png");

            $Result = true;
        }

        return $Result;
    }

    public function Session($Value = null){
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

    public function Administrator($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Company($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

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

    public function ShortName($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Title($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $this->SetTerminalTitle();

            $Result = true;
        }

        return $Result;
    }

    public function TitlePrefix($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $this->SetTerminalTitle();

            $Result = true;
        }

        return $Result;
    }

    public function TitleSuffix($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $this->SetTerminalTitle();

            $Result = true;
        }

        return $Result;
    }

    public function TitleSeperator($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $this->SetTerminalTitle();

            $Result = true;
        }

        return $Result;
    }

    public function Description($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $this->Property["Terminal"]->META("description", $this->Property[__FUNCTION__]);
            $this->Property["OpenGraph"]->Description($this->Property[__FUNCTION__]);

            $Result = true;
        }

        return $Result;
    }

    public function DateFormat($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function ShortDateFormat($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function LongDateFormat($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function TimeFormat($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Keyword($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $this->Property["Terminal"]->META("keywords", $this->Property[__FUNCTION__]);

            $Result = true;
        }

        return $Result;
    }

    public function Database(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function EncryptionKey($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function UseSystemScript($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function SMTPBodyStyle($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Viewport($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $this->Property["Terminal"]->META("viewport", $this->Property[__FUNCTION__]);

            $Result = true;
        }

        return $Result;
    }

    // This immediately calls the same function of Terminal object! Can be said Application alias for Terminal object
    public function DocumentType($Value = null, $ClearBuffer = false){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $this->Property["Terminal"]->DocumentType($this->Property[__FUNCTION__], $ClearBuffer);

            $Result = true;
        }

        return $Result;
    }

    public function Language($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $this->Property["Terminal"]->Language($this->Property[__FUNCTION__]);
            $this->Property["OpenGraph"]->Locale($this->Property[__FUNCTION__]->HTMLCode());

            $Result = true;
        }

        return $Result;
    }

    public function DefaultScript($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function CharacterSet($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $this->Property["Terminal"]->CharacterSet($this->Property[__FUNCTION__]);

            $Result = true;
        }

        return $Result;
    }

    public function Version($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function StatusCode($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            http_response_code($this->Property[__FUNCTION__]);

            $Result = true;
        }

        return $Result;
    }

    public function Data($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function OpenGraph($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }
    #endregion Property

    #region Function
    private function SetTerminalTitle(){
        $this->Property["Terminal"]->Title(implode($this->Property["TitleSeperator"], array_filter([$this->Property["TitlePrefix"], $this->Property["Title"], $this->Property["TitleSuffix"], ])));
        $this->Property["OpenGraph"]->Title($this->Property["Terminal"]->Title());
        $this->Property["OpenGraph"]->ImageTitle($this->Property["OpenGraph"]->Title());

        return true;
    }
    #endregion Function
}

class Template{
    #region Property variable
    private $Property = [
		"Application"				=>	null,
        "Configuration"				=>	null,
        "Lifetime"					=>	60 * 5, // Seconds
        "ActionMarker"				=>	"#",
		"NoCache"					=>	false, // Trade off flag between performance and memory consumption
		"ViewPath"					=>	"./template/view/",
		"CachePath"					=>	"./template/cache/",
        "FileName"					=>	null,
        "ViewFile"					=>	null,
        "ViewFilePath"				=>	null,
        "CacheFile"					=>	null,
        "CacheFilePath"				=>	null,
		"TimeToExpire"				=>	0,
		"Expired"					=>	true,
    ];
    #endregion Property variable

	#region Private variable
	private static $AlreadyInstantiated = false;
	private $Cache = []; // Memory to hold once loaded content for faster subsequent delivery
	#endregion Private variable

    #region Method
    public function __construct($Application = null, $Configuration = null, $Lifetime = null, $ActionMarker = null, $NoCache = null){
		// Check if already instantiated and throw error if tried again
		if($this::$AlreadyInstantiated)trigger_error("Instantiation is prohibited for " . __CLASS__ .  " object.");
		$this::$AlreadyInstantiated = true;

		// Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

        return true;
    }

	public function Content($Name, $Variable = [], $Process = null, $Lifetime = null, $DefaultContent = null){
		if(!isset($this->Cache[$Name])){ // Cache is not loaded into memory
			if($this->Expired($Name, $Lifetime)){ // Cache is expired
				// Create template view PHP with default content if missing
				if(!file_exists($this->Property["ViewFilePath"]))file_put_contents($this->Property["ViewFilePath"], $DefaultContent ? $DefaultContent : "This is default content for the template '{$Name}' as set in the view script!");

				$PreviousTerminalBufferMode = $this->Property["Application"]->Terminal()->Mode();
				$this->Property["Application"]->Terminal()->Flush(); // Flush any previous output to exclude from this template cache

				#region Regenerate cache
				$this->Property["Application"]->Terminal()->Mode(OUTPUT_BUFFER_MODE_TEMPLATE); // Change output buffer to template
				___ExecuteTemplateView($this->Property["ViewFilePath"], $Variable, $this->Property["Application"], $this->Property["Configuration"]);
				$this->Property["Application"]->Terminal()->Flush(); // Flush contents to the template buffer
				file_put_contents("{$this->Property["CacheFilePath"]}", $this->Property["Application"]->Terminal()->Content(OUTPUT_BUFFER_MODE_TEMPLATE));
				$this->Property["Application"]->Terminal()->Clear(OUTPUT_BUFFER_MODE_TEMPLATE); // Also clear the template buffer
				#endregion Regenerate cache

				//$this->Property["Application"]->Terminal()->Flush();
				$this->Property["Application"]->Terminal()->Mode($PreviousTerminalBufferMode); // Set output buffer mode back
			}

			$this->Cache[$Name] = file_get_contents($this->Property["CacheFilePath"]); // Load cache into memory
		}

		#region Process cache
		if($Process){ // // Parse for variables and directives as dynamic content
			$RestContent = $this->Cache[$Name];

			while($RestContent && preg_match("/{$this->Property["ActionMarker"]}[A-Za-z0-9_]+:?[^{$this->Property["ActionMarker"]}]+{$this->Property["ActionMarker"]}/u", $RestContent, $Match)){
				$PreContent[] = substr($RestContent, 0, strpos($RestContent, $Match[0]));

				$OriginalMatch = $Match[0];
				$Match = explode(":", $OriginalMatch);

				if(isset($Match[1])){
					$Command = strtoupper(substr($Match[0], 1, strlen($Match[0]) - 1));
					$Parameter = substr($Match[1], 0, strlen($Match[1]) - 1);
				}
				else{
					$Command = "VARIABLE";
					$Parameter = substr($Match[0], 1, strlen($Match[0]) - 2);
				}

				if(($Command == "VARIABLE" || $Command == "VAR") && isset($Variable[$Parameter])){
					$PreContent[] = $Variable[$Parameter];
				}
				elseif(($Command == "GET") && isset($_GET[$Parameter])){
					$PreContent[] = $_GET[$Parameter];
				}
				elseif(($Command == "POST" || $Command == "POS") && isset($_POST[$Parameter])){
					$PreContent[] = $_POST[$Parameter];
				}
				elseif(($Command == "REQUEST" || $Command == "REQ") && isset($_REQUEST[$Parameter])){
					$PreContent[] = $_REQUEST[$Parameter];
				}
				elseif(($Command == "SESSION" || $Command == "SES") && isset($_SESSION[$Parameter])){
					$PreContent[] = $_SESSION[$Parameter];
				}
				elseif(($Command == "SERVER" || $Command == "SER") && isset($_SERVER[$Parameter])){
					$PreContent[] = $_SERVER[$Parameter];
				}
				else{
					$PreContent[] = $OriginalMatch;
				}

				$RestContent = substr($RestContent, strpos($RestContent, $OriginalMatch) + strlen($OriginalMatch));
			}

			$Result = implode(null, isset($PreContent) ? $PreContent : []) . $RestContent; // Store content in memory (per request)
		}
		else{ // Don't parse, return as is as flat content
			$Result = $this->Cache[$Name];
		}
		#endregion Process cache

		if($this->Property["NoCache"])$this->Cache = []; // Destroy memory cache

		return $Result;
	}
    #endregion Method

    #region Property
    public function Application($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["ViewPath"] = "{$this->Property["Application"]->Terminal()->Environment()->Path()}template/view/";
			$this->Property["CachePath"] = "{$this->Property["Application"]->Terminal()->Environment()->Path()}template/cache/";

			$Result = true;
        }

        return $Result;
    }

    public function Configuration($Value = null){
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

    public function ActionMarker($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function NoCache($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function ViewPath(){
		$Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function CachePath(){
		$Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function FileName(){
		$Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function ViewFile(){
		$Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function ViewFilePath(){
		$Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function CacheFile(){
		$Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function CacheFilePath(){
		$Result = $this->Property[__FUNCTION__];

        return $Result;
    }

	public function TimeToExpire($Name, $Lifetime = null){
		$this->SetName($Name);
		if(is_null($Lifetime))$Lifetime = $this->Property["Lifetime"];

		$Result = (
				!file_exists("{$this->Property["CacheFilePath"]}")
			||	(time() - filemtime($this->Property["CacheFilePath"])) > $Lifetime
		) ? 0 : ($Lifetime - time() + filemtime($this->Property["CacheFilePath"]));

		return $Result;
	}

	public function Expired($Name, $Lifetime = null){
		$Result = $this->TimeToExpire($Name, $Lifetime) == 0 ? true : false;

		return $Result;
	}
    #endregion Property

	#region Function
    private function SetName($Value){
		$Result = true;

		$this->Property["FileName"] = "{$this->Property["Application"]->Terminal()->Environment()->Utility()->ValidFileName($Value)}";

		$this->Property["ViewFile"] = "{$this->Property["FileName"]}.php";
		$this->Property["ViewFilePath"] = "{$this->Property["ViewPath"]}{$this->Property["ViewFile"]}";

		$CacheFilenamePrefix = "{$this->Property["FileName"]}_UserID_{$this->Property["Application"]->Session()->User()->ID()}";

		$this->Property["CacheFile"] = "{$CacheFilenamePrefix}_" . md5($CacheFilenamePrefix) . ".tpc";
		$this->Property["CacheFilePath"] = "{$this->Property["CachePath"]}{$this->Property["CacheFile"]}";

        return $Result;
    }
	#endregion Function
}

class Utility{
	#region Property variable
    private $Property = [
        "Debug"				=>	null,
        "Graphic"			=>	null,
    ];
    #endregion Property variable

	#region Private variable
	private $MaxMindGeoIP2Reader = false;
	#region Private variable

    #region Method
    public function __construct($Debug = null){
		// Set default property values
		$this->Property["Graphic"] = new Graphic();

        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

        return true;
    }

	public function Upload($Path, $Field = null, $SetPOST = null, $MustRename = null, $AllowedExtension = null, $ForbiddenExtension = null){
		if(is_null($SetPOST))$SetPOST = true;
		if(is_null($MustRename))$MustRename = true;
		if(is_null($ForbiddenExtension))$ForbiddenExtension = "asp, aspx, bat, bin, cfm, cfc, com, exe, jsp, pl, py, sh, shtml";
        //var_dump($_FILES);
		$POSTFileField = array_keys($_FILES);
		if(count($POSTFileField) && !is_dir($Path))mkdir($Path, 0777, true); // Create the target folder it doesn't exist
        //var_dump($POSTFileField);
		foreach(isset($Field) ? array_intersect(explode(",", str_replace(" ", null, $Field)), $POSTFileField) : $POSTFileField as $Field){
            //var_dump($Field);
			if(is_array($_FILES[$Field]["name"])){
                //var_dump($_FILES[$Field]["name"]);
				foreach($_FILES[$Field]["name"] as $Key=>$Value){
					$Result[$Field][$Key] = $this->MoveUploadedItem($Path, $Value, $_FILES[$Field]["tmp_name"][$Key], $MustRename, $AllowedExtension, $ForbiddenExtension);
					if($SetPOST)$_POST[$Field][$Key] = $Result[$Field][$Key];
				}
			}
			else{
                //var_dump($Path, $_FILES[$Field]["name"], $_FILES[$Field]["tmp_name"]);
				$Result[$Field] = $this->MoveUploadedItem($Path, $_FILES[$Field]["name"], $_FILES[$Field]["tmp_name"], $MustRename, $AllowedExtension, $ForbiddenExtension);
				if($SetPOST)$_POST[$Field] = $Result[$Field];
			}
		}
        //var_dump($Result);
		return isset($Result) ? $Result : false;
	}

    public function ValidFileName($Name, $ReplacementCharacter = "_"){
		$Result = str_replace(str_split("~@#%^&*+`={}[];':\"<>?,/|\\ "), $ReplacementCharacter, $Name);

		return $Result;
	}

	public function UniqueFileName($File){
		$FileInformation = pathinfo($File);
		while(file_exists("" . ($File = "{$FileInformation["dirname"]}/{$this->GUID()}{$this->GUID()}" . ($FileInformation["extension"] ? ".{$FileInformation["extension"]}" : null) . "") . ""));

		return $File;
	}

    public function ValidVariableName($Name, $ReplacementCharacter = "_"){
		$Result = str_replace(str_split(".~@#%^&*+`={}[];':\"<>?,/|\\ "), $ReplacementCharacter, $Name);

		return $Result;
	}

	public function CleanPath($Path){
		$Result = true;

		foreach(glob("{$Path}*") as $Item){
			if(is_file($Item)){
				@unlink($Item);
			}
			else{
				$this->CleanPath("{$Item}/");
				@rmdir($Item);
			}
		}

		return $Result;
	}

	public function GUID($Hyphen = false, $CurlyBrace = false){
		/*
			Create a globally unique string

			Hyphen			[BOOLEAN]		=		TRUE = Add hyphens like Microsoft SQL Server UUID like UNIQUE-ID-VALUE; FALSE = Don't use hyphens
			CurlyBrace		[BOOLEAN]		=		TRUE = Wrap within curly braces like {UNIQUE ID}; FALSE = Don't use curly braces
		*/

		$HyphenCharacter = "";
		if($Hyphen)$HyphenCharacter = "-";
		$GUID = sprintf("%04x%04x".$HyphenCharacter."%04x".$HyphenCharacter."%04x".$HyphenCharacter."%04x".$HyphenCharacter."%04x%04x%04x", mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
		if($CurlyBrace)$GUID = "{" . $GUID . "}";

		return strtoupper($GUID);
	}

	public function ListToArray($List, $Separator = null, $DiscardSpace = null, $IgnoreEmpty = null){
		if(is_null($Separator))$Separator = ",";
		if(is_null($DiscardSpace))$DiscardSpace = true;
		if(is_null($IgnoreEmpty))$IgnoreEmpty = true;

		if($DiscardSpace)$List = str_replace(" ", null, $List);
		$List = explode($Separator, trim($List));
		if($IgnoreEmpty)$List = array_filter($List);

		return $List;
	}

	public function IP2Geo($IP = null){
		//var_dump("" . __CLASS__ . "->" . __FUNCTION__ . "('{$IP}')");
		if($this->MaxMindGeoIP2Reader === false){ // Load MaxMindGeoIP2Reader if not already loaded
			#region Extract MaxMind GeoLite2 City database if not exists
			$GeoLite2CityDatabasePath = __DIR__ . "/library/3rdparty/maxmind/";
			$GeoLite2CityDatabase = "{$GeoLite2CityDatabasePath}GeoLite2-City.mmdb";

			if(!file_exists($GeoLite2CityDatabase)){
				//var_dump($GeoLite2CityDatabase, "GeoLite2 City database missing");
				$ZipArchive = new \ZipArchive;
				if($ZipArchive->open("{$GeoLite2CityDatabasePath}GeoLite2-City.zip") === true){
					$ZipArchive->extractTo($GeoLite2CityDatabasePath);
					//var_dump("GeoLite2 City database decompression successful");
					$ZipArchive->close();
				}
				else{
					//var_dump("Failed to decompress MaxMind GeoLite2 City database");
					trigger_error("Failed to decompress MaxMind GeoLite2 City database");
				}
			}
			else{
				//var_dump("GeoLite2 City database found");
			}
			#endregion Extract MaxMind GeoLite2 City database if not exists

			require __DIR__ . "/library/3rdparty/maxmind/geoip2.phar";
			$this->MaxMindGeoIP2Reader = new \GeoIp2\Database\Reader($GeoLite2CityDatabase);
		}

		try{
			$Result = $this->MaxMindGeoIP2Reader->city($IP ? $IP : $_SERVER["REMOTE_ADDR"]);
		}
		catch(\Exception $e){
			$Result = false;
		}

		return $Result;
	}

	public function NumberInWord($num = false){
		// Credit goes to: http://stackoverflow.com/questions/11500088/php-express-number-in-words

		$num = str_replace(array(",", " "), null , trim($num));

		if(!$num)return false;

		$num = (int) $num;
		$words = array();

		$list1 = array(null, "one", "two", "three", "four", "five", "six", "seven", "eight", "nine", "ten", "eleven", "twelve", "thirteen", "fourteen", "fifteen", "sixteen", "seventeen", "eighteen", "nineteen");
		$list2 = array(null, "ten", "twenty", "thirty", "forty", "fifty", "sixty", "seventy", "eighty", "ninety", "hundred");
		$list3 = array(null, "thousand", "million", "billion", "trillion", "quadrillion", "quintillion", "sextillion", "septillion", "octillion", "nonillion", "decillion", "undecillion", "duodecillion", "tredecillion", "quattuordecillion", "quindecillion", "sexdecillion", "septendecillion", "octodecillion", "novemdecillion", "vigintillion");

		$num_length = strlen($num);
		$levels = (int) (($num_length + 2) / 3);
		$max_length = $levels * 3;
		$num = substr("00" . $num, -$max_length);
		$num_levels = str_split($num, 3);

		for($i = 0; $i < count($num_levels); $i++){
			$levels--;
			$hundreds = (int) ($num_levels[$i] / 100);
			$hundreds = ($hundreds ? " {$list1[$hundreds]} hundred" . ($hundreds == 1 ? null : "s") . " " : null);
			$tens = (int) ($num_levels[$i] % 100);
			$singles = null;

			if($tens < 20){
				$tens = ($tens ? " {$list1[$tens]} " : null);
			}
			else{
				$tens = (int)($tens / 10);
				$tens = " {$list2[$tens]} ";
				$singles = (int) ($num_levels[$i] % 10);
				$singles = " {$list1[$singles]} ";
			}

			$words[] = $hundreds . $tens . $singles . (($levels && (int) ($num_levels[$i])) ? " {$list3[$levels]} " : null);
		}

		$commas = count($words);
		if($commas > 1)$commas = $commas - 1;

		return implode(" ", $words);
	}

	public function RandomString($Length = 8, $Number = true, $Lowercase = true, $Uppercase = true, $Symbol = true, $TagSafe = true){
		if($Number)$Character[] = "0123456789";
		if($Lowercase)$Character[] = "abcdefghijklmnopqrstuvwxyz";
		if($Uppercase)$Character[] = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		if($Symbol)$Character[] = "`-=~!@#\$%^&*()_+[]\\{}|;':\",./?";
		if($Symbol && !$TagSafe)$Character[] = "<>";

		if(!isset($Character)){
			var_dump("function " . __FUNCTION__ . "()- At least one scope is required to generate a random string.");
			$RandomString = false;
		}
		else{
			$Character = implode(null, $Character);
		    $CharacterLength = strlen($Character);

		    for($CharacterCount = 0; $CharacterCount < $Length; $CharacterCount++)$RandomString[] = $Character[rand(0, $CharacterLength - 1)];

		    $RandomString = implode(null, $RandomString);
		}

		return $RandomString;
	}

	public function IsALPHABETIC($Value){
		return preg_match("/^[a-zA-Z ]*$/", $Value);
	}

	public function IsEMAIL($Value){
		return filter_var($Value, FILTER_VALIDATE_EMAIL);
	}

	public function IsNUMBER($Value){
		return is_numeric($Value);
	}

	public function IsNUMERIC($Value){
		return is_numeric($Value);
	}

	public function IsURL($Value){
		return preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $Value);
	}

	public function IsINTEGER($Value){
		return $this->IsNUMBER($Value) && preg_match("/[0-9]/i", $Value);
	}

	public function IsPOSITIVE($Value){
		return $this->IsNUMBER($Value) && $Value > 0;
	}

	public function IsFLOAT($Value){
		return $this->IsNUMBER($Value);
	}

	public function IsNONNEGATIVE($Value){
		return $this->IsNUMBER($Value) && $Value >= 0;
	}

	// Distance unit = Meter
	public function HaversineGreatCircleDistance($FromLatitude, $FromLongitude, $ToLatitude, $ToLongitude, $EarthRadius = 6371000){
		$FromLatitude = deg2rad($FromLatitude);
		$FromLongitude = deg2rad($FromLongitude);
		$ToLatitude = deg2rad($ToLatitude);
		$ToLongitude = deg2rad($ToLongitude);

		return 2 * asin(sqrt(pow(sin(($ToLatitude - $FromLatitude) / 2), 2) + cos($FromLatitude) * cos($ToLatitude) * pow(sin(($ToLongitude - $FromLongitude) / 2), 2))) * $EarthRadius;
	}

	public function SecondToTime($Second = 0){
		$Second = intval($Second);

		$Hour = str_pad(floor($Second / 60 / 60), 2, "0", STR_PAD_LEFT);
		$Minute = str_pad(floor(($Second - ($Hour * 60 * 60)) / 60), 2, "0", STR_PAD_LEFT);
		$Second = str_pad($Second - ($Hour * 60 * 60) - ($Minute * 60), 2, "0", STR_PAD_LEFT);

		return "{$Hour}:{$Minute}:{$Second}";
	}

	// Calculate angle/segment of direction for two points, coordinates; from one to another
    public function PointAngle(
		$X2, // X to
		$Y2, // Y to
		$X1 = null, // X from, defaults to 0
		$Y1 = null, // Y from, defaults to 0
		$Segment = null, // Convert angle into number of segment of angle amount
		$FullCircle = true // Use 360 degree range instead of negative angle value
	){
		if(is_null($X1))$X1 = 0;
		if(is_null($Y1))$Y1 = 0;

		$Angle = rad2deg(atan2($Y2 - $Y1, $X2 - $X1));
		if($Angle < 0 && $FullCircle)$Angle = $Angle + 360;

		$Result = $Segment ? round($Angle / $Segment, 0) : $Angle;

		return $Result;
	}

	public function ReplaceByKey($Content, $Data, $BeginEnclosure = "%", $EndEnclosure = "%"){
		foreach(array_keys($Data) as $Key)$Field[] = "{$BeginEnclosure}{$Key}{$EndEnclosure}";

		return str_replace($Field, $Data, $Content);
	}

	function CreatePath($Path){
		if(!is_dir($Path))mkdir($Path, 0777, true);

		return true;
	}
    #endregion Method

    #region Property
    public function Debug($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$Result = true;
        }

        return $Result;
    }

    public function Graphic($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$Result = true;
        }

        return $Result;
    }
    #endregion Property

	#region Function
	private function MoveUploadedItem($Path, $File, $TemporaryFile, $MustRename, $AllowedExtension, $ForbiddenExtension){
		$Result = false;
		$File = pathinfo($File);

		if(
				$File["basename"]
			&&	(!isset($AllowedExtension) || in_array($File["extension"], array_filter(explode(",", str_replace(" ", null, $AllowedExtension)))))
			&&	(!isset($ForbiddenExtension) || !in_array($File["extension"], array_filter(explode(",", str_replace(" ", null, $ForbiddenExtension)))))
		){
			if($MustRename || file_exists("{$Path}" . ($FileName = "{$File["basename"]}") . ""))while(file_exists("{$Path}" . ($FileName = "" . ($MustRename ? null : "{$File["filename"]}") . "{$this->GUID()}{$this->GUID()}" . ($File["extension"] ? ".{$File["extension"]}" : null) . "") . ""));
			$Result = move_uploaded_file($TemporaryFile, "{$Path}{$FileName}") ? $FileName : false;
		}
		else{
			$Result = false;
		}
//var_dump($Result);
		return $Result;
	}
	#endregion Function
}

class Graphic{
	#region Property variable
    private $Property = [
        //"Debug"				=>	null,
    ];
    #endregion Property variable

	#region Private variable
	//private $MaxMindGeoIP2Reader = false;
	#region Private variable

    #region Method
    public function __construct($Debug = null){
        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

        return true;
    }

	public function Resample($PictureFile, $MaximumWidth = null, $MaximumHeight = null, $SavePath = null, $Percent = null, $Width = null, $Height = null, $Type = null){
		if(is_null($Width))$Width = 0;
		if(is_null($Height))$Height = 0;
		if(is_null($MaximumWidth))$MaximumWidth = 0;
		if(is_null($MaximumHeight))$MaximumHeight = 0;
		if(is_null($Percent))$Percent = 100;
		if(is_null($Type))$Type = IMAGE_TYPE_PNG;

		$Result = false;

		if(file_exists($PictureFile)){
			$PictureFileInformation = pathinfo($PictureFile);
			if(is_null($SavePath))$SavePath = "{$PictureFileInformation["dirname"]}/";

			$PictureInformation = getimagesize($PictureFile);

			if($PictureInformation[2] == IMAGETYPE_JPEG || $PictureInformation[2] == IMAGETYPE_JPEG2000){
				$PictureHandle = imagecreatefromjpeg($PictureFile);
			}
			elseif($PictureInformation[2] == IMAGETYPE_PNG){
				$PictureHandle = imagecreatefrompng($PictureFile);
				if($PictureHandle)imagealphablending($PictureHandle, true);
			}
			elseif($PictureInformation[2] == IMAGETYPE_GIF){
				$PictureHandle = imagecreatefromgif($PictureFile);
			}

			if(!$PictureHandle){ // Error loading picture
var_dump("Irrecoverable error with '{$PictureFile}' at " . __FILE__ . ":" . __LINE__);
			}
			else{
				if($MaximumWidth > 0 && $MaximumHeight > 0){
					if($PictureInformation[0] > $PictureInformation[1]){
						$Width = $MaximumWidth;
						$Height = $PictureInformation[1] * $MaximumWidth / $PictureInformation[0];
					}else{
						$Width = $PictureInformation[0] * $MaximumHeight / $PictureInformation[1];
						$Height = $MaximumHeight;
					}
				}
				else{
					if($Percent != 100){
						$Width = $PictureInformation[0] * $Percent / 100;
						$Height = $PictureInformation[1] * $Percent / 100;
					}
					else{
						$Width = $PictureInformation[0];
						$Height = $PictureInformation[1];
					}
				}

				$ResampleHandle = imagecreatetruecolor($Width, $Height);

				imagealphablending($ResampleHandle, false);
				imagesavealpha($ResampleHandle, true);
				imagecopyresampled($ResampleHandle, $PictureHandle, 0, 0, 0, 0, $Width, $Height, $PictureInformation[0], $PictureInformation[1]);
				imagedestroy($PictureHandle);

				$ResampleFileName = "{$SavePath}{$PictureFileInformation["filename"]}";

				if($Type == IMAGE_TYPE_JPEG || $Type == IMAGE_TYPE_JPEG2000){
					$ResampleFile = UniqueFileName("{$ResampleFileName}.jpg");
					imagejpeg($ResampleHandle, $ResampleFile, 100);
				}elseif($Type == IMAGE_TYPE_PNG){
					$ResampleFile = UniqueFileName("{$ResampleFileName}.png");
					imagepng($ResampleHandle, $ResampleFile, 0);
				}elseif($Type == IMAGE_TYPE_GIF){
					$ResampleFile = UniqueFileName("{$ResampleFileName}.gif");
					imagegif($ResampleHandle, $ResampleFile);
				}

				imagedestroy($ResampleHandle);

				$Result = basename($ResampleFile);
//var_dump($PictureFileInformation, $PictureInformation, $PictureHandle, $Width, $Height, $ResampleFile, basename($ResampleFile)); exit;
			}
		}
		else{ // Picture file not found
var_dump("Picture file '{$PictureFile}' not found at " . __FILE__ . ":" . __LINE__);
		}

		return $Result;
	}
    #endregion Method

    #region Property
    public function Debug($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$Result = true;
        }

        return $Result;
    }
    #endregion Property

	#region Function
	private function MoveUploadedItem($Path, $File, $TemporaryFile, $MustRename, $AllowedExtension, $ForbiddenExtension){
		$Result = false;

		return $Result;
	}
	#endregion Function
}

class Debug{
	#region Property variable
    private $Property = [
        "Enabled"				=>	false,
        "BasePath"				=>	null,
        "AlertTime"				=>	0,
        "Checkpoint"			=>	[],
        "CheckpointHTML"		=>	null,
        "DumpHTML"				=>	null,
        "BackTraceHTML"			=>	null,
		"BeginTime"				=>	null,
		"EndTime"				=>	null,
    ];
    #endregion Property variable

	#region Private variable
	private static $AlreadyInstantiated = false;
	private $Utility = null;
	#region Private variable

    #region Method
    public function __construct($Enabled = null){
		// Check if already instantiated and throw error if tried again
		if($this::$AlreadyInstantiated)trigger_error("Instantiation is prohibited for " . __CLASS__ .  " object.");
		$this::$AlreadyInstantiated = true;

		$this->Utility = new Utility;
		$this->Property["BeginTime"] = microtime(true);

        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

        return true;
    }

    public function StartCheckpoint($Name, $Description = null, $AlertTime = 0){
		$Result = false;

		if($this->Property["Enabled"]){
			$LastScriptTrace = debug_backtrace(null, 1)[0];
			$BasePathLength = strlen($this->Property["BasePath"]) - 1;

			$this->Property["Checkpoint"][$ID = GUID()] = [
				"Name"	        =>	$Name,
				//"Begin"			=>	round(microtime(true) / 1000, 3, PHP_ROUND_HALF_UP), // Convert micro (10^6) to mili (10^3)
				"Begin"			=>	microtime(true),
				"Description"	=>	$Description,
				"BeginScript"	=>	isset($LastScriptTrace["file"]) ? (substr($LastScriptTrace["file"], 0, $BasePathLength) == substr($this->Property["BasePath"], 0, $BasePathLength) ? substr($LastScriptTrace["file"], $BasePathLength + 1) : $LastScriptTrace["file"]) : null,
				"BeginLine"		=>	isset($LastScriptTrace["line"]) ? $LastScriptTrace["line"] : null,
				"AlertTime"		=>	$AlertTime,
			];

			$Result = $ID;
		}

		return $Result;
	}

    public function StopCheckpoint($ID){
		$Result = false;

		if($ID !== false && $this->Property["Enabled"]){
			$LastScriptTrace = debug_backtrace(null, 1)[0];
			$BasePathLength = strlen($this->Property["BasePath"]) - 1;

			$this->Property["Checkpoint"][$ID] = array_merge($this->Property["Checkpoint"][$ID], [
				//"End"			=>	$EndTime = round(microtime(true) / 1000, 3, PHP_ROUND_HALF_UP), // Convert micro (10^6) to mili (10^3)
				"End"			=>	$EndTime = microtime(true),
				"Time"			=>	$EndTime - $this->Property["Checkpoint"][$ID]["Begin"],
				"EndScript"		=>	isset($LastScriptTrace["file"]) ? (substr($LastScriptTrace["file"], 0, $BasePathLength) == substr($this->Property["BasePath"], 0, $BasePathLength) ? substr($LastScriptTrace["file"], $BasePathLength + 1) : $LastScriptTrace["file"]) : null,
				"EndLine"		=>	isset($LastScriptTrace["line"]) ? $LastScriptTrace["line"] : null,
			]);

			$Result = true;
		}

		return $Result;
	}

	public function StartRegion($Show = false, $Name = null){
		$Result = false;

		if($this->Property["Enabled"]){
			$HTML[] = "<div class=\"DebugRegion\">";
			if($Name)$HTML[] = "<div class=\"Title\" onclick=\"StopEventFlow(this); ToggleVisibilityByElementID('" . ($DebugRegionContentID = "DebugRegionContent_" . $this->Utility->GUID() . "") . "');\">{$Name}</div><div id=\"{$DebugRegionContentID}\" class=\"Content\">";

			$Result = implode(null, $HTML);

			if($Show)print $Result;
		}

		return $Result;
	}

	public function StopRegion($Show = false){
		$Result = false;

		if($this->Property["Enabled"]){
			$HTML[] = "</div></div>";
			if($Show)print implode(null, $HTML);

			$Result = true;
		}

		return $Result;
	}

	// Shouldn't we retire this function in favor of the newer Dump function?
	public function DumpHTML(){
		$Result = false;
//var_dump(func_get_args());
		foreach(func_get_args() as $Key=>$Argument)$HTML[] = "<tr><td>{$Key}</td><td>" . (is_array($Argument) ? "ARRAY" : $Argument) . "</td></tr>";

		$Result = "
			<div class=\"Debug\">
				<table class=\"List\">
					<thead>
						<tr>
							<th>#</th>
							<th>Value</th>
						</tr>
					</thead>

					<tbody>" . implode(null, $HTML) . "</tbody>
				</table>
			</div>
		";

		return $Result;
	}

	// Dump function can be used to generate the same output?!
	public function BackTraceHTML(){
		$Result = false;

		if($this->Property["Enabled"]){
			$BackTraceCount = count($BackTrace = debug_backtrace());

			foreach($BackTrace as $Key=>$Trace){
				$HTML[] = "
						<tr>
							<td>" . ($BackTraceCount - $Key) . "</td>
							<td>" . (isset($Trace["file"]) ? $Trace["file"] : null) . "</td>
							<td class=\"Center\">" . (isset($Trace["line"]) ? $Trace["line"] : null) . "</td>
							<td>" . (isset($Trace["class"]) ? $Trace["class"] : null) . "</td>
							<!--<td>" . (isset($Trace["object"]) ? "Object" : null) . "</td>-->
							<td class=\"Center\">" . (isset($Trace["type"]) ? $Trace["type"] : null) . "</td>
							<td>" . (isset($Trace["function"]) ? $Trace["function"] : null) . "</td>
							<!--<td>" . (isset($Trace["arg"]) ? "Argument" : null) . "</td>-->
						</tr>
				";
			}

			$Result = $this->Property[__FUNCTION__] = "
				<div class=\"Debug\">
					<table class=\"List\">
						<thead>
							<tr class=\"Title\"><th colspan=\"8\">Debug: Back trace</th></tr>

							<tr>
								<th>#</th>
								<th>File</th>
								<th>Line</th>
								<th>Class</th>
								<!--<th>Object</th>-->
								<th>Type</th>
								<th>Function</th>
								<!--<th>Argument</th>-->
							</tr>
						</thead>

						<tbody>" . implode(null, $HTML) . "</tbody>
					</table>
				</div>
			";
		}

		return $Result;
	}

	public function Dump($Value, $Name = null, $Output = true, $CallerDepth = 0, $NestingLevel = 0){
		if(is_array($Value)){
			$Type = "Array<div class=\"Information\">" . count($Value) . "</div>";
			foreach($Value as $Key => $ThisValue)$Item[] = $this->Dump($ThisValue, $Key, false, $CallerDepth, $NestingLevel + 1);
			$ToggleID = $this->Utility->GUID();

			$Value = isset($Item) ? "
				<input type=\"checkbox\" id=\"Toggle_{$ToggleID}\" checked class=\"Toggle\">
				<label for=\"Toggle_{$ToggleID}\" class=\"ToggleNotch\"></label>
				<div class=\"Array\">" . implode(null, $Item) . "</div>
			" : "<div class=\"Empty\">EMPTY</div>";
		}
		elseif(is_object($Value)){
			$Type = "Object<div class=\"Information\">" . get_class($Value) . "</div>";
			$Array = (array)$Value;

			if(count($Array)){
				$FirstArrayKey = array_keys($Array)[0];
				if(substr(trim($FirstArrayKey), 0, 5) == "sPHP\\" && count($Array) == 1)$Array = $Array[$FirstArrayKey];
				foreach($Array as $Key => $ThisValue)$Item[] = $this->Dump($ThisValue, $Key, false, $CallerDepth, $NestingLevel + 1);
				$ToggleID = $this->Utility->GUID();

				$Value = isset($Item) ? "
					<input type=\"checkbox\" id=\"Toggle_{$ToggleID}\" checked class=\"Toggle\">
					<label for=\"Toggle_{$ToggleID}\" class=\"ToggleNotch\"></label>
					<div class=\"Object\">" . implode(null, $Item) . "</div>
				" : "<div class=\"Empty\">EMPTY</div>";
			}
			else{
				$Value = "<div class=\"Empty\">EMPTY</div>";
			}
		}
		else{
			if(is_bool($Value)){
				$Type = "Boolean";
				$Value = "<div class=\"Boolean\">" . ($Value ? "TRUE" : "FALSE") . "</div>";
			}
			elseif(is_numeric($Value)){
				if(is_int($Value)){
					$Type = "Integer";
				}
				elseif(is_float($Value)){
					$Type = "Float";
				}
				else{
					$Type = "Numeric";
				}
			}
			elseif(is_string($Value)){
				$Type = "String<div class=\"Information\">" . strlen($Value) . "</div>";
			}
			else{
				$Type = "" . gettype($Value) . "";
			}
		}

		if(is_null($Value))$Value = "<div class=\"NULL\">NULL</div>";

		$Result = "
				<div class=\"Item\">
					" . (strlen($Name) ? "<div class=\"Name\">{$Name}</div>" : null) . "
					<div class=\"Type\">{$Type}</div>
					<div class=\"Value\">{$Value}</div>
				</div>
		";

		if($NestingLevel == 0){
			$BackTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $CallerDepth + 1)[$CallerDepth];
			$Source = "<div class=\"Source\"><span class=\"Icon\">⚑</span>" . substr($BackTrace["file"], strlen($this->Property["BasePath"]) - 1) . " : {$BackTrace["line"]} (" . date("H:i:s") . ")</div>";
			$Result = "<div class=\"DebugDump\">{$Source}{$Result}</div>";
		}
		else{
			$Source = null;
		}

		if($Output)print $Result;
		return $Result;
	}
    #endregion Method

    #region Property
    public function Enabled($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$Result = true;
        }

        return $Result;
    }

    public function BasePath($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$Result = true;
        }

        return $Result;
    }

    public function AlertTime($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$Result = true;
        }

        return $Result;
    }

    public function Checkpoint($ID = null){
		$Result = is_null($ID) ? $this->Property[__FUNCTION__] : $this->Property[__FUNCTION__][$ID];

        return $Result;
    }

    public function CheckpointCount(){
		$Result = count($this->Property[__FUNCTION__]);

        return $Result;
    }

	public function CheckpointHTML($Show = false, $TimeDecimalPlaces = 5){
		$Result = false;

		if(is_null($this->Property[__FUNCTION__]) && count($this->Property["Checkpoint"])){
			$CheckpointCounter = count($this->Property["Checkpoint"]) + 1;
			$CheckpointTime = 0;

			foreach(array_reverse($this->Property["Checkpoint"]) as $Key => $Checkpoint)if(isset($Checkpoint["End"])){
				$CheckpointCounter--;

				//$CheckpointTime = $CheckpointTime + ($Checkpoint["End"] - $Checkpoint["Begin"]);
				$CheckpointTime = $CheckpointTime + ($Checkpoint["Time"]);

				$CheckpointHTML[] = "
					<tr>
						<td class=\"Numeric\">{$CheckpointCounter}</td>
						<td>{$Checkpoint["Name"]}</td>
						<td class=\"Numeric" . (($AlertTime = $Checkpoint["AlertTime"] ? $Checkpoint["AlertTime"] : $this->Property["AlertTime"]) && $Checkpoint["Time"] >= $AlertTime ? " Alert" : null) . "\">" . number_format($Checkpoint["Time"], $TimeDecimalPlaces) . "</td>
						<td>{$Checkpoint["BeginScript"]}</td>
						<td class=\"Center\">{$Checkpoint["BeginLine"]}</td>
						<td>{$Checkpoint["EndScript"]}</td>
						<td class=\"Center\">{$Checkpoint["EndLine"]}</td>
						<td>{$Checkpoint["Description"]}</td>
					</tr>
				";
			}

			$ExecutionTime = ($this->Property["EndTime"] = microtime(true)) - $this->Property["BeginTime"];

			$this->Property[__FUNCTION__] = "
				<div class=\"Debug DebugCheckpoint\">
					<table class=\"List\">
						<thead>
							<tr class=\"Title\"><th colspan=\"10\">Debug: Checkpoint</th></tr>

							<tr>
								<th class=\"Numeric\">#</th>
								<th>Name</th>
								<th>Time</th>
								<th colspan=\"2\">In</th>
								<th colspan=\"2\">Out</th>
								<th>Description</th>
							</tr>
						</thead>

						<tbody>" . (isset($CheckpointHTML) ? implode(null, $CheckpointHTML) : null) . "</tbody>

						<tfoot>
							<tr class=\"Checkpoint\">
								<td colspan=\"2\">Checkpoint</td>
								<td class=\"Numeric" . ($this->Property["AlertTime"] && $CheckpointTime >= $this->Property["AlertTime"] ? " Alert" : null) . "\">" . number_format($CheckpointTime, $TimeDecimalPlaces) . "</td>
								<td colspan=\"5\">ms</td>
							</tr>

							<tr class=\"Execution\">
								<td colspan=\"2\">Execution</td>
								<td class=\"Numeric" . ($this->Property["AlertTime"] && $ExecutionTime >= $this->Property["AlertTime"] ? " Alert" : null) . "\">" . number_format($ExecutionTime, $TimeDecimalPlaces) . "</td>
								<td colspan=\"5\">ms</td>
							</tr>
						</tfoot>
					</table>
				</div>
			";
		}

		$Result = $this->Property[__FUNCTION__];
		if($Show)print $Result;

		return $Result;
	}

	public function BeginTime(){
		$Result = $this->Property[__FUNCTION__];

		return $Result;
	}

	public function EndTime(){
		$Result = $this->Property[__FUNCTION__];

		return $Result;
	}
    #endregion Property

	#region Function
	#endregion Function
}
?>