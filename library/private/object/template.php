<?php
namespace sPHP;

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
		"LastContentLoadedFromCache"	=>	true,
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

	public function Content($Name, $Variable = [], $Process = null, $Lifetime = null, $DefaultContent = null, $CacheName = null, $Debug = null){
        if($Debug)DebugDump([
            "Script" => __FILE__ . " : " . __LINE__, 
            "Caller" => get_class($this) . "->" . __FUNCTION__ . "()", 
            "Argument" => get_defined_vars(), 
        ]);

        if(!isset($this->Cache[$Name])){ // Cache is not loaded into memory
			if($this->Expired($Name, $Lifetime, $CacheName)){ // Cache is expired
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
                $this->Property["LastContentLoadedFromCache"] = false;
            }
            else{
                $this->Property["LastContentLoadedFromCache"] = true;
                //DebugDump("Template->Content: Loaded from cache");
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

			$Result = implode("", isset($PreContent) ? $PreContent : []) . $RestContent; // Store content in memory (per request)
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

	public function TimeToExpire($Name, $Lifetime = null, $CacheName = null){
		$this->SetName($Name, $CacheName);
		if(is_null($Lifetime))$Lifetime = $this->Property["Lifetime"];

		$Result = (
				!file_exists("{$this->Property["CacheFilePath"]}")
			||	(time() - filemtime($this->Property["CacheFilePath"])) > $Lifetime
		) ? 0 : ($Lifetime - time() + filemtime($this->Property["CacheFilePath"]));

		return $Result;
	}

	public function Expired($Name, $Lifetime = null, $CacheName = null){
        /*
        DebugDump([
            "SOURCE" => [
                "Script" => __FILE__, 
                "Line" => __LINE__, 
                "Function" => __FUNCTION__, 
            ], 
            "Name" => $Name, 
            "Lifetime" => $Lifetime, 
            "CacheName" => $CacheName, 
        ]);
        */
		$Result = $this->TimeToExpire($Name, $Lifetime, $CacheName) == 0 ? true : false;

		return $Result;
    }
    
    public function LastContentLoadedFromCache(){
		$Result = $this->Property[__FUNCTION__];

        return $Result;
    }
    #endregion Property

	#region Function
    private function SetName($Value, $CacheName = null){
        $Result = true;
        
        $CacheNameModifier = $CacheName ? "_{$this->Property["Application"]->Terminal()->Environment()->Utility()->ValidFileName($CacheName)}" : null;

		$this->Property["FileName"] = "{$this->Property["Application"]->Terminal()->Environment()->Utility()->ValidFileName($Value)}";

		$this->Property["ViewFile"] = "{$this->Property["FileName"]}.php";
		$this->Property["ViewFilePath"] = "{$this->Property["ViewPath"]}{$this->Property["ViewFile"]}";

		$CacheFilenamePrefix = "{$this->Property["FileName"]}{$CacheNameModifier}_UserID_{$this->Property["Application"]->Session()->User()->ID()}";

		$this->Property["CacheFile"] = "{$CacheFilenamePrefix}_" . md5($CacheFilenamePrefix) . ".tpc";
		$this->Property["CacheFilePath"] = "{$this->Property["CachePath"]}{$this->Property["CacheFile"]}";

        return $Result;
    }
	#endregion Function
}
?>