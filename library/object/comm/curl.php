<?php
/*
    Name:           cURL
    Purpose:        cURL object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  Fri, 4 Jun 2021 02:28:00 GMT+06:00
*/

namespace sPHP\Comm;

class cURL{
    private $Property = [
        "URI"				=>	null, // URL/URI to connect to
        "GET"				=>	[], // Array of GET data/field to submit; Can be passed via URI too
        "POST"				=>	[], // Array of POST data/field to submit
        "FILE"				=>	[], // Array of FILE field to upload
        "HTTPHeader"		=>	[], // Array of HTTP header to send as (key value pair)
		"CookiePath"		=>	null, // Cookie file path
		"CookieFile"		=>	null, // Cookie file to read/write, without extension (name only with full path)
        "Method"			=>	\sPHP\HTTP_METHOD_GET, // Connection method; HTTP_METHOD_GET | HTTP_METHOD_POST
        "SSLVerifyPeer"		=>	false, // SSL verification for peer connection
        "SSLVerifyHost"		=>	false, // SSL verification for host
		"UserAgent"			=>	"sPHP Comm cURL/1.0", // User agent string

        #region Read only
		"Status"			=>	["Code" => null, "Name" => null, ], // Last operation status
		"Header"		=>	null, // Last HTTP header received
		"Response"			=>	null, // Last response received
		"Referer"			=>	null, // Referer URL
        "FollowRedirection"	=>	true, // Automatically forllow HTTP redirection
        #endregion Read only
    ];

    #region Variable
	private $cURL = null;
    #endregion Variable

    #region Method
    public function __construct(?string $URI = null, ?array $GET = null, ?array $POST = null, ?array $FILE = null, ?array $HTTPHeader = null, ?string $CookiePath = null, $CookieFile = null, ?string $Method = null, ?bool $SSLVerifyPeer = null, ?bool $SSLVerifyHost = null, ?string $UserAgent = null, ?string $Referer = null, ?bool $FollowRedirection = null){
		#region Default property value for objects
        //$this->Utility = new Utility;
        //$this->Property["From"] = new MailContact();
        //$this->Property["ReplyTo"] = new MailContact();
		#endregion Default property value for objects

		#region Default private property values
		$this->cURL = curl_init();
		#endregion Default private property values

        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){
		$this->Close(); // Close cURL connection

		return true;
    }

	public function Close(){
		curl_close($this->cURL); // Explicitely close the cURL connection
        $this->Property["Status"] = ["Code" => null, "Name" => null, ];
	}

    public function Fetch(?string $URI = null, ?array $GET = null, ?array $POST = null, ?array $FILE = null, ?array $HTTPHeader = null, ?string $CookiePath = null, $CookieFile = null, ?string $Method = null, ?bool $SSLVerifyPeer = null, ?bool $SSLVerifyHost = null, ?string $UserAgent = null, ?string $Referer = null, ?bool $FollowRedirection = null){
		#region Set default argument values in respect with properties
		foreach(array_filter(explode(",", str_replace(" ", "", "URI, GET, POST, FILE, HTTPHeader, CookiePath, CookieFile, Method, SSLVerifyPeer, SSLVerifyHost, UserAgent, Referer, FollowRedirection"))) as $Argument)if(is_null($$Argument))$$Argument = $this->Property[$Argument];
		if(!is_array($GET))$GET = []; // In case if this is not array
		if(!is_array($POST))$POST = []; // In case if this is not array
		foreach($FILE as $Key => $Value)$POST[$Key] = curl_file_create($Value); // Add FILEs to POST data array
		if(!is_array($HTTPHeader))$HTTPHeader = []; // In case if this is not array
		#endregion Set default argument values in respect with properties

        #region Generate key value pairs for GET variables
        $GETKVP = [];
        foreach($GET as $Key => $Value)$GETKVP[] = "{$Key}=" . urlencode($Value);
        if(count($GETKVP))$URI = $URI . "?" . implode("&", $GETKVP); //? Prepare full URL with GET variables
        #endregion Generate key value pairs for GET variables

        #region Generate key value pairs for Header values
        $HTTPHeaderKVP = [];
        foreach($HTTPHeader as $Key => $Value)$HTTPHeaderKVP[] = "{$Key}: " . $Value;
        #endregion Generate key value pairs for Header values

		#region Set cookie file
        if($CookieFile === false)$CookieFile = null;
        if($CookieFile === true)$CookieFile = parse_url($URI)["host"]; // URI host as cookie file name when intend to use cookie without a file name
		if($CookieFile)$CookieFile = "{$CookiePath}/{$CookieFile}.cck";
        if($CookieFile && !is_dir($CookiePath))mkdir($CookiePath, 0777, true); // Ensure the cookie path exists when using cookie
		#endregion Set cookie file

		#region Set cURL options
		curl_setopt($this->cURL, CURLOPT_URL, $URI);
		curl_setopt($this->cURL, CURLOPT_FOLLOWLOCATION, $FollowRedirection);

        if($Method == \sPHP\HTTP_METHOD_POST || count($POST)){ //? Set POST method and variables
            curl_setopt($this->cURL, CURLOPT_POST, true); // Either explicitely set or we have POST data
            curl_setopt($this->cURL, CURLOPT_POSTFIELDS, $POST);
        }

        if($CookieFile){ //? Cookie configuration
            curl_setopt($this->cURL, CURLOPT_COOKIEJAR, $CookieFile);
            curl_setopt($this->cURL, CURLOPT_COOKIEFILE, $CookieFile);
        }

		curl_setopt($this->cURL, CURLOPT_HTTPHEADER, $HTTPHeaderKVP);
		curl_setopt($this->cURL, CURLOPT_SSL_VERIFYPEER, $SSLVerifyPeer);
		curl_setopt($this->cURL, CURLOPT_SSL_VERIFYHOST, $SSLVerifyHost);
		curl_setopt($this->cURL, CURLOPT_USERAGENT, $UserAgent);
		curl_setopt($this->cURL, CURLOPT_REFERER, $Referer);
		curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->cURL, CURLOPT_HEADER, true);
		#endregion Set cURL options

		$Response = curl_exec($this->cURL); // Try to get response
		
		if($Response === false){ //! Call failed
			$this->Property["Status"] = ["Code" => null, "Name" => null, ]; // Reset last status
			$this->Property["Header"] = $this->Property["Response"] = null; // Reset last response

			$Result = false; // Return explicite FALSE
		}
		else{ // Call succeeded
            #region Parse HTTP header
            $HTTPHeaderLength = curl_getinfo($this->cURL, CURLINFO_HEADER_SIZE);

            foreach(array_filter(explode("\n", substr($Response, 0, $HTTPHeaderLength))) as $Item){
                $Item = explode(":", $Item);
                $Result["Header"][$Item[0]] = isset($Item[1]) ? trim($Item[1]) : null;
            }
            #endregion Parse HTTP header
            
            $Result["Response"] = substr($Response, $HTTPHeaderLength);

            #region Decode JSON if applicable
            if(isset($Result["Header"]["Content-Type"]) && strpos($Result["Header"]["Content-Type"], "application/json;") !== false){
                $ResponseJSON = json_decode($Result["Response"]);
                if($ResponseJSON)$Result["Response"] = $ResponseJSON;
            }
            #endregion Decode JSON if applicable

            #region Set property from Result
			$Result["Status"]["Code"] = curl_getinfo($this->cURL, CURLINFO_HTTP_CODE);
            $Result["Status"]["Name"] = isset(\sPHP\HTTP_STATUS_BY_CODE[$Result["Status"]["Code"]]) ? \sPHP\HTTP_STATUS_BY_CODE[$Result["Status"]["Code"]] : "Unknown";

			$this->Property["Status"] = $Result["Status"];
            $this->Property["Header"] = $Result["Header"];
			$this->Property["Response"] = $Result["Response"];
            #endregion Set property from Result
		}

        return $Result;
    }
    #endregion Method

    #region Property
    public function URI(?string $Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function GET(?array $Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function POST(?array $Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function FILE(?array $Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function HTTPHeader(?array $Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function CookiePath($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function CookieFile($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Method(?string $Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function SSLVerifyPeer(?bool $Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function SSLVerifyHost(?bool $Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function UserAgent(?string $Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Referer(?string $Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function FollowRedirection(?bool $Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Status(){
		$Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function Header(){
		$Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function Response(){
		$Result = $this->Property[__FUNCTION__];

        return $Result;
    }
    #endregion Property
}
?>