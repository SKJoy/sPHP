<?php
namespace sPHP;

class Database{
    private $Property = [ // Property value store
        "Type"						=>	DATABASE_TYPE_MYSQL,
        "Host"						=>	"127.0.0.1",
        "User"						=>	null,
        "Password"					=>	null,
        "Name"						=>	null,
		"ODBCDriver"				=>	null,
		"TablePrefix"				=>	null,
		"Timezone"					=>	"+00:00", // Using named time zone is unstable in some cases
		"Encoding"					=>	"utf8mb4", //! Do not use utf8 as MySQL/MariaDB has dropped support for that
		"Strict"					=>	true,
		"Verbose"					=>	false,
		"Transactional"				=>	false,
		"KeepQueryHistory"			=>	false, // Memory consuming!
		"ErrorLogPath"				=>	null,
		"IgnoreQueryError"			=>	false, // Trigger error on $this->Query() malfunction

		// Read only
        "Connection"				=>	null,
		"Recordset"					=>	null,
		"LastDuration"				=>	0,
		"Duration"					=>	0,
		"QueryCount"				=>	0,
		"QueryHistory"				=>	[],
		"Table"						=>	[],
    ];

	#region Variable
	//private $Connection = null;
	#endregion Variable

    #region Method
    public function __construct($Type = null, $Host = null, $User = null, $Password = null, $Name = null, $ODBCDriver = null, $TablePrefix = null, $Timezone = null, $Encoding = null, $Strict = null, $Verbose = null, $ErrorLogPath = null, $IgnoreQueryError = null){
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
				$this->Property["Connection"] = new \PDO("mysql:host={$this->Property["Host"]};dbname={$this->Property["Name"]};charset={$this->Property["Encoding"]}", $this->Property["User"], $this->Property["Password"], array_filter([
    				\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
					//\PDO::ATTR_EMULATE_PREPARES => false, //! This triggers false error with SET XXXX statements
					\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,					
					//\PDO::ATTR_TIMEOUT => 300, //! Not reliable; policy deffers for MySQL/MySQLND & underlying PHP compilation
					\PDO::MYSQL_ATTR_COMPRESS  => !in_array(strtoupper($this->Property["Host"]), ["LOCALHOST", "127.0.0.1", "::1", $_SERVER["LOCAL_ADDR"], ]), // Compress connection content for remote hosts
					\PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '{$this->Property["Timezone"]}'",
					\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY  => true, 
				]));

				//! Following does not work with PDO::ATTR_EMULATE_PREPARES = false; neither could be appended with MYSQL_ATTR_INIT_COMMAND
				if($this->Property["Strict"])$this->Query("SET sql_mode = 'STRICT_ALL_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO'", null, null, null, true);

				if($this->Property["Transactional"])$this->Property["Connection"]->beginTransaction(); // Encapsulate execution within transaction
				
				if($this->Property["Name"]){ // Get table information
					$Tables = $this->Query("SHOW TABLES", null, null, null, true);
					foreach(is_array($Tables) ? $Tables[0] : [] as $Table)$this->Property["Table"][] = $Table[array_keys($Table)[0]];
				}

				$Result = $this->Property["Connection"];
			}
			catch(\Throwable $Exception){ // Connection error
				$this->LogError( // Log and show error message but don't trigger any error so the User can use Custom settings to update database connection
					"Connection failed: {$Exception->getMessage()}", // Error message
					null, // We don't have any SQL to log
					[ // Parameters to log
						//$Key = "Host" => $this->Property["{$Key}"], 
						//$Key = "User" => $this->Property["{$Key}"], 
						//$Key = "Name" => $this->Property["{$Key}"], 
						//$Key = "Password" => "*****", 
					], 
					true // Show error message
				);

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
		if($this->Property["Connection"]){ // Connection exists
			if($this->Property["Transactional"])$this->Property["Connection"]->commit(); // Encapsulate execution within transaction
			$this->Property["Connection"] = null; // Close connection
		}

		return true;
	}

	/*
		Argument
			SQL: SQL statement to execute
			Parameter: Query parameters to pass to prepare the statement with
			Verbose: Display query information on demand
			NoHistory: TRUE = Does not keep query informatio history; Keeps otherwise
			IgnoreError: Default = TRUE = Does not trigger PHP fatal error on query execution error; Error log is still generated

		Return value
			Array of full or partial recordset(s) when query is successfully executed with at least one statement
			Explicit FALSE upon connection or complete query execution error (no statement could be executed)
			Returned recordsets may vary in number, depending on how many SQL statements could be executed before an error occurred

		Note
			Return type Array does not necessarily mean the query execution went without error entirely.
			Empty or partial Array of recordset(s) may be generated even if error is encountered during query execution.
			This happens especially with multi query where error is encountered after at least one successful statement execution.
			
			An empty Array also evaluates to FALSE. So do not just rely on $Database->Query() method's return value only.
			Use explicit comparison with !== FALSE or is_array() with optional count() of recordset to determine accurate expectation from the query.
	*/
	public function Query($SQL, $Parameter = null, $Verbose = null, $NoHistory = null, $IgnoreError = null){ //DebugDump($SQL);
		#region Set default argument values
		if(is_null($Parameter))$Parameter = [];
		if(is_null($Verbose))$Verbose = $this->Property["Verbose"];
		if(is_null($NoHistory))$NoHistory = !$this->Property["KeepQueryHistory"];
		if(is_null($IgnoreError))$IgnoreError = $this->Property["IgnoreQueryError"];
		#endregion Set default argument values

		if($this->Property["Connection"]){ // Process the query
			if($Verbose)$this->ShowMessage("Initiating query execution.", $SQL, $Parameter);
	
			$LastDurationStart = microtime(true);

			#region Try executing the query
			$Result = []; // Initialize Result as an Array to hold the recordset(s)
			$Query = $this->Property["Connection"]->prepare($SQL); // Does not throw error when PDO::ATTR_EMULATE_PREPARES = false

			try{
				$Query->execute($Parameter);
			}
			catch(\Throwable $Exception){
				$Result = false; // Return explicit FALSE as no statement could be executed successfully
				$this->LogError($Exception->getMessage(), $SQL, $Parameter, true);
				if(!$IgnoreError)trigger_error($Exception->getMessage(), E_USER_ERROR);
			}
			#endregion Try executing the query
			
			#region Generate resulting recordset(s)
			if($Result !== false)do{ // Generate mutiple recordset
				try{ // Get current recordset by TRYing as this might result into a GENERAL ERROR which appears to be a false positive
					$Dataset = $Query->fetchAll(); //DebugDump($Dataset);
					if($Dataset)$Result[] = $Dataset; // Take valid recordset only; Failed to find a reliable & generic way to filter out non SELECT recordset only
				}catch(\Throwable $Exception){} // Is there really anything to do here!

				// Special thanks to Saiful Islam for the tricky flow control below
				try{ // Advance the recordset pointer to the next available
					$RecordsetLeft = $Query->nextRowset();
				}
				catch(\Throwable $Exception){ //DebugDump($Exception->errorInfo);
					$RecordsetLeft = false; // Somehow the system sets the end value to FALSE and is able exit the DO WHILE loop! But this is phishy!
					$this->LogError($Exception->getMessage(), $SQL, $Parameter, true);
					if(!$IgnoreError)trigger_error($Exception->getMessage(), E_USER_ERROR);
				}
			}while($RecordsetLeft !== false); //DebugDump($RecordsetLeft);
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
		}
		else{ // No database connection
			$Result = false; // Return explicit FALSE on connection error
			$this->LogError("No database connection", $SQL, $Parameter, true); // Show error message but do not throw Exception
		} //DebugDump($Result);
		
		$this->Property["Recordset"] = $Result; // Update Recordset property with the generated result set

		return $Result;
	}

	public function Escape($Value){
		return str_replace(str_split("'\\"), str_split("''\\\\", 2), $Value);
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
		$Value = strtolower(str_replace("-", "", $Value)); // Adapt to MySQL syntax
		if($Value == "utf8")$Value = "utf8mb4"; // Fix for MySQL utf8 alias

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

	public function IgnoreQueryError($Value = null){
		if(is_null($Value))return $this->Property[__FUNCTION__];
		$this->Property[__FUNCTION__] = $Value;
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
	private function LogError($Message, $SQL, $Parameter, $Verbose){
		#region Call trace
		$DebugCallStack = debug_backtrace();
		array_shift($DebugCallStack); // Remove self
		
		#region Filter out framework base calls
		array_pop($DebugCallStack); // sPHP\Application->__destruct()
		if(substr($DebugCallStack[$DebugCallIndex = count($DebugCallStack) - 1]["file"], strlen($DebugCallStack[$DebugCallIndex]["file"]) - strlen($Keyword = "\class.php")) == $Keyword && $DebugCallStack[$DebugCallIndex]["function"] == "sPHP\___ExecuteApplicationScript")array_pop($DebugCallStack);
		if(substr($DebugCallStack[$DebugCallIndex = count($DebugCallStack) - 1]["file"], strlen($DebugCallStack[$DebugCallIndex]["file"]) - strlen($Keyword = "\private_function.php")) == $Keyword && $DebugCallStack[$DebugCallIndex]["function"] == "require")array_pop($DebugCallStack);
		#endregion Filter out framework base calls
		#endregion Call trace

		if($this->Property["ErrorLogPath"])file_put_contents( // Write error information to log file
			"{$this->Property["ErrorLogPath"]}database.json",
			json_encode([
				"Error" => [
					"Code" => null,
					"Message" => $Message,
				],
				"Time" => date("r"),
				"Argument" => [
					"SQL" => $SQL,
					"Parameter" => $Parameter,
				],
				"Callstack" => $DebugCallStack,
			])
		);

		if($Verbose)$this->ShowMessage($Message, $SQL, $Parameter, $DebugCallStack);

		return true;
	}

	private function ShowMessage($Message = null, $SQL = null, $Parameter = null, $CallStack = []){
		if(!is_array($Parameter))$Parameter = [$Parameter];

		foreach($Parameter as $Key => $Value)$ParameterHTML[] = "{$Key}: {$Value}";		

		$CallCount = count($CallStack);

		foreach($CallStack as $CallIndex => $Call){
			if(isset($Call["file"])){
				$CallFile = $Call["file"];

				if(isset(\sPHP::$Environment)){
					$Path = \sPHP::$Environment->Path();
					$SystemPath = \sPHP::$Environment->SystemPath();

					if(substr($CallFile, 0, strlen($Path)) == $Path)$CallFile = "APP" . DIRECTORY_SEPARATOR . substr($CallFile, strlen($Path));
					if(substr($CallFile, 0, strlen($SystemPath)) == $SystemPath)$CallFile = "sPHP" . DIRECTORY_SEPARATOR . substr($CallFile, strlen($SystemPath));
				}
			}
			else{
				$CallFile = null;
			}

			$CallStackHTML[] = "	" . ($CallCount - $CallIndex) . ". " . ($CallFile ? "{$CallFile}:{$Call["line"]} &gt; " : null) . (isset($Call["class"]) ? "{$Call["class"]}{$Call["type"]}" : null) . "{$Call["function"]}()";		
		}

		$sPHPEligible = false && isset(\sPHP::$User) && (\sPHP::$User->UserGroupIdentifierHighest() == "ADMINISTRATOR" || \sPHP::$Session->DebugMode());

		print \sPHP\HTML\UI\MessageBox(
			implode("<br><br>", array_filter([
				$Message ? $Message : null, 
				count($CallStack) ? "<code>Call stack" . PHP_EOL . implode(PHP_EOL, $CallStackHTML) . "</code>" : null, 
				count($Parameter) && $sPHPEligible ? "<ul><li>" . implode("</li><li>", $ParameterHTML) . "</li></ul>" : null, 
				$SQL && $sPHPEligible ? "<code>{$SQL}</code>" : null, 
			])), 
			"Database", 
			"MessageBoxError"
		);

		return true;
	}
	#endregion Function
}
?>