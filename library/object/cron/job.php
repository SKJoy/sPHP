<?php
/*
    Name:           Job
    Purpose:        Job object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  July 26, 2020 11:32 AM
*/

namespace sPHP\Cron;

class Job{
    #region Property
    private $Property = [
		"Command"			=>	null,
		"Interval"			=>	60 * 60, // Seconds
		"Resource"			=>	[], // Resources passed to job for PHP script; Leave NULL to inherit from Cron object
        "Name"				=>	null, // Job title
        "MaximumExecutionTime"	=>	60 * 60, // Seconds
        "Type"				=>	null, // Type of Cron job
		"Result"			=>	null,
    ];
    #endregion Property

    #region Variable
    //private $Buffer = [];
    #endregion Variable

    #region Method
    public function __construct($Command = null, $Interval = null, $Resource = null, $Name = null, $MaximumExecutionTime = null, $Type = null){
        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

		return true;
    }

	public function Execute(){
		set_time_limit($this->Property["MaximumExecutionTime"]);

		if($this->Property["Type"] == \sPHP\CRON_JOB_TYPE_URL){
			$cURL = curl_init();
			curl_setopt($cURL, CURLOPT_URL, $this->Property["Command"]);
			curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true); // TRUE to return content than auto output
			curl_setopt($cURL, CURLOPT_HEADER, false); // TRUE to include the header in the output
			curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false); // FALSE to stop cURL from verifying the peer's certificate
			curl_setopt($cURL, CURLOPT_VERBOSE, false);

			$cURLResponse = curl_exec($cURL);
			$this->Property["Result"] = ["Error" => ["Code" => 0, "Message" => null, ], "Result" => $cURLResponse ? true : false, ];
			$this->Property["Result"]["Status"] = [];

			curl_close($cURL);
		}
		elseif($this->Property["Type"] == \sPHP\CRON_JOB_TYPE_PHP){
			$this->Property["Result"] = ___CronJob_PHP_Execute($this->Property["Command"], $this->Property["Resource"]);
		}
		elseif($this->Property["Type"] == \sPHP\CRON_JOB_TYPE_SHELL){
			$this->Property["Result"] = ["Error" => ["Code" => 0, "Message" => null, ], "Result" => shell_exec($this->Property["Command"]), ];
			$this->Property["Result"]["Status"] = [];
		}
		elseif($this->Property["Type"] == \sPHP\CRON_JOB_TYPE_SHELL_NOWAIT){
			$this->Property["Result"]["Error"] = ["Code" => 1, "Message" => "Type not implemented", ];
			$this->Property["Result"]["Status"] = [];
		}
		else{
			$this->Property["Result"]["Error"] = ["Code" => 1, "Message" => "Unknown type", ];
			$this->Property["Result"]["Status"] = [];
		}

		return $this->Property["Result"];
	}
    #endregion Method

    #region Property
    public function Command($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			if(is_null($this->Property["Type"])){
				$UpperCaseCommand = strtoupper($this->Property[__FUNCTION__]);
				$PathInfo = pathinfo($this->Property[__FUNCTION__]);

				if(
						substr($UpperCaseCommand, 0, 5) == "HTTP:"
					||	substr($UpperCaseCommand, 0, 6) == "HTTPS:"
				){
					$this->Property["Type"] = \sPHP\CRON_JOB_TYPE_URL;
				}
				elseif(
						isset($PathInfo["extension"])
					&&	strtoupper($PathInfo["extension"]) == "PHP"
				){
					$this->Property["Type"] = \sPHP\CRON_JOB_TYPE_PHP;
				}
				else{
					$this->Property["Type"] = \sPHP\CRON_JOB_TYPE_SHELL;
				}
			}

			if(!$this->Property["Name"])$this->Property["Name"] = $this->Property[__FUNCTION__];

            $Result = true;
        }

        return $Result;
    }

    public function Interval($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Resource($Value = null){
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

    public function MaximumExecutionTime($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Type($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Result(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }
    #endregion Property
}

function ___CronJob_PHP_Execute($Script, $Resource){
	if(file_exists($Script)){
		$sPHPCronJob = true; // DO NOT REMOVE THIS; PHP scripts can determine if ran from here by checking this variable

		require $Script;

		if(!isset($CronJobResult["Error"]))$CronJobResult["Error"] = ["Code" => 0, "Message" => null, ];
		if(!isset($CronJobResult["Status"]))$CronJobResult["Status"] = [];
	}
	else{
		$CronJobResult["Error"] = ["Code" => 1, "Message" => "Script not found", ];
		$CronJobResult["Status"] = [];
	}
//\sPHP\DebugDump($CronJobResult);
	return $CronJobResult;
}
?>