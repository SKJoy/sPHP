<?php
/*
    Name:           cURL
    Purpose:        cURL object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  Sat, 23 May 2021 08:58:25 GMT+06:00
*/

namespace sPHP\Comm;

class cURL{
    private $Property = [
        "URI"				=>	null, // URL/URI to connect to
        "POST"				=>	[], // Array of POST data/field to submit
        "FILE"				=>	[], // Array of FILE field to upload
		"CookiePath"		=>	"./", // Path to the cookie file store
		"CookieFile"		=>	null, // Only the name of the cookie file to read/write, without extension
        "Method"			=>	\sPHP\HTTP_METHOD_GET, // Connection method; HTTP_METHOD_GET | HTTP_METHOD_POST
        "SSLVerifyPeer"		=>	false, // SSL verification for peer connection
        "SSLVerifyHost"		=>	false, // SSL verification for host
		"UserAgent"			=>	"sPHP Comm cURL/1.0", // User agent string
		"Status"			=>	null, // Last operation status
		"Response"			=>	null, // Last response
		"Referer"			=>	null, // Referer URL
        "FollowRedirection"	=>	true, // Automatically forllow HTTP redirection
    ];

    #region Variable
	private $cURL = null;
    #endregion Variable

    #region Method
    public function __construct(?string $URI = null, ?array $POST = null, ?array $FILE = null, ?string $CookiePath = null, $CookieFile = null, ?string $Method = null, ?bool $SSLVerifyPeer = null, ?bool $SSLVerifyHost = null, ?string $UserAgent = null, ?string $Referer = null, ?bool $FollowRedirection = null){
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
	}

    public function Call(?string $URI = null, ?array $POST = null, ?array $FILE = null, ?string $CookiePath = null, $CookieFile = null, ?string $Method = null, ?bool $SSLVerifyPeer = null, ?bool $SSLVerifyHost = null, ?string $UserAgent = null, ?string $Referer = null, ?bool $FollowRedirection = null){
		#region Set default argument values in respect with properties
		foreach(array_filter(explode(",", str_replace(" ", null, "URI, POST, FILE, CookiePath, CookieFile, Method, SSLVerifyPeer, SSLVerifyHost, UserAgent, Referer, FollowRedirection"))) as $Argument)if(is_null($$Argument))$$Argument = $this->Property[$Argument];
		if(!is_array($POST))$POST = []; // In case if this is not array
		foreach($FILE as $Key => $Value)$POST[$Key] = curl_file_create($Value); // Add FILEs to POST data array
		#endregion Set default argument values in respect with properties

		#region Set cookie file
        if($CookieFile === false)$CookieFile = null;
        if($CookieFile === true)$CookieFile = parse_url($URI)["host"]; // URI host as cookie file name when intend to use cookie without a file name
		if($CookieFile)$CookieFile = "{$CookiePath}{$CookieFile}.cck";
        
        if($CookieFile && !is_dir($CookiePath))mkdir($CookiePath, 0777, true); // Ensure the cookie path exists when using cookie
		#endregion Set cookie file

		#region Set cURL options
		curl_setopt($this->cURL, CURLOPT_URL, $URI);
		curl_setopt($this->cURL, CURLOPT_FOLLOWLOCATION, $FollowRedirection);
		curl_setopt($this->cURL, CURLOPT_POST, $Method == \sPHP\HTTP_METHOD_POST || count($POST)); // Either explicitely set or we have POST data
		curl_setopt($this->cURL, CURLOPT_POSTFIELDS, $POST);
		curl_setopt($this->cURL, CURLOPT_COOKIEJAR, $CookieFile);
		curl_setopt($this->cURL, CURLOPT_COOKIEFILE, $CookieFile);
		curl_setopt($this->cURL, CURLOPT_SSL_VERIFYPEER, $SSLVerifyPeer);
		curl_setopt($this->cURL, CURLOPT_SSL_VERIFYHOST, $SSLVerifyHost);
		curl_setopt($this->cURL, CURLOPT_USERAGENT, $UserAgent);
		curl_setopt($this->cURL, CURLOPT_REFERER, $Referer);
		curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, true);
		#endregion Set cURL options

		$Result["Response"] = curl_exec($this->cURL); // Try to get response
		
		if($Result["Response"] === false){ // Call failed
			$Result = false; // Return explicite FALSE
			$this->Property["Response"] = $this->Property["Status"] = null; // Reset last status and response
		}
		else{ // Call succeeded
			$Result["Status"] = curl_getinfo($this->cURL, CURLINFO_HTTP_CODE);

			$this->Property["Response"] = $Result["Response"];
			$this->Property["Status"] = $Result["Status"];
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

    public function CookiePath(?string $Value = null){
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

    public function Response(){
		$Result = $this->Property[__FUNCTION__];

        return $Result;
    }
    #endregion Property
}
?>