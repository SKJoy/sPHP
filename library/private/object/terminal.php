<?php
namespace sPHP;

class Terminal{
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
        "Manifest"          =>  null,
        "IFrameLoad"		=>  IFRAME_LOAD_SAMEORIGIN,
        "IP"		        =>  null,
    ];

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

        $this->Property["DocumentType"] = $Environment->CLI() ? DOCUMENT_TYPE_TXT : DOCUMENT_TYPE_HTML;
        $this->Property["Language"] = new Language();
        $this->Property["IP"] = isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? explode(",", $_SERVER["HTTP_X_FORWARDED_FOR"])[0] : $_SERVER["REMOTE_ADDR"]; // https://gtranslate.io/forum/http-real-http-forwarded-for-t2980.html

        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        #region Generate WebManifest URL
        $WebManifestFile = "manifest.webmanifest"; // WebManifest file name
        $WebManifestURL = "{$this->Property["Environment"]->HTTPSURL()}{$WebManifestFile}"; // Global WebManifest
        if(file_exists("{$this->Property["Environment"]->DomainPath()}" . ($FileToCheck = "{$WebManifestFile}") . ""))$WebManifestURL = "{$this->Property["Environment"]->HTTPSURL()}domain/{$_SERVER["SERVER_NAME"]}/{$FileToCheck}"; // Domain specific WebManifest
        $this->Property["Manifest"] = $WebManifestURL;
        #endregion Generate WebManifest URL

        return true;
    }

    public function __destruct(){
        ob_end_clean(); // Close (erase/empty) output buffer and start sending to standard output

        if(!$this->Property["Environment"]->Utility()->Debug()->Enabled()){ // Compress output only if not in debug mode
            ini_set("zlib.output_handler", ""); // Must be set to empty to allow the compression work
            ini_set("zlib.output_compression", 131072); // Default is 4096 (4 KB)
            ini_set("zlib.output_compression_level", 9); // 0 to 9
        }

        #region Header
        #region sPHP custom header
        header("XX-Powered-By: {$this->Property["Environment"]->Name()}/{$this->Property["Environment"]->Version()->Full()}");
        //header("XX-sPHP-Developer: Binary Men");
        //header("XX-sPHP-Developer-URL: http://sPHP.Info");
        #endregion sPHP custom header
        
		header("Content-Type: " . EXTENSION_MIMETYPE[strtolower($this->Property["DocumentType"])] . (!is_null($this->Property["CharacterSet"]) ? "; charset={$this->Property["CharacterSet"]}" : null));
        header("X-Frame-Options: {$this->Property["IFrameLoad"]}");
		header("Server: UNKNOWN"); // Security warning: Server header should contain server name only; So, detect the server name and put only the name here
        
		#region Remove depricated/vulnerable headers
        header_remove("X-Powered-By");
        header_remove("Server"); // Some servers may enforce retaining/sending this (e.g: LightSpeed)
        header_remove("Pragma");
        header_remove("Expires");
		#endregion Remove depricated/vulnerable headers
        #endregion Header

        if($this->Property["DocumentType"] == DOCUMENT_TYPE_HTML && !isset($_POST["_MainContentOnly"])){ // Full HTML document
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
			//$HTML_Head_Link[] = "<link rel=\"manifest\" href=\"{$this->Property["Environment"]->URL()}webapp_manifest.php\">";
			//$HTML_Head_Link[] = "<link rel=\"manifest\" href=\"{$this->Property["Environment"]->URL()}?_Script=WebApp/Manifest\">";

            foreach($this->HTML_Head_JavaScript as $Item)$HTML_Head_JavaScript[] = "<script" . ($Item["URL"] ? " src=\"{$Item["URL"]}\"" : null) . "></script>";

            #region Generate FavIco URL
            $FavIconFile = "{$this->Property["Icon"]}.ico"; // FavIco file name
            $FavIconURL = "{$this->Property["Environment"]->ImageURL()}{$FavIconFile}"; // Global FavIco
            if(file_exists("{$this->Property["Environment"]->DomainPath()}" . ($FileToCheck = "image/{$FavIconFile}") . ""))$FavIconURL = "{$this->Property["Environment"]->DomainURL()}{$FileToCheck}"; // Domain specific FavIco
            #endregion Generate FavIco URL

            print "<!DOCTYPE html><html lang=\"{$this->Property["Language"]->HTMLCode()}\"><head>
				" . implode(null, $HTML_Head_META) . "
				<title>{$this->Property["Title"]}</title>
				<link rel=\"shortcut icon\" href=\"{$FavIconURL}\">
                " . (isset($HTML_Head_Link) ? implode(null, $HTML_Head_Link) : null) . "
                " . (isset($HTML_Head_JavaScript) ? implode(null, $HTML_Head_JavaScript) : null) . "
            {$this->Property["HTMLHeadCode"]}</head><body id=\"DocumentBody\"" . (isset($_POST["_NoHeader"]) && isset($_POST["_NoFooter"]) ? " class=\"DocumentBodyContentView\"" : null) . ">";
        }

        // Ouput contents
        foreach(array_merge(isset($this->Buffer[OUTPUT_BUFFER_MODE_HEADER]) ? $this->Buffer[OUTPUT_BUFFER_MODE_HEADER] : [], isset($this->Buffer[OUTPUT_BUFFER_MODE_MAIN]) ? $this->Buffer[OUTPUT_BUFFER_MODE_MAIN] : []) as $Content)print $Content;

        // Close HTML document output
        if($this->Property["DocumentType"] == DOCUMENT_TYPE_HTML && !isset($_POST["_MainContentOnly"]))print "{$this->Property["Environment"]->Utility()->Debug()->CheckpointHTML()}</body></html>";

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
    
    public function LetDownload($Content, $DocumentType = null, $FileName = null, $Header = true){
        if(is_array($Content)){ // Create content from Array
            if(!$DocumentType)$DocumentType = DOCUMENT_TYPE_CSV; // Default MIME/content type

            if($DocumentType == DOCUMENT_TYPE_CSV){
                $Content = $this->Property["Environment"]->Utility()->ArrayToCSV($Content, $Header);
            }
            elseif($DocumentType == DOCUMENT_TYPE_JSON){
                $Content = json_encode($Content);
            }
            elseif($DocumentType == DOCUMENT_TYPE_XML){
                $Content = "ARRAY to XML conversion is not implemented!";
            }
            else{
                $Content = serialize($Content);
            }
        }

        $_POST["_NoHeader"] = $_POST["_NoFooter"] = true; // Suppress header and footer

        $this->Clear(); // Discard all previous output
        if($DocumentType)$this->DocumentType($DocumentType); // MIME type for browser/terminal interaction
        if($FileName)$this->DocumentName($FileName); // Set custom filename for browser/terminal download

        print $Content; // Output content

        $this->Suspended(true); // Suppress any further output
        $this->Property["Environment"]->Log()->Put("" . \sPHP::$User->Name() . " (" . \sPHP::$User->UserGroupIdentifierHighest() . ")", ["File" => $FileName, "Type" => $DocumentType, "Byte" => strlen($Content), ], null, LOG_TYPE_ALERT, "File download", "User", "Application");

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

    public function IFrameLoad($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$Result = true;
        }

        return $Result;
    }

    public function IP($Value = null){
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
    private function Send($Content){ // This function is called with each PRINT/ECHO by the PHP's built in output handler
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
?>