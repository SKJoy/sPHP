<?php
/*
    Name:           Message
    Purpose:        Message object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  March 2, 2020 04:09 PM
*/

namespace sPHP\Firebase\Cloud;

class Message{
    #region Property variable
    private $Property = [
		"ServerKey"			=>	null,
        "Content"			=>	null,
        "Title"				=>	null,
        "DeviceKey"			=>	null,
    ];
    #endregion Property variable

	#region Private property
	private $cURL = null;
	#endregion Private property

    #region Method
    public function __construct($ServerKey = null, $Content = null, $Title = null, $DeviceKey = null){
		// Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){
		$this->Disconnect();

        return true;
    }

	public function Connect($ServerKey = null){
		$ServerKey = is_null($ServerKey) ? $this->Property["ServerKey"] : $ServerKey;
		$APIURL = "https://fcm.googleapis.com/fcm/send";

		$this->cURL = curl_init();
		curl_setopt($this->cURL, CURLOPT_URL, $APIURL);
		curl_setopt($this->cURL, CURLOPT_POST, true);
		curl_setopt($this->cURL, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($this->cURL, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, true);

		curl_setopt($this->cURL, CURLOPT_HTTPHEADER, $Header = [
			"Content-Type:application/json",
			"Authorization:Bearer {$ServerKey}",
			"Accept:application/json",
		]);

		return $this->cURL ? true : false;
	}

	public function Disconnect(){
		if($this->cURL)curl_close($this->cURL);

		return true;
	}

	public function DEPRECATED_Send($Content = null, $Title = null, $DeviceKey = null, $ServerKey = null){
		$ProcessTimeStart = microtime(true);

		$Content = is_null($Content) ? $this->Property["Recipient"] : $Content;
		$Title = is_null($Title) ? $this->Property["Recipient"] : $Title;

		$DeviceKey = is_null($DeviceKey) ? $this->Property["DeviceKey"] : $DeviceKey;
		$DeviceKey = is_array($DeviceKey) ? $DeviceKey : [$DeviceKey];

		$ServerKey = is_null($ServerKey) ? $this->Property["ServerKey"] : $ServerKey;

		$APIURL = "https://fcm.googleapis.com/fcm/send";
		$APITimeStart = microtime(true);

		$cURL = curl_init();
		curl_setopt($cURL, CURLOPT_URL, $APIURL);
		curl_setopt($cURL, CURLOPT_POST, true);
		curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);

		curl_setopt($cURL, CURLOPT_HTTPHEADER, $Header = [
			"Content-Type:application/json",
			"Authorization:Bearer {$ServerKey}",
			"Accept:application/json",
		]);

		curl_setopt($cURL, CURLOPT_POSTFIELDS, $JSONRequest = json_encode([
			"registration_ids" => $DeviceKey,
			"notification" => [
				"body" => $Content,
				"title" => $Title,
			],
			"data" => [
				"Key" => "Value",
			],
		]));

		$cURLResponse = curl_exec($cURL);
		curl_close($cURL);
		$APITimeStop = microtime(true);

		$JSONResponse = json_decode($cURLResponse);

		$Result["Error"] = ["Code" => 0, "Message" => null, ];
		$Result["Response"] = json_decode("{\"multicast_id\": null, \"success\": 0, \"failure\": 0, \"canonical_ids\": 0, \"results\": []}");
		$Result["DeviceKey"] = ["Valid" => [], "Invalid" => [], ];
		$Result["ServerKey"] = $ServerKey;
		$Result["API"] = ["URL" => $APIURL, "Time" => ["Start" => date("Y-m-d H:i:s", $APITimeStart), "Stop" => date("Y-m-d H:i:s", $APITimeStop), "DurationMS" => $APITimeStop - $APITimeStart], ];

		if($JSONResponse){
			$Result["Response"] = $JSONResponse;
			foreach($Result["Response"]->results as $ResponseResultKey => $ThisResponseResult)$Result["DeviceKey"][isset($ThisResponseResult->error) ? "Invalid" : "Valid"][] = $DeviceKey[$ResponseResultKey];
		}
		else{
			if(strpos($cURLResponse, "Authentication (Server-) Key contained an invalid or malformed FCM-Token") !== false){
				$ErrorDescription = "Invalid server key";
			}
			else{
				$ErrorDescription = "Invalid response";
			}

			$Result["Error"] = ["Code" => 1, "Message" => $ErrorDescription, ];
		}

		$ProcessTimeStop = microtime(true);
		$Result["Process"] = ["Time" => ["Start" => date("Y-m-d H:i:s", $ProcessTimeStart), "Stop" => date("Y-m-d H:i:s", $ProcessTimeStop), "DurationMS" => $ProcessTimeStop - $ProcessTimeStart], ];

		return $Result;
	}

	public function Send($Content = null, $Title = null, $DeviceKey = null, $ServerKey = null){
		$ProcessTimeStart = microtime(true);

		$Content = is_null($Content) ? $this->Property["Recipient"] : $Content;
		$Title = is_null($Title) ? $this->Property["Recipient"] : $Title;

		$DeviceKey = is_null($DeviceKey) ? $this->Property["DeviceKey"] : $DeviceKey;
		$DeviceKey = is_array($DeviceKey) ? $DeviceKey : [$DeviceKey];

		if(!$this->cURL)$this->Connect($ServerKey);

		curl_setopt($this->cURL, CURLOPT_POSTFIELDS, $JSONRequest = json_encode([
			"registration_ids" => $DeviceKey,
			"notification" => [
				"body" => $Content,
				"title" => $Title,
			],
			"data" => [
				"Key" => "Value",
			],
		]));

		$APITimeStart = microtime(true);
		$cURLResponse = curl_exec($this->cURL);
		$APITimeStop = microtime(true);

		$JSONResponse = json_decode($cURLResponse);

		$Result["Error"] = ["Code" => 0, "Message" => null, ];
		$Result["Response"] = json_decode("{\"multicast_id\": null, \"success\": 0, \"failure\": 0, \"canonical_ids\": 0, \"results\": []}");
		$Result["DeviceKey"] = ["Valid" => [], "Invalid" => [], ];
		$Result["ServerKey"] = $ServerKey;
		$Result["API"] = ["Time" => ["Start" => date("Y-m-d H:i:s", $APITimeStart), "Stop" => date("Y-m-d H:i:s", $APITimeStop), "Duration" => $APITimeStop - $APITimeStart], ];

		if($JSONResponse){
			$Result["Response"] = $JSONResponse;
			foreach($Result["Response"]->results as $ResponseResultKey => $ThisResponseResult)$Result["DeviceKey"][isset($ThisResponseResult->error) ? "Invalid" : "Valid"][] = $DeviceKey[$ResponseResultKey];
		}
		else{
			if(strpos($cURLResponse, "Authentication (Server-) Key contained an invalid or malformed FCM-Token") !== false){
				$ErrorDescription = "Invalid server key";
			}
			else{
				$ErrorDescription = "Invalid response";
			}

			$Result["Error"] = ["Code" => 1, "Message" => $ErrorDescription, ];
		}

		$ProcessTimeStop = microtime(true);
		$Result["Process"] = ["Time" => ["Start" => date("Y-m-d H:i:s", $ProcessTimeStart), "Stop" => date("Y-m-d H:i:s", $ProcessTimeStop), "Duration" => $ProcessTimeStop - $ProcessTimeStart], ];

		return $Result;
	}
    #endregion Method

    #region Property
    public function ServerKey($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Content($Value = null){
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

    public function DeviceKey($Value = null){
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
}
?>