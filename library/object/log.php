<?php
namespace sPHP;

class Log{
	private $Property = [ // Default property value
        "Console" => false, 
        "File" => false, 
        "FileExpirySizeMB" => 2, 
        "Format" => "%TIME%	%TYPE%	%SOURCE%	%SUBJECT%	%ACTION%	%DESCRIPTION%	%DATA%	%SCRIPT%:%LINE%", 
        //"Format" => "%TIME% ◯ %TYPE% ◌ %SOURCE% ▶ %SUBJECT% ▷ %ACTION% ☵ %DESCRIPTION% ▤ %DATA% ♬ %SCRIPT% ⚐ %LINE%", 
        "Database" => false, 
        "DatabaseTable" => null, 
        "DatabaseColumn" => [
            "LogTime"			=>	"'%TIME%'", 
            "LogType"			=>	"'%TYPE%'", 
            "LogSource"			=>	"'%SOURCE%'", 
            "LogSubject"		=>	"'%SUBJECT%'", 
            "LogAction"			=>	"'%ACTION%'", 
            "LogDescription"	=>	"'%DESCRIPTION%'", 
            "LogDataType"		=>	"'%DATATYPE%'", 
            "LogData"			=>	"'%DATA%'", 
            "LogCallerFile"		=>	"'%SCRIPT%'", 
            "LogCallerFileLine"	=>	"'%LINE%'", 
            "UserID"			=>	"%USERID%", 
            "LogIsActive"		=>	"1", 
            "UserIDInserted"	=>	"%USERID%", 
            "TimeInserted"		=>	"NOW()", 
        ], 
        "UserID" => null, 
        "BasePath" => [
            "F:/Web/Test/", 
            "F:/Web/Test2/", 
        ], 
        "MinimumTypeLength" => 11, 
        "MinimumSourceLength" => 11, 
        "MinimumSubjectLength" => 11, 
        "MinimumActionLength" => 16, 
        "MinimumDescriptionLength" => 50, 
        
        #region Internal properties
        "SQL_INSERT_VALUES" => [], 
        #endregion Internal properties
    ];

	#region System
	public function __construct($Console = null, $File = null, $FileExpirySizeMB = null, $Format = null, $Database = null, $DatabaseColumn = null, $UserID = null, $BasePath = null, $MinimumTypeLength = null, $MinimumSourceLength = null, $MinimumSubjectLength = null, $MinimumActionLength = null, $MinimumDescriptionLength = null){
		// Set property from construct argument
		foreach(get_defined_vars() as $Property => $Value)if(!is_null($Value))$this->Property[$Property] = $Value;

		// Convert base path list property to processed array for performance
		foreach($this->Property["BasePath"] as $Key => $Value)$this->Property["BasePath"][$Key] = strtolower(str_replace("\\", "/", $Value));
	}

	public function __destruct(){
        $this->Flush();
	}
	#endregion System

	#region Method
	public function Put($Description = null, $Data = null, $Console = null, $Type = null, $Action = null, $Subject = null, $Source = null, $Time = null, $File = null, $Format = null, $Database = null){
		#region Determine missing arguments
		if(is_null($Console))$Console = $this->Property["Console"];
		if(is_null($File))$File = $this->Property["File"];
		if(is_null($Database))$Database = $this->Property["Database"] ? true : false;
		if(is_null($Format))$Format = $this->Property["Format"];
		#endregion Determine missing arguments
		
		if(($Console || $File || $Database) && $Format){ // Logging enabled
			#region Set missing arguments
			if(is_null($Type))$Type = LOG_TYPE_GENERAL;
			if(is_null($Time))$Time = date("Y-m-d H:i:s");
			#endregion Set missing arguments
			
			#region Detect data type and format data for store
			if(is_array($Data)){
				$DataType = LOG_DATA_TYPE_JSON;
				$Data = json_encode($Data);
			}
			elseif(is_object($Data)){
				$DataType = LOG_DATA_TYPE_OBJECT;
				$Data = json_encode($Data);
			}
			elseif(is_numeric($Data)){
				$DataType = LOG_DATA_TYPE_NUMERIC;
			}
			else{
				$DataType = LOG_DATA_TYPE_TEXT;
			}
			#endregion Detect data type and format data for store
	
			$Caller = debug_backtrace();
	
			if(!$Source || !$Subject || !$Action){ // Detect source, subject & action automatically if not provided with
				if(count($Caller) == 1){
					$Caller = $Caller[0]; //var_dump($Source); exit;
		
					$DetectedSource = "SCRIPT";
					$DetectedSubject = "STATEMENT";
					$DetectedAction = "EXECUTE";
				}
				else{
					$Caller = $Caller[1]; //var_dump($Source); exit;
		
					if(isset($Caller["class"]) && $Caller["class"]){
						$DetectedSource = "CLASS";
						$DetectedSubject = "{$Caller["class"]}";
						$DetectedAction = "{$Caller["function"]}";
					}
                    elseif(isset($Caller["function"]) && $Caller["function"]){
						$DetectedSource = "SCRIPT";
						$DetectedSubject = null;
						$DetectedAction = "{$Caller["function"]}";
                    }
					else{
						$DetectedSource = $DetectedSubject = $DetectedAction = null;
					}
				}
	
				if(!$Source)$Source = $DetectedSource;
				if(!$Subject)$Subject = $DetectedSubject;
				if(!$Action)$Action = $DetectedAction;
			}

			#region Limit content size
			$ConsoleDescription = strlen($Description) > 256 ? substr($Description, 0, 244) . "...truncated" : $Description;
			$ConsoleData = strlen($Data) > 256 ? substr($Data, 0, 244) . "...truncated" : $Data;
			#endregion Limit content size

			#region Make content fixed size
			$ConsoleType = str_pad($Type, $this->Property["MinimumTypeLength"], " ", STR_PAD_RIGHT);
			$ConsoleSource = str_pad($Source, $this->Property["MinimumSourceLength"], " ", STR_PAD_RIGHT);
			$ConsoleSubject = str_pad($Subject, $this->Property["MinimumSubjectLength"], " ", STR_PAD_RIGHT);
			$ConsoleAction = str_pad($Action, $this->Property["MinimumActionLength"], " ", STR_PAD_RIGHT);
			$ConsoleDescription = str_pad($ConsoleDescription, $this->Property["MinimumDescriptionLength"], " ", STR_PAD_RIGHT);
			#endregion Make content fixed size
	
            // Determine Caller information if not available
			$CallerFile = isset($Caller["file"]) ? $Caller["file"] : null;
            $CallerLine = isset($Caller["line"]) ? $Caller["line"] : 0;
            
			// Strip base path from beginning of the caller file
			if($CallerFile)foreach($this->Property["BasePath"] as $BasePath)if(substr(strtolower(str_replace("\\", "/", $CallerFile)), 0, strlen($BasePath)) == $BasePath)$CallerFile = substr($CallerFile, strlen($BasePath));

			$ConsoleLog = str_replace( // Make console log line
				["%TIME%", "%TYPE%", "%SOURCE%", "%SUBJECT%", "%ACTION%", "%DESCRIPTION%", "%DATA%", "%SCRIPT%", "%LINE%"], 
				[$Time, $ConsoleType, $ConsoleSource, $ConsoleSubject, $ConsoleAction, $ConsoleDescription, $ConsoleData, $CallerFile, $CallerLine], 
				$Format
			) . PHP_EOL;			
		
			if($ConsoleLog){ // We have some log to show/save
				if($Console)print $ConsoleLog;
	
				if($File){
					if(file_exists($File) && filesize($File) > ($this->Property["FileExpirySizeMB"] * 1024 * 1024))unlink($File);
					file_put_contents($File, $ConsoleLog, FILE_APPEND);
				}

				if($Database)$this->Property["SQL_INSERT_VALUES"][] = "(" . str_replace(
					["%TIME%", "%TYPE%", "%SOURCE%", "%SUBJECT%", "%ACTION%", "%DESCRIPTION%", "%DATATYPE%", "%DATA%", "%SCRIPT%", "%LINE%", "%USERID%"], 
					[$this->Property["Database"]->Escape($Time), $this->Property["Database"]->Escape($Type), $this->Property["Database"]->Escape($Source), $this->Property["Database"]->Escape($Subject), $this->Property["Database"]->Escape($Action), $this->Property["Database"]->Escape($Description), $this->Property["Database"]->Escape($DataType), $this->Property["Database"]->Escape($Data), $this->Property["Database"]->Escape($CallerFile), $CallerLine, $this->Property["Database"]->Escape(intval($this->Property["UserID"]))], 
					implode(", ", $this->Property["DatabaseColumn"])
				) . ")";
			}
		}
	}

	public function Flush(){
        if($this->Property["Database"] && $this->Property["DatabaseTable"] && count($this->Property["SQL_INSERT_VALUES"])){ // Save log to database all at once
            $this->Property["Database"]->Query("INSERT INTO {$this->Property["DatabaseTable"]} (" . implode(", ", array_keys($this->Property["DatabaseColumn"])) . ") VALUES" . PHP_EOL . implode(", " . PHP_EOL, $this->Property["SQL_INSERT_VALUES"]) . "");
            $this->Property["SQL_INSERT_VALUES"] = []; // CLear SQL buffer
        }
	}
	#endregion Method

	#region Property
	public function Console($Value = null){
		if(is_null($Value))return $this->Property[__FUNCTION__];
		$this->Property[__FUNCTION__] = $Value;
	}

	public function File($Value = null){
		if(is_null($Value))return $this->Property[__FUNCTION__];
		$this->Property[__FUNCTION__] = $Value;
	}

	public function FileExpirySizeMB($Value = null){
		if(is_null($Value))return $this->Property[__FUNCTION__];
		$this->Property[__FUNCTION__] = $Value;
	}

	public function Format($Value = null){
		if(is_null($Value))return $this->Property[__FUNCTION__];
		$this->Property[__FUNCTION__] = $Value;
	}

	public function Database($Value = null){
		if(is_null($Value))return $this->Property[__FUNCTION__];
		$this->Property[__FUNCTION__] = $Value;
	}

	public function DatabaseTable($Value = null){
		if(is_null($Value))return $this->Property[__FUNCTION__];
		$this->Property[__FUNCTION__] = $Value;
	}

	public function DatabaseColumn($Value = null){
		if(is_null($Value))return $this->Property[__FUNCTION__];
		$this->Property[__FUNCTION__] = $Value;
	}

	public function UserID($Value = null){
		if(is_null($Value))return $this->Property[__FUNCTION__];
		$this->Property[__FUNCTION__] = $Value;
	}

    public function BasePath($Value = null){
        if(is_null($Value))return $this->Property[__FUNCTION__];
        $this->Property[__FUNCTION__] = $Value;
    }

	public function MinimumTypeLength($Value = null){
		if(is_null($Value))return $this->Property[__FUNCTION__];
		$this->Property[__FUNCTION__] = $Value;
	}

	public function MinimumSourceLength($Value = null){
		if(is_null($Value))return $this->Property[__FUNCTION__];
		$this->Property[__FUNCTION__] = $Value;
	}

	public function MinimumSubjectLength($Value = null){
		if(is_null($Value))return $this->Property[__FUNCTION__];
		$this->Property[__FUNCTION__] = $Value;
	}

	public function MinimumActionLength($Value = null){
		if(is_null($Value))return $this->Property[__FUNCTION__];
		$this->Property[__FUNCTION__] = $Value;
	}

	public function MinimumDescriptionLength($Value = null){
		if(is_null($Value))return $this->Property[__FUNCTION__];
		$this->Property[__FUNCTION__] = $Value;
	}

    public function SQL_INSERT_VALUES(){
        return $this->Property[__FUNCTION__];
    }
	#endregion Property
}
?>