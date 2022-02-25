<?php
namespace sPHP;

class Application{
    private $Property = [ // Property value store
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
        "UserDeviceNotification"    =>  false, 
        "Log"                   =>  null, 
    ];

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

		#region Initialize dynamic properties
		$this->Property["DocumentType"] = $Terminal->DocumentType(); // Default document type from Terminal
		$this->Property["Database"] = new Database();
		$this->Property["Log"] = $Terminal->Environment()->Log(); // Inherit global Log
		$this->Property["EncryptionKey"] = $_SERVER["SERVER_NAME"];
		$this->Property["OpenGraph"] = new OpenGraph(null, null, null, null, null, time() - (24 * 60 * 60));
		#endregion Initialize dynamic properties

		#region Set up log database
		$this->Property["Log"]->Database($this->Property["Database"]);
		$this->Property["Log"]->DatabaseTable("sphp_log");
		#endregion Set up log database

        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        if(!file_exists("{$this->Property["Terminal"]->Environment()->Path()}system/pre.php")){ //? Create application structure if required
            $ZIP = new \ZipArchive;

            if($ZIP->open("{$this->Property["Terminal"]->Environment()->SystemPath()}resource/application-structure.zip") === TRUE){
                $ZIP->extractTo($this->Property["Terminal"]->Environment()->Path()); //* Extract resources to application path
                $ZIP->close();
            }
            else{ //! Failed extracting application structure
                die("Failed to create application structure!");
            }
        }

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
		$this->Property["Terminal"]->ThemeColor($Configuration["ThemeColor"]);

		// Loop for direct properties for configuration items
        foreach($Configuration as $Key => $Value)if(!is_null($Value) && array_key_exists($Key, $this->Property))$this->$Key($Value);

		//if(!$Configuration["CustomError"])restore_error_handler(); // Use PHP's error reporting if wanted
		//if($Configuration["CustomError"])___SetErrorHandler(); // Custom error tuggle is incorporated in the Environment object
		$this->Property["Terminal"]->Environment()->CustomError($Configuration["CustomError"]);

		#region Set indirect properties from configuration items
        $this->Property["Guest"] = new User($Configuration["GuestEmail"], null, $Configuration["GuestName"], $Configuration["CompanyPhone"], null, null, null, null, 0, "GUEST", null, null, "Guest", "GUEST");
        $this->Property["Administrator"] = new User($Configuration["AdministratorEmail"], $Configuration["AdministratorPasswordHash"], $Configuration["AdministratorName"], $Configuration["CompanyPhone"], null, null, null, null, 1, "ADMINISTRATOR", null, null, "Administrator", "ADMINISTRATOR");
        $this->Property["Company"] = new User($Configuration["CompanyEmail"], null, $Configuration["CompanyName"], $Configuration["CompanyPhone"], $Configuration["CompanyAddress"], $Configuration["CompanyURL"]);
		$this->Property["Version"] = new Version($Configuration["VersionMajor"], $Configuration["VersionMinor"], $Configuration["VersionRevision"]);
		#endregion Set indirect properties from configuration items

        // Other static assignments from the configuration
        $this->Property["Terminal"]->META("author", $Configuration["CompanyName"]);
        $this->Property["Terminal"]->Icon($Configuration["Icon"]);
        $this->Property["Terminal"]->IFrameLoad($Configuration["IFrameLoad"]);

        // Set indirect properties by calling to execute inner operations inside the set method
        $this->Language(new Language($Configuration["LanguageName"], $Configuration["LanguageCode"], $Configuration["LanguageRegionCode"], $Configuration["LanguageNativeName"], $Configuration["LanguageNativelyName"]));

		// Keep assigning the HEAD HTML here, modify it later assuming user can alter in in the script level
        //require __DIR__ . "/tinymce_head_html.php";
        //$this->Property["Terminal"]->HTMLHeadCode("{$Configuration["HTMLHeadCode"]}\n\n{$TinyMCEHTMLHeadCode}");
        $this->Property["Terminal"]->HTMLHeadCode("{$Configuration["HTMLHeadCode"]}");

		#region Environment SMTP configuration
		$this->Property["Terminal"]->Environment()->SMTPHost($Configuration["SMTPHost"]);
		$this->Property["Terminal"]->Environment()->SMTPPort($Configuration["SMTPPort"]);
		$this->Property["Terminal"]->Environment()->SMTPUser($Configuration["SMTPUser"]);
		$this->Property["Terminal"]->Environment()->SMTPPassword($Configuration["SMTPPassword"]);
		#endregion Environment SMTP configuration

        // Assume default script to execute if no script is requested
        // Moved here from below to allow ignoring session activity
        // for specific scripts
		$_POST["_Script"] = strtolower(SetVariable("_Script", $this->Property["DefaultScript"]));

        #region Session configuration
		$this->Property["Session"]->Name($Configuration["SessionName"]);
		$this->Property["Session"]->Lifetime($Configuration["SessionLifetime"]);
        $this->Property["Session"]->Isolate($Configuration["SessionIsolate"]);
        $this->Property["Session"]->Guest($this->Property["Guest"]);
        #endregion Session configuration

        // Ignore session activity for specific scripts as set with Configuration
        // Special care taken in case if this Configuration parameter is not set!
        // Remove the special care in future release once confirmed all applications have it
        foreach(isset($Configuration["SessionIgnoreScript"]) ? $Configuration["SessionIgnoreScript"] : [] as $SessionIgnoreScript){
            if($_POST["_Script"] == strtolower($SessionIgnoreScript)){ // Match found
                $this->Property["Session"]->IgnoreActivity(true);
                break; // Match found, no need to check anymore
            }
        }
		
		if(
				$Configuration["DatabaseType"]
			&&	$Configuration["DatabaseHost"]
			&&	$Configuration["DatabaseUser"]
			&&	$Configuration["DatabaseName"]
		){ // Set up database
			#region Database properties
			$this->Property["Database"]->Type($Configuration["DatabaseType"]);
			$this->Property["Database"]->Host($Configuration["DatabaseHost"]);
			$this->Property["Database"]->User($Configuration["DatabaseUser"]);
			$this->Property["Database"]->Password($Configuration["DatabasePassword"]);
			$this->Property["Database"]->Name($Configuration["DatabaseName"]);
			$this->Property["Database"]->ODBCDriver($Configuration["DatabaseODBCDriver"]);
			$this->Property["Database"]->TablePrefix($Configuration["DatabaseTablePrefix"]);
			$this->Property["Database"]->Timezone($Configuration["DatabaseTimezone"]);
			$this->Property["Database"]->Encoding($Configuration["CharacterSet"]);
			$this->Property["Database"]->Strict($Configuration["DatabaseStrictMode"]);
			$this->Property["Database"]->ErrorLogPath("{$this->Property["Terminal"]->Environment()->LogPath()}error/");
			$this->Property["Database"]->IgnoreQueryError($Configuration["DatabaseIgnoreQueryError"]);
			#endregion Database properties

			$this->Property["Database"]->Connect();

			#region Framework generic tables
			$Configuration["DatabaseTable"]["" . ($Entity = "Language") . ""] = new Database\Table("{$Entity}");
			$Configuration["DatabaseTable"]["" . ($Entity = "Country") . ""] = new Database\Table("{$Entity}");
			$Configuration["DatabaseTable"]["" . ($Entity = "Gender") . ""] = new Database\Table("{$Entity}");
			$Configuration["DatabaseTable"]["" . ($Entity = "User") . ""] = new Database\Table("{$Entity}");
			$Configuration["DatabaseTable"]["" . ($Entity = "User") . "Group"] = new Database\Table("{$Entity} group");
			$Configuration["DatabaseTable"]["" . ($Entity = "User") . "UserGroup"] = new Database\Table("{$Entity} user group");
			$Configuration["DatabaseTable"]["" . ($Entity = "Log") . ""] = new Database\Table("{$Entity}");
			$Configuration["DatabaseTable"]["" . ($Entity = "Application") . "Traffic"] = new Database\Table("{$Entity} traffic", "ATr");
			$Configuration["DatabaseTable"]["" . ($Entity = "Measure") . "Type"] = new Database\Table("{$Entity} type");
			$Configuration["DatabaseTable"]["" . ($Entity = "Measure") . ""] = new Database\Table("{$Entity}");
			$Configuration["DatabaseTable"]["" . ($Entity = "Dictionary") . "Data"] = new Database\Table("{$Entity} data");
			$Configuration["DatabaseTable"]["" . ($Entity = "Dictionary") . ""] = new Database\Table("{$Entity}");
			$Configuration["DatabaseTable"]["" . ($Entity = "Dictionary") . "Type"] = new Database\Table("{$Entity} type");
			#endregion Framework generic tables

			foreach($Configuration["DatabaseTable"] as $Table){ // Set dynamic & common table properties
				$Table->UploadPath("{$this->Property["Terminal"]->Environment()->UploadPath()}{$Table->Name()}/");
				$Table->SQLSELECTPath("{$this->Property["Terminal"]->Environment()->SQLSELECTPath()}" . strtolower($this->Property["Database"]->Type()) . "/");
				$Table->Database($this->Property["Database"]);
			}
		}
        #endregion Set configuration
        #endregion Load configuration

        $this->Property["Session"]->Start(); // Start the session after configuring accordingly. Log User ID updated with Session->User() property method
        if($this->Property["Terminal"]->Environment()->CLI())$this->Property["Session"]->User($this->Property["Administrator"]); // Set session User to Application Administrator for CLI mode
		
		#region Log request (traffic)
		if($Configuration["DatabaseLogTraffic"] && !is_null($this->Property["Database"]->Connection())){
			$Configuration["DatabaseTable"]["ApplicationTraffic"]->Put([ // Traffic
				"ApplicationTrafficServer"			=>	$_SERVER["SERVER_NAME"],
				"ApplicationTrafficHost"			=>	$_SERVER["HTTP_HOST"],
				"ApplicationTrafficSessionCode"		=>	$this->Property["Session"]->ID(),
				"ApplicationTrafficTime"			=>	date("Y-m-d H:i:s"),
				"ApplicationTrafficIP"				=>	$this->Property["Terminal"]->IP(), //$_SERVER["REMOTE_ADDR"]
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
		#endregion Log request (traffic)

        // Following session configuration needs to be set after the session starts
        $this->Language($this->Property["Session"]->Language()); // Set Language from session

		// Following will always overwrite session's current value in case of TRUE evaluation
        if($Configuration["ContentEditMode"] || in_array(strtoupper($_SERVER["SERVER_NAME"]), array_filter(explode(",", str_replace(" ", "", strtoupper($Configuration["ContentEditModeServer"]))))) || in_array(strtoupper($_SERVER["REMOTE_ADDR"]), array_filter(explode(",", str_replace(" ", "", strtoupper($Configuration["ContentEditModeClient"]))))))$this->Property["Session"]->ContentEditMode(true);
        if($Configuration["DebugMode"] || in_array(strtoupper($_SERVER["SERVER_NAME"]), array_filter(explode(",", str_replace(" ", "", strtoupper($Configuration["DebugModeServer"]))))) || in_array(strtoupper($_SERVER["REMOTE_ADDR"]), array_filter(explode(",", str_replace(" ", "", strtoupper($Configuration["DebugModeClient"]))))))$this->Property["Session"]->DebugMode(true); 

        #region Include stylesheet
        $Configuration["Stylesheet"][] = "{$this->Property["Terminal"]->Environment()->StyleURL()}language/{$this->Property["Language"]->HTMLCode()}.css";
        if(file_exists(($CSSPath = "{$this->Property["Terminal"]->Environment()->StylePath()}") . ($CSSFile = "script/{$_POST["_Script"]}.css") . ""))$Configuration["Stylesheet"][] = "{$this->Property["Terminal"]->Environment()->StyleURL()}{$CSSFile}?TimeUpdated=" . filemtime("{$CSSPath}{$CSSFile}") . "";

        # LEGACY: Domain specific stylesheet
        if(file_exists("{$this->Property["Terminal"]->Environment()->StylePath()}{$_SERVER["SERVER_NAME"]}/loader.css"))$Configuration["Stylesheet"][] = "{$this->Property["Terminal"]->Environment()->StyleURL()}{$_SERVER["SERVER_NAME"]}/loader.css";
        if(file_exists("{$this->Property["Terminal"]->Environment()->StylePath()}{$_SERVER["SERVER_NAME"]}/script/{$_POST["_Script"]}.css"))$Configuration["Stylesheet"][] = "{$this->Property["Terminal"]->Environment()->StyleURL()}{$_SERVER["SERVER_NAME"]}/script/{$_POST["_Script"]}.css";

        # Domain specific stylesheet
        if(file_exists(($CSSPath = "{$this->Property["Terminal"]->Environment()->DomainPath()}") . ($CSSFile = "style/loader.css") . ""))$Configuration["Stylesheet"][] = "{$this->Property["Terminal"]->Environment()->DomainURL()}{$CSSFile}?TimeUpdated=" . filemtime("{$CSSPath}{$CSSFile}") . "";
        if(file_exists(($CSSPath = "{$this->Property["Terminal"]->Environment()->DomainPath()}") . ($CSSFile = "style/script/{$_POST["_Script"]}.css") . ""))$Configuration["Stylesheet"][] = "{$this->Property["Terminal"]->Environment()->DomainURL()}{$CSSFile}?TimeUpdated=" . filemtime("{$CSSPath}{$CSSFile}") . "";

		foreach($Configuration["Stylesheet"] as $URL)$this->Property["Terminal"]->Link("stylesheet", "text/css", $URL);
        #endregion Include stylesheet

        #region Include JavaScript
        if(file_exists(($JavaScriptPath = "{$this->Property["Terminal"]->Environment()->Path()}") . ($JavaScriptFile = "javascript/script/{$_POST["_Script"]}.js") . ""))$Configuration["JavaScript"][] = "{$this->Property["Terminal"]->Environment()->URL()}{$JavaScriptFile}?TimeUpdated=" . filemtime("{$JavaScriptPath}{$JavaScriptFile}") . "";
        if(file_exists(($JavaScriptPath = "{$this->Property["Terminal"]->Environment()->DomainPath()}") . ($JavaScriptFile = "javascript/script/{$_POST["_Script"]}.js") . ""))$Configuration["JavaScript"][] = "{$this->Property["Terminal"]->Environment()->DomainURL()}{$JavaScriptFile}?TimeUpdated=" . filemtime("{$JavaScriptPath}{$JavaScriptFile}") . "";

        foreach($Configuration["JavaScript"] as $URL)$this->Property["Terminal"]->JavaScript($URL); // Load JavaScript in Terminal
        #endregion Include JavaScript

		// Execute the requested script
		___ExecuteApplicationScript($this, new Template($this, $Configuration, $Configuration["TemplateCacheLifetime"], $Configuration["TemplateCacheActionMarker"]), $Configuration);

        // Add to the final HEAD HTML, assuming user has changed it in the script level
        $this->Property["Terminal"]->HTMLHeadCode("{$this->Property["Terminal"]->HTMLHeadCode()}{$this->Property["OpenGraph"]->MetaHTML()}");

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

        #region Update WebManifest
        $WebManifestLogoFile = "image/logo-webapp.png";
        $WebManifestFileName = "manifest.webmanifest";
        $WebManifestFile = file_exists("{$this->Property["Terminal"]->Environment()->DomainPath()}{$WebManifestLogoFile}") ? "{$this->Property["Terminal"]->Environment()->DomainPath()}{$WebManifestFileName}" : "{$this->Property["Terminal"]->Environment()->Path()}{$WebManifestFileName}";

        if(!file_exists($WebManifestFile) || time() - filemtime($WebManifestFile) > 24 * 60 * 60){ // File does not exist or expired
			$WebManifestLogoURL = file_exists("{$this->Property["Terminal"]->Environment()->DomainPath()}{$WebManifestLogoFile}") ? "{$this->Property["Terminal"]->Environment()->HTTPSURL()}domain/{$_SERVER["SERVER_NAME"]}/{$WebManifestLogoFile}" : "{$this->Property["Terminal"]->Environment()->HTTPSURL()}{$WebManifestLogoFile}";

            file_put_contents($WebManifestFile, json_encode([
                "background_color" => $Configuration["BackgroundColor"],
                "description" => $Configuration["Description"],
                "icons" => [
                    //["src" => $WebManifestLogoURL, "type" => "image/png", "sizes" => "72x72", ], 
                    //["src" => $WebManifestLogoURL, "type" => "image/png", "sizes" => "96x96", ], 
                    //["src" => $WebManifestLogoURL, "type" => "image/png", "sizes" => "128x128", ], 
                    //["src" => $WebManifestLogoURL, "type" => "image/png", "sizes" => "256x256", ], 
                    ["src" => $WebManifestLogoURL, "type" => "image/png", "sizes" => "512x512", ], 
                ],
                "lang" => $this->Language()->HTMLCode(), 
                "name" => $this->Name(),
                "short_name" => $Configuration["ShortName"],
                "start_url" => $this->Property["Terminal"]->Environment()->HTTPSURL(),
                "theme_color" => $this->Property["Terminal"]->ThemeColor(), 
                "display" => "standalone",
                "scope" => "/",
            ]));
        }
        #endregion Update WebManifest

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
			foreach(explode(",", str_replace(" ", "", "To, Subject, Message, EventTime, Signature, Type, Source, From")) as $Attribute){
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
					foreach(array_filter(explode(",", str_replace(" ", "", $ThisNotification["To"]))) as $To){
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
			$SQL = "
                LOCK TABLES sphp_notification WRITE; # Try to avoid deadlock on a busy database

                INSERT IGNORE INTO sphp_notification (
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
                ) VALUES " . implode(", ", $SQL_INSERT_VALUE) . ";

                UNLOCK TABLES;
            "; //DebugDump("<pre>{$SQL}</pre>");
            
			$Recordset = $Database->Query($SQL);
		}

		return true;
    }
    
    public function NotifyUserDevice($Message, $UserID = null, $Subject = null, $UserGroupIdentifier = null, $EventTime = null){
        if($this->Property["UserDeviceNotification"]){
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
        }
        else{
            $Result = false;
        }
    
        return $Result;    
    }

    public function OTP($Phone = null, $Email = null, $Length = 5, $ValidityFrom = null, $ValidityTo = null, $UserIDCreated = null, $UserIDFor = null){
        $Error = [];
        if(!$Phone && !$Email)$Error[] = "Either phone or email is required";

        if(count($Error)){
            $Result = ["Error" => $Error, ];
        }
        else{
            $Length = intval($Length);
            if(!$Length)$Length = 5; //DebugDump($Length);

            if(!$ValidityFrom){
                $ValidityFrom = time();
                $ValidityTo = date("Y-m-d H:i:s", $ValidityFrom + (60 * 10)); // N minutes
                $ValidityFrom = date("Y-m-d H:i:s", $ValidityFrom); // Format to string
            }
    
            if(!$ValidityTo)$ValidityTo = date("Y-m-d H:i:s", strtotime($ValidityFrom) + (60 * 10)); // N minutes
    
            if(!$UserIDCreated)$UserIDCreated = $this->Property["Session"]->User()->ID();
            $UserIDCreated = intval($UserIDCreated);

            $UserIDFor = intval($UserIDFor);
    
            $Code = $this->Property["Terminal"]->Environment()->Utility()->RandomString($Length, true, false, false, false);
            $Signature = "{$this->Property["Terminal"]->Environment()->Utility()->GUID()}-{$this->Property["Terminal"]->Environment()->Utility()->GUID()}";
    
            $SQL = "# sPHP: Application: OTP: Create
                DELETE FROM sphp_otp WHERE DATEDIFF(NOW(), OTPValidityTimeTo) > 30; # Clean

                SET @Code := '{$Code}';
                SET @Signature := '{$Signature}';

                INSERT IGNORE INTO sphp_otp (OTPCode, OTPPhone, OTPEmail, OTPSignature, OTPValidityTimeFrom, OTPValidityTimeTo, UserIDCreated, UserIDFor, OTPIsVerified, OTPIsActive, UserIDInserted, TimeInserted) 
                VALUES (@Code, '{$Phone}', '$Email', @Signature, '{$ValidityFrom}', '{$ValidityTo}', {$UserIDCreated}, " . ($UserIDFor ? $UserIDFor : "NULL") . ", 0, 1, {$this->Property["Session"]->User()->ID()}, NOW());

                SELECT * FROM sphp_otp WHERE OTPID = @@IDENTITY; #OTPCode = @Code AND OTPSignature = @Signature;
            "; //DebugDump($SQL);

            $Recordset = $this->Property["Database"]->Query($SQL);
    
            if(count($Recordset)){
                $Result = ["Error" => [], "Response" => ["OTP" => [
                    "ID" => $Recordset[0][0]["OTPID"], 
                    "Code" => $Recordset[0][0]["OTPCode"], 
                    "Signature" => $Recordset[0][0]["OTPSignature"], 
                    "ValidityTime" => [
                        "From" => $Recordset[0][0]["OTPValidityTimeFrom"], 
                        "To" => $Recordset[0][0]["OTPValidityTimeTo"], 
                    ], 
                ], ], ];
            }
            else{
                $Result = ["Error" => ["Databse query failed", ], ];
            }
        }

        return $Result;
    }

    public function VerifyOTP($ID, $Signature, $Code){
        $SQL = "# sPHP: Application: OTP: Verify
            SELECT          OTP.OTPID
            INTO            @OTPID 
            FROM            sphp_otp AS OTP
            WHERE           OTP.OTPID = {$ID} 
                AND         NOW() BETWEEN OTP.OTPValidityTimeFrom AND OTP.OTPValidityTimeTo
                AND         OTP.OTPCode = '{$Code}' 
                AND         OTP.OTPSignature = '{$Signature}'
                AND         OTP.OTPIsVerified = 0
                AND         OTP.OTPIsActive = 1;

            UPDATE          sphp_otp AS OTP 
                SET         OTP.OTPIsVerified = 1
            WHERE           OTPID = @OTPID;

            SELECT @OTPID AS OTPID;
        "; //DebugDump($SQL);

        return $this->Property["Database"]->Query($SQL)[0][0]["OTPID"] ? true : false;
    }
    #endregion Method

    #region Property
    public function Terminal($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $LogoFile = "image/logo.png";
            $LogoURL = file_exists("{$this->Property["Terminal"]->Environment()->DomainPath()}{$LogoFile}") ? "{$this->Property["Terminal"]->Environment()->DomainURL()}{$LogoFile}" : "{$this->Property["Terminal"]->Environment()->ImageURL()}{$LogoFile}";

            $this->Property["OpenGraph"]->URL($this->Property[__FUNCTION__]->Environment()->URL());
            $this->Property["OpenGraph"]->Image($LogoURL);

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

    public function UserDeviceNotification($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Log(){
        $Result = $this->Property[__FUNCTION__];

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
?>