<?php
namespace sPHP;

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
	#endregion Private variable

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

			$Result = implode("", $HTML);

			if($Show)print $Result;
		}

		return $Result;
	}

	public function StopRegion($Show = false){
		$Result = false;

		if($this->Property["Enabled"]){
			$HTML[] = "</div></div>";
			if($Show)print implode("", $HTML);

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

					<tbody>" . implode("", $HTML) . "</tbody>
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

						<tbody>" . implode("", $HTML) . "</tbody>
					</table>
				</div>
			";
		}

		return $Result;
	}

	public function Dump($Value, $Name = null, $Output = true, $CallerDepth = 0, $NestingLevel = 0){
		if(is_null($Value)){
            $Value = "<div class=\"NULL\">NULL</div>";
            $Type = "Unknown";
		}
		elseif(is_array($Value)){
			$Type = "Array<div class=\"Information\">" . count($Value) . "</div>";
			foreach($Value as $Key => $ThisValue)$Item[] = $this->Dump($ThisValue, $Key, false, $CallerDepth, $NestingLevel + 1);
			$ToggleID = $this->Utility->GUID();

			$Value = isset($Item) ? "
				<input type=\"checkbox\" id=\"Toggle_{$ToggleID}\" checked class=\"Toggle\">
				<label for=\"Toggle_{$ToggleID}\" class=\"ToggleNotch\"></label>
				<div class=\"Array\">" . implode("", $Item) . "</div>
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
					<div class=\"Object\">" . implode("", $Item) . "</div>
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
                
				$Value = "<div class=\"Number\">{$Value}</div>";
			}
			elseif(is_string($Value)){
				$Type = "String<div class=\"Information\">" . strlen($Value) . "</div>";
				$Value = "<div class=\"String\">{$Value}</div>";
			}
			else{
				$Type = "" . gettype($Value) . "";
			}
		}

		$Result = "
				<div class=\"Item\">
					" . (strlen($Name) ? "<div class=\"Name\">{$Name}</div>" : null) . "
					<div class=\"Type\">{$Type}</div>
					<div class=\"Value\">{$Value}</div>
				</div>
		";

		if($NestingLevel == 0){
            //var_dump($CallerDepth);
            //var_dump(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $CallerDepth + 1)[$CallerDepth]);
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
            $this->Property[__FUNCTION__] = $Value; //var_dump(__FILE__, __LINE__, __FUNCTION__, $Value, debug_backtrace()); exit;

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

						<tbody>" . (isset($CheckpointHTML) ? implode("", $CheckpointHTML) : null) . "</tbody>

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