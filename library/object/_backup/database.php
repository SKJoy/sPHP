<?php
/*
    Name:           Database
    Purpose:        Database object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 12, 2018 10:51 AM
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
		"Encoding"					=>	"UTF8",
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
		//var_dump("Trying connecting to database..."); exit;
		if($this->Property["Type"] == DATABASE_TYPE_MYSQL){
			try{
				$this->Property["Connection"] = new \PDO("mysql:host={$this->Property["Host"]};dbname={$this->Property["Name"]}", $this->Property["User"], $this->Property["Password"], [
                    //\PDO::ATTR_TIMEOUT => 300,
                ]);
			}
			catch(\Throwable $Exception){
				//file_put_contents(__DIR__ . "/error.log", "Database connection attempt failed! {$Exception->getMessage()}");
				trigger_error($Exception->getMessage(), E_USER_ERROR);

				/*
				print "" . HTML\UI\MessageBox(
					"
						Couldn't connect to the database!<br>
						<br>
						{$Exception->getMessage()}
					",
					"Database",
					"MessageBoxError"
				) . "";
				*/
			}

			if(!is_null($this->Property["Connection"])){
				$this->Property["Connection"]->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

				$this->Query("
					SET NAMES " . strtolower(str_replace("-", null, $this->Property["Encoding"])) . ";
					SET time_zone = '{$this->Property["Timezone"]}';
					SET sql_mode = " . ($this->Property["Strict"] ? "STRICT_ALL_TABLES" : '') . ";
				");

				if($this->Property["Transactional"])$this->Property["Connection"]->beginTransaction(); // Encapsulate execution within transaction
				//var_dump($this->Query("SHOW TABLES"), $this->Query("SHOW TABLES")[0]);
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

	public function Query($SQL, $Parameter = null, $Verbose = null, $NoHistory = null, $IgnoreError = false){
		if(is_null($NoHistory))$NoHistory = !$this->Property["KeepQueryHistory"];
		if(is_null($Verbose))$Verbose = $this->Property["Verbose"];

		//var_dump($SQL);
		if($Verbose)print "<div class=\"MessageBox\"><div class=\"Container\"><div class=\"Title\">sPHP\Database-&gt;Query</div><div class=\"Content\"><pre class=\"Code\">" . trim($SQL) . "</pre></div></div></div>";

		if(is_null($this->Property["Connection"])){
			if($Verbose)print "<div class=\"MessageBox\"><div class=\"Container\"><div class=\"Title\">sPHP\Database-&gt;" . __FUNCTION__ . "</div><div class=\"Content\">No database connection!</div></div></div>";
			$Result = false;
			//file_put_contents(__DIR__ . "/error.log", "No database connection!");
		}
		else{
			if(($Query = $this->Property["Connection"]->prepare($SQL)) === false){ // Database server failed to prepare the statement
				if($Verbose)print "<div class=\"MessageBox\"><div class=\"Container\"><div class=\"Title\">sPHP\Database-&gt;" . __FUNCTION__ . "</div><div class=\"Content\">Statement preparation failed!</div></div></div>";
				$Result = false;
				//file_put_contents(__DIR__ . "/error.log", "Database statement preparation failed!");
			}
			else{ // Database server successully prepared the statment
				$LastDurationStart = microtime(true);

				try{
					$Query->execute(is_null($Parameter) ? [] : $Parameter);
				}
				catch(\Throwable $Exception){
					//var_dump($SQL);
					$Result = false;
					$ErrorMessage = $Exception->getMessage();

					if($this->Property["ErrorLogPath"]){
						$DebugCallStack = debug_backtrace();
						array_pop($DebugCallStack);
						array_pop($DebugCallStack);
						array_pop($DebugCallStack);

						file_put_contents(
							"{$this->Property["ErrorLogPath"]}database.json",
							json_encode([
								"Error" => [
									"Code" => null,
									"Message" => $ErrorMessage,
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
					}

					if(!$IgnoreError)trigger_error($ErrorMessage, E_USER_ERROR);
				}

				// Temporarily suspend error exception
				$this->Property["Connection"]->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);

				do{// Generate mutiple recordset
					if($Dataset = $Query->fetchAll(\PDO::FETCH_ASSOC))$Recordset[] = $Dataset;
				}while($Query->nextRowset());
				//var_dump($Recordset);
				// Enable error exception back
				$this->Property["Connection"]->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

				// Set query counters
				$this->Property["LastDuration"] = microtime(true) - $LastDurationStart;
				$this->Property["Duration"] = $this->Property["Duration"] + $this->Property["LastDuration"];
				$this->Property["QueryCount"]++;

				#region Detect query error from query status
				$QueryError = $Query->errorInfo();
				
				if($QueryError[0] != "00000"){ // Throw query error exception
					$this->Property["Recordset"] = [];
					$Result = false;

					if($this->Property["ErrorLogPath"]){
						$DebugCallStack = debug_backtrace();
						array_pop($DebugCallStack);
						array_pop($DebugCallStack);
						array_pop($DebugCallStack);

						file_put_contents(
							"{$this->Property["ErrorLogPath"]}database.json",
							json_encode([
								"Error" => [
									"Code" => null,
									"Message" => $QueryError[2],
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
					}
					
					if(!$IgnoreError)trigger_error($QueryError[2], E_USER_ERROR);
				}
				else{
					if(isset($Recordset)){
						$Result = $this->Property["Recordset"] = $Recordset;
					}
					else{
						$this->Property["Recordset"] = [];
						$Result = true;
					}
				}
				#endregion Detect query error from query status

				// Add query to history
				if(!$NoHistory)$this->Property["QueryHistory"][] = ["SQL" => $SQL, "Parameter" => $Parameter, "Duration" => $this->Property["LastDuration"], "Result" => $Result];
			}
		}
		//var_dump($Result);
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

    public function Encoding($Value = null){
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
}
?>