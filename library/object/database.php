<?php
/*
    Name:           Database
    Purpose:        Database object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 12, 2018 10:51 AM
    Date modified:  January 22, 2021 09:31 PM
*/

namespace sPHP;

class Database{
    #region Property variable
    private $Property = [
        "Type"						=>	DATABASE_TYPE_MYSQL,
        "Host"						=>	"127.0.0.1",
        "User"						=>	null,
        "Password"					=>	null,
        "Name"						=>	null,
		"ODBCDriver"				=>	null,
		"TablePrefix"				=>	null,
		"Timezone"					=>	"GMT", // GMT Asia/Dhaka
		"Encoding"					=>	"UTF8MB4",
		"Strict"					=>	true,
		"Verbose"					=>	false,
		"Transactional"				=>	false,
		"KeepQueryHistory"			=>	false, // Memory consuming!
		"ErrorLogPath"				=>	null,

		// Read only
        "Connection"				=>	null,
		"Recordset"					=>	null,
		"LastDuration"				=>	0,
		"Duration"					=>	0,
		"QueryCount"				=>	0,
		"QueryHistory"				=>	[],
		"Table"						=>	[],
    ];
    #endregion Property variable

	#region Variable
	//private $Connection = null;
	#endregion Variable

    #region Method
    public function __construct($Type = null, $Host = null, $User = null, $Password = null, $Name = null, $ODBCDriver = null, $TablePrefix = null, $Timezone = null, $Encoding = null, $Strict = null, $Verbose = null, $ErrorLogPath = null){
        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){
		$this->Disconnect(); // Close any existing connection

        return true;
    }

	public function Connect(){
		$this->Disconnect(); // Close any previous connection
		
		if($this->Property["Type"] == DATABASE_TYPE_MYSQL){
			try{
				$this->Property["Connection"] = new \PDO("mysql:host={$this->Property["Host"]};dbname={$this->Property["Name"]};charset=" . strtolower(str_replace("-", null, $this->Property["Encoding"])) . "", $this->Property["User"], $this->Property["Password"], [
					\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
					\PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '{$this->Property["Timezone"]}'",
					//\PDO::ATTR_TIMEOUT => 300,
					//\PDO::ATTR_EMULATE_PREPARES => false, // This triggers false error with SET XXXX statements
				]);
			}
			catch(\Throwable $Exception){ // Log and show the error/exception but do not trigger a PHP fatal error
				$this->LogError("Database connection failed!", null, [
					$Key = "Host" => $this->Property["{$Key}"], 
					$Key = "User" => $this->Property["{$Key}"], 
					$Key = "Name" => $this->Property["{$Key}"], 
					$Key = "Password" => "*****", 
				], false, true);
			}

			if(!is_null($this->Property["Connection"])){
				$this->Query("
					# Following does not work with PDO::ATTR_EMULATE_PREPARES = false; neither could be appended with MYSQL_ATTR_INIT_COMMAND
					SET sql_mode = '" . ($this->Property["Strict"] ? "STRICT_ALL_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO" : null) . "';
				");

				if($this->Property["Transactional"])$this->Property["Connection"]->beginTransaction(); // Encapsulate execution within transaction
				
				if(is_array($Tables = $this->Query("SHOW TABLES")[0])){
					foreach($Tables as $Table)$this->Property["Table"][] = $Table[array_keys($Table)[0]];
				}

				$Result = $this->Property["Connection"];
			}
			else{
				$Result = false;
			}
		}
		elseif($this->Property["Type"] == DATABASE_TYPE_MSSQL && $this->Property["ODBCDriver"]){
			try{
				$this->Property["Connection"] = new \PDO("odbc:Driver={$this->Property["ODBCDriver"]}; Server={$this->Property["Host"]}; Database={$this->Property["Name"]}", $this->Property["User"], $this->Property["Password"]);
			}
			catch(\Throwable $Exception){
				//trigger_error($Exception->getMessage(), E_USER_ERROR);

				print "" . HTML\UI\MessageBox(
					"
						Couldn't connect to the database!<br>
						<br>
						{$Exception->getMessage()}
					",
					"Database",
					"MessageBoxError"
				) . "";
			}

			if(!is_null($this->Property["Connection"])){
				$this->Property["Connection"]->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

				/*
				$this->Query("
					SET NAMES " . strtolower(str_replace("-", null, $this->Property["Encoding"])) . ";
					SET time_zone = '{$this->Property["Timezone"]}';
					SET sql_mode = " . ($this->Property["Strict"] ? "STRICT_ALL_TABLES" : '') . ";
				");
				*/

				if($this->Property["Transactional"])$this->Property["Connection"]->beginTransaction(); // Encapsulate execution within transaction

				if($this->Property["Type"] == DATABASE_TYPE_MSSQL){
					foreach($this->Property["Connection"]->query("SELECT DISTINCT(TABLE_NAME) FROM information_schema.TABLES") as $Table)$this->Property["Table"][] = $Table["TABLE_NAME"];
				}

				$Result = $this->Property["Connection"];
			}
			else{
				$Result = false;
			}
		}
		else{
			//trigger_error("Unsupported database type '{$this->Property["Type"]}'", E_USER_ERROR);
			print "" . HTML\UI\MessageBox("Unsupported database type '{$this->Property["Type"]}'", "Database", "MessageBoxError") . "";

			$Result = false;
		}

		return $Result;
	}

	public function Disconnect(){
		$Result = true;

		if(!is_null($this->Property["Connection"])){ // Connection exists
			if($this->Property["Transactional"])$this->Property["Connection"]->commit(); // Encapsulate execution within transaction
			$this->Property["Connection"] = null; // Close connection
		}

		return $Result;
	}

	/*
		Argument
			SQL: SQL statement to execute
			Parameter: Query parameters to pass to prepare the statement with
			Verbose: Display query information on demand
			NoHistory: TRUE = Does not keep query informatio history; Keeps otherwise
			IgnoreError: TRUE = Does not trigger PHP fatal error on query execution error; Error log is still generated

		Return value
			Array of full or partial recordset(s) when query is successfully executed with at least one statement
			Explicit FALSE upon connection or complete query execution error (no statement could be executed)

		Note
			Return type Array does not necessarily mean the query execution went without error through the entire way.
			Empty or partial Array of recordset(s) may be generated even if error is encountered during query execution.
			This happens especially with multi query where error is encountered after at least one successful statement execution.
			
			An empty Array also evaluates to FALSE. So do not just rely on $Database->Query() method's return value only.
			Use explicit comparison with !== FALSE or is_array() with optional count() of recordset to determine accurate expectation from the query.
	*/
	public function Query($SQL, $Parameter = null, $Verbose = null, $NoHistory = null, $IgnoreError = null){ //DebugDump($SQL);
		#region Set default argument values
		if(is_null($Verbose))$Verbose = $this->Property["Verbose"];
		if(is_null($NoHistory))$NoHistory = !$this->Property["KeepQueryHistory"];
		if(is_null($IgnoreError))$IgnoreError = false;
		#endregion Set default argument values

		if($Verbose)$this->ShowMessage("Initiating query execution.", $SQL, $Parameter);

		if(is_null($this->Property["Connection"])){ // No database connection
			$Result = false; // Return explicit FALSE on connection error
			$this->LogError("No connection", $SQL, $Parameter, false, true); // Show error message but do not throw Exception
		}
		else{ // Process the query
			$LastDurationStart = microtime(true);

			#region Try executing the query
			try{
				$Query = $this->Property["Connection"]->prepare($SQL); // Does not throw error when PDO::ATTR_EMULATE_PREPARES = false
				$Query->execute(is_null($Parameter) ? [] : $Parameter);
				$Result = []; // Initialize Result as an Array to hold the recordset(s)
			}
			catch(\Throwable $Exception){
				$Result = false; // Return explicit FALSE as no statement could be executed successfully
				$this->LogError("{$Exception->errorInfo[0]}: {$Exception->errorInfo[2]}", $SQL, $Parameter, !$IgnoreError, $Verbose);
			}
			#endregion Try executing the query
			
			#region Generate resulting recordset(s)
			if($Result !== false){ // No error detected
				do{ // Generate mutiple recordset
					try{ // Get current recordset by TRYing as this might result into a GENERAL ERROR which appears to be a false positive
						$Dataset = $Query->fetchAll(\PDO::FETCH_ASSOC); //DebugDump($Dataset);
						if($Dataset)$Result[] = $Dataset; // Generate result only if a valid recordset
					}catch(\Throwable $Exception){ //DebugDump($Exception->errorInfo);
						// We do not do anything as we do not add up any empty recordset into the result
					}

					// Special thanks to Saiful Islam for the tricky flow control below
					try{ // Advance the recordset pointer to the next available
						$ContinueLoop = $Query->nextRowset();
					}
					catch(\Throwable $Exception){ //DebugDump($Exception->errorInfo);
						$ContinueLoop = false; // Somehow the system sets the end value to FALSE and is able exit the DO WHILE loop! But this is phishy!
						
						// We do not need the following
						//$Result = false; // Keep with Array with results so far

						$this->LogError("{$Exception->errorInfo[0]} ({$Exception->errorInfo[1]}): {$Exception->errorInfo[2]}", $SQL, $Parameter, !$IgnoreError, $Verbose);
					}
				}while($ContinueLoop); //DebugDump($ContinueLoop);
			}
			#endregion Generate resulting recordset(s)

			#region Update query information & history
			$this->Property["LastDuration"] = microtime(true) - $LastDurationStart;
			$this->Property["Duration"] = $this->Property["Duration"] + $this->Property["LastDuration"];
			$this->Property["QueryCount"]++;

			if(!$NoHistory)$this->Property["QueryHistory"][] = [ // Add query information to history
				"SQL" => $SQL, 
				"Parameter" => $Parameter, 
				"Duration" => $this->Property["LastDuration"], 
				"Result" => $Result, // Beware, huge recordsets can make the history too heavy to cause memory load/out
			];
			#endregion Update query information & history
		} //DebugDump($Result);

		$this->Property["Recordset"] = $Result; // Update Recordset property with the generated result set

		return $Result;
	}

	public function Escape($Value){
		return str_replace("'", "''", $Value);
	}

	public function ClearHistory(){
		$this->Property["QueryHistory"] = [];

		return true;
	}
    #endregion Method

    #region Property
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

    public function Host($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function User($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Password($Value = null){
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

    public function ODBCDriver($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function TablePrefix($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Timezone($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Encoding($Value = null){ //DebugDump($Value);
		if($Value == "UTF-8")$Value = "UTF-8-MB4"; // Fix for MySQL UTF8 alias

        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Strict($Value = null){
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

    public function Transactional($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function KeepQueryHistory($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function ErrorLogPath($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Connection(){
		$Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function Recordset(){
		$Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function LastDuration(){
		$Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function Duration(){
		$Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function QueryCount(){
		$Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function QueryHistory(){
		$Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function Table(){
		$Result = $this->Property[__FUNCTION__];

        return $Result;
    }
	#endregion Property
	
	#region Function
	private function LogError($Message, $SQL, $Parameter, $TriggerError, $Verbose){
		$DebugCallStack = debug_backtrace();
		array_shift($DebugCallStack);
		array_pop($DebugCallStack);
		array_pop($DebugCallStack);
		array_pop($DebugCallStack);

		file_put_contents(
			"{$this->Property["ErrorLogPath"]}database.json",
			json_encode([
				"Error" => [
					"Code" => null,
					"Message" => $Message,
				],
				"Time" => date("r"),
				"Procedure" => [
					"Namespace" => __NAMESPACE__,
					"Object" => __CLASS__,
					"Method" => __FUNCTION__,
					"Argument" => [
						"SQL" => $SQL,
						"Parameter" => $Parameter,
					],
				],
				"Callstack" => $DebugCallStack,
			])
		);

		if($TriggerError)trigger_error("Database: {$Message}", E_USER_ERROR);
		if($Verbose)$this->ShowMessage($Message, $SQL, $Parameter);

		return true;
	}

	private function ShowMessage($Message, $SQL, $Parameter){
		if(!is_array($Parameter))$Parameter = [$Parameter];

		$ParameterHTML = [];
		foreach($Parameter as $Key => $Value)$ParameterHTML[] = "{$Key}: {$Value}";
		print \sPHP\HTML\UI\MessageBox("{$Message}" . (count($ParameterHTML) ? "<ul><li>" . implode("</li><li>", $ParameterHTML) . "</li></ul>" : null) . ($SQL ? "<code>{$SQL}</code>" : null) . "", "Database", "MessageBox_Error");

		return true;
	}
	#endregion Function
}
?>