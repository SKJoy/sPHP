<?php
namespace sPHP\Database\PDO;

class MySQL{
	private $Property = [ // Property storage
		#region Regular
		"User"			=>	null, 
		"Password"		=>	null, 
		"Host"			=>	"LocalHost", 
		"Database"		=>	null, 
		"Timezone"		=>	"+00:00", 
		"Strict"		=>	true, 
		"Characterset"	=>	"utf8mb4", 
		#endregion Regular

		#region Read only
		"Connection"	=>	false, 
		#endregion Read only
	];	
	
	#region Method
	public function __construct(?string $User = null, ?string $Password = null, ?string $Host = null, ?string $Database = null, ?string $Timezone = null, ?bool $Strict = null, ?string $Characterset = null){
		foreach(get_defined_vars() as $Property => $Value)if(!is_null($Value) && method_exists($this, $Property))$this->Property[$Property] = $Value;
	}

	public function __destruct(){
		$this->Disconnect();
	}

	public function VarDump(){
		foreach(func_get_args() as $Name => $Value){
			print "<pre style=\"display: inline-block; margin: 1em; box-shadow: 0 0 5px Grey; border-radius: 5px; border: 5px White solid; background-color: Black; padding: 0.5em 1em; color: White; font-family: Consolas, Verdana, Tahoma, Arial; font-size: 20px; line-height: 1.62; vertical-align: top;\">";
			var_dump($Value);

			#region Debug backtrace
			$BackTrace = debug_backtrace();
			array_shift($BackTrace);
			$CallCount = count($BackTrace);
			
			if($CallCount){				
				print "<div style=\"border: 1px Orange solid; padding: 0.5em 1em;\"><div style=\"border-bottom: 1px Ornage solid; color: Yellow; font-weight: bold;\">Call trace</div>";
				foreach($BackTrace as $CallerIndex => $Caller)print "<div style=\"margin-left: 2em;\">" . ($CallCount - $CallerIndex) . ". {$Caller["file"]}:{$Caller["line"]} {$Caller["class"]}{$Caller["type"]}{$Caller["function"]}()</div>";
				print "</div>";
			}
			#endregion Debug backtrace

			print "</pre>";
		}		
	}

	public function Connect(?string $User = null, ?string $Password = null, ?string $Host = null, ?string $Database = null, ?string $Timezone = null, ?bool $Strict = null, ?string $Characterset = null, ?bool $Verbose = false){
		#region Validate arguments
		#region Get connection parameters from object properties if not supplied with
		if(is_null($User))$User = $this->Property["User"];
		if(is_null($Password))$Password = $this->Property["Password"];
		if(is_null($Host))$Host = $this->Property["Host"];
		if(is_null($Database))$Database = $this->Property["Database"];
		if(is_null($Timezone))$Timezone = $this->Property["Timezone"];
		if(is_null($Strict))$Strict = $this->Property["Strict"];
		if(is_null($Characterset))$Characterset = $this->Property["Characterset"];
		#endregion Get connection parameters from object properties if not supplied with

		if(is_null($Verbose))$Verbose = false;
		#endregion Validate arguments

		$this->Disconnect(); // Make sure to close any previous connection

		//if($Verbose)$this->VarDump("Connecting {$User}" . ($Password ? ":*****" : null) . "@{$Host}; Timezone = {$Timezone}; Strict = " . ($Strict ? "Yes" : "No") . "");

		try{
			$this->Property["Connection"] = new \PDO("mysql:host={$Host};dbname={$Database};charset={$Characterset}", $User, $Password, [
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
				\PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '{$Timezone}'",
				//\PDO::MYSQL_ATTR_COMPRESS  => true,
				//\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY  => true,
			]);

			if($Strict)$this->Query("SET sql_mode = 'STRICT_ALL_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO'");
		}catch(\Throwable $Exception){
			$this->Property["Connection"] = false; 
			//$this->VarDump($Exception->getMessage());
			throw new \Exception($Exception->getMessage());
		}
	
		return $this->Property["Connection"];
	}
	
	public function Disconnect(){
		$this->Property["Connection"] = false; // Close connection
	}
	
	public function Query(string $SQL, ?array $Parameter = [], ?bool $Verbose = false, ?bool $IgnoreError = false){
		if(is_null($Parameter))$Parameter = [];
		if(is_null($Verbose))$Verbose = false;
		if(is_null($IgnoreError))$IgnoreError = false;

		//if($Verbose)$this->VarDump($SQL);
	
		$Result = []; // Assume we have an empty recordset
	
		if($this->Property["Connection"]){		
			try{		
				$Query = $this->Property["Connection"]->prepare($SQL);
				
				if($Query->execute($Parameter))do{
					try{ // Take care of SQLSTATE[HY000]: General error
						$Recordset = $Query->fetchAll();
					}catch(\Throwable $Exception){
						$Recordset = false;
						//$this->VarDump($Exception->getMessage());
						throw new \Exception($Exception->getMessage());
					}

					if($Recordset !== false){
						//if($Recordset)$Result[] = $Recordset;
						$Result[] = $Recordset ? $Recordset : [];
					}
				}while($Query->nextRowset());
			}catch(\Throwable $Exception){
				if(!$IgnoreError){
					$Result = false; 
					//$this->VarDump($Exception->getMessage());
					throw new \Exception($Exception->getMessage());
				}
			}
		}
		else{
			if(!$IgnoreError){
				$Result = false; 
				//$this->VarDump("No database connection");
				throw new \Exception("No database connection");
			}
		}
	
		return $Result;
	}
	#endregion Method

	#region Property
	public function User($Value = null){
		if(is_null($Value))return $this->Property[__FUNCTION__];
		$this->Property[__FUNCTION__] = $Value;
		$this->Disconnect();
	}
	
	public function Password($Value = null){
		if(is_null($Value))return $this->Property[__FUNCTION__];
		$this->Property[__FUNCTION__] = $Value;
		$this->Disconnect();
	}
	
	public function Host($Value = null){
		if(is_null($Value))return $this->Property[__FUNCTION__];
		$this->Property[__FUNCTION__] = $Value;
		$this->Disconnect();
	}

	public function Database($Value = null){
		if(is_null($Value))return $this->Property[__FUNCTION__];
		$this->Property[__FUNCTION__] = $Value;
	}

	public function Timezone($Value = null){
		if(is_null($Value))return $this->Property[__FUNCTION__];
		$this->Property[__FUNCTION__] = $Value;
	}

	public function Strict($Value = null){
		if(is_null($Value))return $this->Property[__FUNCTION__];
		$this->Property[__FUNCTION__] = $Value;
	}

	public function Characterset($Value = null){
		if(is_null($Value))return $this->Property[__FUNCTION__];
		$this->Property[__FUNCTION__] = $Value;
	}
	#endregion Property
}
?>