<?php
/*
    Name:           Cron
    Purpose:        Cron object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  July 26, 2020 11:32 AM
*/

namespace sPHP;

class Cron{
    private $Property = [
		"Path"				=>	null,
        "Name"				=>	"Default", // Process name, should not be duplicated to prevent configuration & status overwrite
        "Job"				=>	[],
		"Resource"			=>	[], // Resources to pass to JOBs if type is REQUIRE PHP script
        "MaximumExecutionTime"	=>	60 * 60, // Seconds; Cron will restart if this time expires, even if running. Watch out for per Job MaximumExecutionTime
		"ServiceInterval"	=>	null, // Make it a service with given sleep interval seconds; Set NULL to skip service mode; CAUTION: Too low value will keep the CPU too busy!
		"ExitCommand"		=>	"EXIT",
		"Verbose"			=>	false,
		"ExitDuration"		=>	24 * 60 * 60, // Automatically exit after this duration, without setting the Exit command so the next call can run, like a service restart scope
        "StatusURL"			=>	null,
		"StatusFile"		=>	null,
		"CommandFile"		=>	null,
    ];

    #region Variable
    //private $Buffer = [];
    #endregion Variable

    #region Method
    public function __construct($Path = null, $Name = null, $Job = null, $Resource = null, $MaximumExecutionTime = null, $ServiceInterval = null, $ExitCommand = null, $Verbose = null){
        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

		return true;
    }

	public function Execute(){
		$this->Property["Job"] = array_filter($this->Property["Job"]);
		CreatePath(pathinfo($this->Property["StatusFile"])["dirname"]);

		if(!file_exists($this->Property["StatusFile"]))$this->SaveStatusFile([ // Create missing status JSON file
			"Exit"		=>	[
				"Time"			=>	null,
				"Reason"		=>	null,
			],
			"Configuration"	=>	[
				"Interval"				=>	$this->Property["ServiceInterval"],
				"MaximumExecutionTime"	=>	$this->Property["MaximumExecutionTime"],
				"StatusURL"				=>	$this->Property["StatusURL"],
			],
			"Running"	=>	false,
			"Job"		=>		[],
			"Iteration"	=>	[
				"Count"		=>	null,
				"Time"		=>	[
					"Begin"		=>	null,
					"End"		=>	null,
					"Duration"	=>	null,
				],
			],
			"Time"		=>	[
				"Begin"		=>	null,
				"End"		=>	null,
				"Duration"	=>	null,
			],
			"Load"		=>	[
				"Memory"	=>	null,
				"System"	=>	null,
			],
		]);

		if($Status = json_decode(file_get_contents($this->Property["StatusFile"]))){
			$CurrentTime = microtime(true);
			$StatusFileAge = intval($CurrentTime - filemtime($this->Property["StatusFile"]));

			if(
					$Status->Running // Already running				
				&&	$StatusFileAge <= $this->Property["MaximumExecutionTime"] // Status file is not aged enough to discard
			){
				$Status->Exit->Time = date("r", $CurrentTime);
				$Status->Exit->Reason = "Previous process running";
				//$this->SaveStatusFile($Status); // This will fail the file modification time comparison!

				$Result = false;

				if($this->Property["Verbose"])print HTML\UI\MessageBox("
					Previous process of '{$this->Property["Name"]}' is already running.<br>
					<br>
					Status age: " . SecondToTime($StatusFileAge) . "
				", "Cron");
			}
			else{
				ignore_user_abort(true); // Continue execution even if client leaves/disconnects/aborts

				$Status->Exit->Time = null;
				$Status->Exit->Reason = null;
				$Status->Configuration->Interval = $this->Property["ServiceInterval"];
				$Status->Configuration->MaximumExecutionTime = $this->Property["MaximumExecutionTime"];
				$Status->Configuration->StatusURL = $this->Property["StatusURL"];
				$Status->Time->Begin = date("r", $CurrentTime);
				$Status->Running = true;

				if(is_array($Status->Job))$Status->Job = (object)[]; // Convert Job node to stdClass on fresh run

				foreach($this->Property["Job"] as $JobIndex => $Job){					
					if(!isset($Status->Job->{$Job->Name()}))$Status->Job->{$Job->Name()} = (object)[ // // Create new Job node as stdClass
						"Running" => false, 
						"Time" => (object)[], 
						"Configuration" => (object)[], 
						"Comment" => (object)[], 
						"Result" => (object)[], 
						"Data" => (object)[], 
					];

					#region Check and set if nodes do not exist
					if(!isset($Status->Job->{$Job->Name()}->Data))$Status->Job->{$Job->Name()}->Data = (object)[];
					#endregion Check and set if nodes do not exist

					$Status->Job->{$Job->Name()}->Configuration->Order = $JobIndex + 1;
					$Status->Job->{$Job->Name()}->Configuration->Type = $Job->Type();
					$Status->Job->{$Job->Name()}->Configuration->Command = $Job->Command();
					$Status->Job->{$Job->Name()}->Configuration->Interval = $Job->Interval();
					$Status->Job->{$Job->Name()}->Configuration->MaximumExecutionTime = $Job->MaximumExecutionTime();
				}

				$IterationCounter = 0;

				do{
					if(!file_exists($this->Property["CommandFile"]))file_put_contents($this->Property["CommandFile"], null);
					$Command = file_get_contents($this->Property["CommandFile"]);

					if($Command == $this->Property["ExitCommand"]){
						$Status->Exit->Time = date("r", $CurrentTime);
						$Status->Exit->Reason = "Exit command";

						$Result = false;

						if($this->Property["Verbose"])print HTML\UI\MessageBox("Exiting the process of '{$this->Property["Name"]}' due to '{$this->Property["ExitCommand"]}' command.", "Cron");
					}
					else{
						set_time_limit($this->Property["MaximumExecutionTime"]); // Limit maximum execution time

						$IterationCounter++;
						$IterationTimeBegin = microtime(true);

						$Status->Iteration->Count = $IterationCounter;
						$Status->Iteration->Time->Begin = date("r", $IterationTimeBegin);
						$this->SaveStatusFile($Status);

						foreach($this->Property["Job"] as $JobIndex => $Job){
							if(
									!isset($Status->Job->{$Job->Name()}->Time->Begin) // Never ran
								||	time() - strtotime($Status->Job->{$Job->Name()}->Time->Begin) > $Job->Interval() // Expired
							){
								#region Pass through resoueces to Job
								if(!$Job->Resource() && $this->Property["Resource"])$Job->Resource($this->Property["Resource"]); // Assign if not already there
								$Job->Data($Status->Job->{$Job->Name()}->Data);
								#endregion Pass through resoueces to Job
								
								#region Initiate Job execution start
								$JobBeginTime = microtime(true);
								$Status->Job->{$Job->Name()}->Comment = null;
								$Status->Job->{$Job->Name()}->Running = true;
								$Status->Job->{$Job->Name()}->Time->Begin = date("r", $JobBeginTime);
								$this->SaveStatusFile($Status); // Update Cron status
								#endregion Initiate Job execution start
								
								$Status->Job->{$Job->Name()}->Result = $Job->Execute(); // Execute the job
								
								#region Initiate Job execution stop
								$JobEndTime = microtime(true);
								$Status->Job->{$Job->Name()}->Running = false;
								$Status->Job->{$Job->Name()}->Time->End = date("r", $JobEndTime);
								$Status->Job->{$Job->Name()}->Time->Duration = $JobEndTime - $JobBeginTime;
								#endregion Initiate Job execution stop
								
								#region Check and set the return values from Job
								if(!isset($Status->Job->{$Job->Name()}->Result["Error"]))$Status->Job->{$Job->Name()}->Result["Error"] = ["Code" => 0, "Message" => null, ];
								if(!isset($Status->Job->{$Job->Name()}->Result["Status"]))$Status->Job->{$Job->Name()}->Result["Status"] = [];
								if(!isset($Status->Job->{$Job->Name()}->Result["Data"]))$Status->Job->{$Job->Name()}->Result["Data"] = $Status->Job->{$Job->Name()}->Data; // Keep previous data
								#endregion Check and set the return values from Job
								
								// Move Data to Job node from Result node
								$Status->Job->{$Job->Name()}->Data = $Status->Job->{$Job->Name()}->Result["Data"];
								unset($Status->Job->{$Job->Name()}->Result["Data"]);
								
								// Check why this converts the simple array to an indexed array!
								// Filter out empty status
								//$Status->Job->{$Job->Name()}->Result["Status"] = array_filter($Status->Job->{$Job->Name()}->Result["Status"]);
								
								$this->SaveStatusFile($Status); // Update Cron status
							}
							else{
								$Status->Job->{$Job->Name()}->Comment = "Skipped within interval";
							}
						}

						if(!$this->Property["ServiceInterval"])$Status->Running = false; // Follow service mode

						$IterationTimeEnd = microtime(true);

						$Status->Iteration->Time->End = date("r", $IterationTimeEnd);
						$Status->Iteration->Time->Duration = $IterationTimeEnd - $IterationTimeBegin;
						$Status->Time->End = date("r", $IterationTimeEnd);
						$Status->Time->Duration = $IterationTimeEnd - $CurrentTime;
						$Status->Load->Memory = memory_get_usage();
						$Status->Load->System = function_exists("sys_getloadavg") ? sys_getloadavg()[0] : 0;
						$this->SaveStatusFile($Status);

						$Result = true;

						sleep(intval($this->Property["ServiceInterval"]));
					}
				}while(
						$this->Property["ServiceInterval"] 
					&&	$Command != $this->Property["ExitCommand"]
					&&	time() - $CurrentTime < $this->Property["ExitDuration"] // Shouldn't we get a restart at least once a day :)
				);

				$Status->Running = false;
				$this->SaveStatusFile($Status);
			}
		}
		else{ // Corrupted status JSON file!
			unlink($this->Property["StatusFile"]); // Wait for next call and create new
			$Result = false;
		}

		return $Result;
	}

	// Note: This is not a property, although might behave like that
	public function Command($Command = null){
		if(is_null($Command)){
			$Result = file_get_contents($this->Property["CommandFile"]);
		}
		else{
			$Result = true;
			file_put_contents($this->Property["CommandFile"], $Command);

			if($this->Property["Verbose"])print HTML\UI\MessageBox("Command set to '{$Command}' for '{$this->Property["Name"]}'.", "Cron");
		}

		return $Result;
	}
    #endregion Method

    #region Property
    public function Path($Value = null){
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
            $this->Property[__FUNCTION__] = $Value ? $Value : "Default"; // Don't allow empty name

			$ProcessPathName = ValidFileName($this->Property[__FUNCTION__]);
			$Path = "{$this->Property["Path"]}cron/process/{$ProcessPathName}/";
			$StatusFileName = "status.json";

			$this->Property["StatusURL"] = "./script/cron/process/{$ProcessPathName}/{$StatusFileName}";
			$this->Property["StatusFile"] = "{$Path}{$StatusFileName}";
			$this->Property["CommandFile"] = "{$Path}command.txt";

            $Result = true;
        }

        return $Result;
    }

    public function Job($Value = null){
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

    public function ServiceInterval($Value = null){
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

    public function Verbose($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function ExitDuration($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function StatusURL(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function StatusFile(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function CommandFile(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }
    #endregion Property

	#region Private function
	private function SaveStatusFile($Status){
		file_put_contents($this->Property["StatusFile"], json_encode($Status));

		return true;
	}
	#endregion Private function
}
?>