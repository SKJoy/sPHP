<?php
/*
    Name:           Service
    Purpose:        Service object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 15, 2018 11:17 PM
*/

namespace sPHP;

class Service{
    #region Property
    private $Property = [
        "Name"				=>	null, // Service name
        "Execute"			=>	[], // Script or function to execute, can be arrays of them
		"StatusExpiry"		=>	120, // Seconds after which the status file is assumed out dated
		"ExecutableBasePath"	=>	null, // Common base path for more than one executables (if script)
        "Delay"				=>	5, // Delay in seconds between each execution
        "StartupDelay"		=>	5, // Delay in seconds before the first execution occurs
        "StatusFile"		=>	null, // Status JSON file to output current information
        "CommandFile"		=>	null, // Command file to read command from
        "Prepend"			=>	null, // Script or function to execute before the first execution
        "Append"			=>	null, // Script or function to execute after the first execution upon normal exit
		"ExitCommand"		=>	"STOP", // Command to exit execution loop and stop the service
    ];
    #endregion Property

    #region Variable
    //private $Buffer = [];
    #endregion Variable

    #region Method
    public function __construct($Name = null, $Execute = null, $StatusExpiry = null, $ExecutableBasePath = null, Delay = null, StartupDelay = null, $StatusFile = null, $CommandFile = null, $Prepend = null, $Append = null, $ExitCommand = null){
        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){
		$Result = true;

        return $Result;
    }

	function Start(){
		$___ServiceStartTime = microtime(true);

		if(
				!file_exists($this->Property["CommandFile"])
			||	isset($_POST["Command"])
			||	isset($_GET["Command"])
		){
			file_put_contents($this->Property["CommandFile"], isset($_POST["Command"]) ? $_POST["Command"] : (isset($_GET["Command"]) ? $_GET["Command"] : null));
		}

		if(!file_exists($this->Property["StatusFile"]))file_put_contents($this->Property["StatusFile"], json_encode([
			"Service" => [
				"Name" => $this->Property["Name"],
				"CommandFile" => $this->Property["CommandFile"],
				"Command" => file_get_contents($this->Property["CommandFile"]),
				"Prepend" => $this->Property["Prepend"],
				"Append" => $this->Property["Append"],
				"StartupDelay" => $this->Property["StartupDelay"],
				"StatusExpiry" => $this->Property["StatusExpiry"],
				"Running" => false,
			],
			"Executable" => [
				"BasePath" => $this->Property["ExecutableBasePath"],
				"Delay" => $this->Property["Delay"],
			],
		]));

		$___ServiceStatus = json_decode(file_get_contents($this->Property["StatusFile"]));

		if(
				file_get_contents($this->Property["CommandFile"]) != $this->Property["ExitCommand"]
			&&	(
						!$___ServiceStatus->Service["Running"]
					||	$___ServiceStartTime - filemtime($this->Property["StatusFile"]) > $this->Property["StatusExpiry"]
				)
		){
			if($this->Property["StartupDelay"])sleep($this->Property["StartupDelay"]);

			$___ServiceStatus->Service["Running"] = true;
			$___ServiceStatus->Service["Time"]["Start"] = date("r", $___ServiceStartTime);
			file_put_contents($this->Property["StatusFile"], json_encode($___ServiceStatus));

			if($this->Property["Prepend"]){
				require $this->Property["Prepend"];
			}

			while(
					!file_exists($this->Property["CommandFile"])
				||	file_get_contents($this->Property["CommandFile"]) != $this->Property["ExitCommand"]
			){
				foreach(is_array($this->Property["Execute"]) ? $this->Property["Execute"] : [$this->Property["Execute"]] as $___ExecutableIndex => $___Execute){
					$___ExecutableStartTime = microtime(true);

					$___ServiceStatus->Executable["Item"][$___ExecutableIndex] = [
						"Execute" => $___Execute,
						"Time" => [
							"Start" => date("r", $___ExecutableStartTime);
						],
					];

					$ExecutableStatus = [];
					require "{$this->Property["ExecutableBasePath"]}{$___Execute}";
					$___ServiceStatus->Executable["Item"][$___ExecutableIndex]["Status"] = isset($ExecutableStatus) ? $ExecutableStatus : [];

					$___ExecutableStopTime = microtime(true);
					$___ServiceStatus->Executable["Item"][$___ExecutableIndex]["Time"]["Stop"] = date("r", $___ExecutableStopTime);
					$___ServiceStatus->Executable["Item"][$___ExecutableIndex]["Time"]["Duration"] = $___ExecutableStopTime - $___ExecutableStartTime;
					file_put_contents($this->Property["StatusFile"], json_encode($___ServiceStatus));
				}

				if($this->Property["Delay"])sleep($this->Property["Delay"]);
			}

			if($this->Property["Append"]){
				require $this->Property["Prepend"];
			}

			$___ServiceStopTime = microtime(true);
			$___ServiceStatus->Service["Time"]["Stop"] = date("r", $___ServiceStopTime);
			$___ServiceStatus->Service["Time"]["Duration"] = $___ServiceStopTime - $___ServiceStartTime;
			$___ServiceStatus->Service["Running"] = false;
			file_put_contents($this->Property["StatusFile"], json_encode($___ServiceStatus));
		}
	}
    #endregion Method

    #region Property
    public function Name($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			if(is_null($this->Property["StatusFile"]))$this->Property["StatusFile"] = "./" . str_replace(split(" \\/*:\".?"), "_", $this->Property[__FUNCTION__]) . ".json";
			if(is_null($this->Property["CommandFile"]))$this->Property["CommandFile"] = "./" . str_replace(split(" \\/*:\".?"), "_", $this->Property[__FUNCTION__]) . "_command.txt";

            $Result = true;
        }

        return $Result;
    }

    public function Execute($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function StatusExpiry($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function ExecutableBasePath($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Delay($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function StartupDelay($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function StatusFile($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function CommandFile($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Prepend($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Append($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function ExitCommand($Value = null){
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