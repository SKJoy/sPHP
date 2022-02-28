<?php
/*
	Foundation for generic object with extended feature set
		Feature
			Property function		= Property can be accessed for GET/SET through both direct & method fashion
				GET
					$MyObject->MyProperty					// Get by name
					$MyObject->MyProperty()					// Get by method
				SET
					$MyObject->MyProperty = "My Value"		// Set by name (assignment)
					$MyObject->MyProperty("My Value")		// Set by method
			Read only property		= Define read only property to prevent accidental property value set
			Dependant property		= Define property that is to be automatically reset when another property changes
		Method
			_WriteLog($Message)		= Show console messages or log to file like JavaScript 'console.log()'
		Callback function
			_OnGet($Name)			= When a property is read, returning FALSE cancels the operation
			_OnSet($Name, $Value)	= When a property is set, returning FALSE cancels the operation
		Usage
			Create a class extending the 'ObjectBase' class
				class MyObject extends \sPHP\ObjectBase{}
			Instantiate 'MyObject' & customize through constructor method
				$MyObject = new MyObject
			Create custom property method
			Execute
				$MyObject->_WriteLog("This is my object with extended functionality")
		Example
			Check 'objectbase-example.php' script
*/

namespace sPHP;

abstract class ObjectBase1{
	protected $Property = [ // Property store
		"_OnGet"	=> null, 
		"_OnSet"	=> null, 
	];

	protected $ReadOnlyProperty = []; // List of read only properties
	protected $DependantProperty = []; // List of properties depend on other properties

	#region Method
	#region System
	protected function __construct(array $Property){
		foreach($Property as $PropertyName => $PropertyValue){ // Check for each property from argument
			//! Do not try to sum up the following condition block; it won't work
			if(is_null($PropertyValue)){ // No property value defined
				if(!isset($this->Property[$PropertyName])){ // Property not set yet (property can be set from child constructor before this constructor)
					$this->Property[$PropertyName] = null; // Just create the property for further external use !!! DO NOT PUT IT IN next ELSE section
				}
			}else{ // We have some value to define to the property
				$this->Property[$PropertyName] = $PropertyValue; //* Set property value from argument
			}
		}

		//var_dump($this->Property); exit;
	}

	public function __get(string $PropertyName){ // Get property value from property store
		if(is_null($this->Property["_OnGet"]) || $this->Property["_OnGet"]($PropertyName) !== false){
			if(!isset($this->Property[$PropertyName]))$this->Property[$PropertyName] = null; // Set property if not exists

			if(method_exists($this, $PropertyName)){ // Property method found
				$Result = $this->$PropertyName(); // Get value from property method
			}
			else{
				$Result = $this->Property[$PropertyName]; // Get value from property store
			}
		}
		else{ //! Callback function prevented the operation
			$Result = null; // Return UNKNOWN state
		}

		return $Result;
	}
	
	public function __set(string $PropertyName, $PropertyValue){ // Set property value to property store
		if(in_array($PropertyName, $this->ReadOnlyProperty)){ //! Property is in read only property list, cannot set
			$Backtrace = debug_backtrace();			
			$Caller = $Backtrace[0];
			if($Caller["class"] == __CLASS__ && $Caller["function"] == __FUNCTION__)$Caller = $Backtrace[1]; //? Get outside caller if called by $this->__call()

			throw new Exception("Cannot set read only property '{$PropertyName}' in {$Caller["file"]}:{$Caller["line"]}");
		}
		else{
			if(is_null($this->Property["_OnSet"]) || $this->Property["_OnSet"]($PropertyName, $PropertyValue) !== false){
				if(isset($this->DependantProperty[$PropertyName])){ // Found property that depends on this property
					foreach($this->DependantProperty[$PropertyName] as $DependantProperty){ // Check for each dependant property
						$this->Property[$DependantProperty] = null; // Reset dependant property value
					}
				}
	
				if(method_exists($this, $PropertyName)){ // Property method found
					$this->$PropertyName($PropertyValue); // Get value from property method
				}
				else{
					$this->Property[$PropertyName] = $PropertyValue; // Set property value to property store
				}

				$Result = true;
			}
			else{ //! Callback function prevented the operation
				$Result = false; // Return operation failed
			}

			return $Result;
		}
	}

	public function __call(string $PropertyName, array $Argument){ // Get/set property value with virtual method
		$PropertyValue = isset($Argument[0]) ? $Argument[0] : null; // Get property value from the first argument value; otherwise GET mode

		if(is_null($PropertyValue)){ // Get
			return $this->__get($PropertyName);
		}
		else{ // Set
			$this->__set($PropertyName, $PropertyValue);
		}
	}

	protected function __destruct(){
		
	}
	#endregion System

	protected function SetReadOnlyProperty(string|array $PropertyName){ // Add/replace read only properties to the read only property list
		if(is_array($PropertyName)){
			$this->ReadOnlyProperty = $PropertyName; // Replace read only property list with the list provided as argument
		}
		else{
			$this->ReadOnlyProperty[] = $PropertyName; // Add property name to read only property list
		}
	}

	protected function SetDependantProperty(string $DependantPropertyName, string|array $PropertyName){ // Add/replace read only properties to the read only property list
		if(is_array($PropertyName)){
			foreach($PropertyName as $ThisPropertyName)$this->DependantProperty[$ThisPropertyName][] = $DependantPropertyName;
		}
		else{
			$this->DependantProperty[$PropertyName][] = $DependantPropertyName;
		}
	}
	#endregion Method
}

class ObjectBase1Helper_Log extends ObjectBase1{
	static $MessageCounter = 0; // Keep track of global message count

	#region Method
	#region System
	public function __construct(?bool $ShowSource = null, ?int $SourceDepth = null, ?string $File = null, ?int $FileSizeLimitByte = null){
		#region Set default property value
		$this->Property["ShowSource"] = true;
		$this->Property["SourceDepth"] = 0;
		$this->Property["FileSizeLimitByte"] = 1024 * 1024 * 10; // N MB
		#endregion Set default property value

		parent::__construct(get_defined_vars()); // Call parent constructor with arguments provided with

		#region Configuration
		//parent::SetReadOnlyProperty(["Name", ]); // Set read only property list to prevent accidental property setting
		//parent::SetDependantProperty("Name", ["FirstName", "LastName"]); // Set dependant property that gets reset by other properties
		#endregion Configuration
	}

	public function __destruct(){
		parent::__destruct(); // Call parent destructor upon destruction
	}
	#endregion System

	public function Write(string $Message, ?bool $OnConsole = null, ?bool $InFile = null, ?int $SourceDepth = null){ // Output message
		#region Determine default argument value
		if(is_null($OnConsole))$OnConsole = true;
		if(is_null($InFile))$InFile = $this->Property["File"] ? true : false; //* Default is write in log if log file is provided with
		if(is_null($SourceDepth))$SourceDepth = $this->Property["SourceDepth"];
		#endregion Determine default argument value

		$this->MessageCounter = $this->MessageCounter + 1; //? Increase global message counter

		if($this->Property["ShowSource"]){ // Determine source of log message
			$Backtrace = debug_backtrace();
			$Backtrace = isset($Backtrace[$SourceDepth]) ? $Backtrace[$SourceDepth] : $Backtrace[0]; //? Adjust debug level by depth

			$SourceFile = $Backtrace["file"];
			$SourceLine = $Backtrace["line"];
			$Source = "{$SourceFile}:{$SourceLine} ▶ "; // CLASS & FUNCTION will always be of SELF, so including them is meaningless
		}
		else{ // Source not needed
			$SourceFile = null;
			$SourceLine = null;
			$Source = null;
		}

		$TimeMessage = date("[Y-m-d H:i:s] ");
		$CLIMessage = $TimeMessage . $Source . $Message . PHP_EOL; //* Plain text message

		if($InFile && $this->Property["File"]){ // Log in file & log file is provided with
			if( // Check log file
					file_exists($this->Property["File"]) // Log file found
				&&	filesize($this->Property["File"]) > $this->Property["FileSizeLimitByte"] //! Log file size exceeds limit
			)unlink($this->Property["File"]); //? Remove log file to start with a new one

			file_put_contents($this->Property["File"], $CLIMessage); //? Write message to log file
		}

		if($OnConsole){ // Show log message on console
			if(php_sapi_name() == "cli"){ // Console
				print $CLIMessage;
			}
			else{ // Other; typically the browser
				print "<div style=\"display: inline-block; margin: 0.5em; box-sizing: border-box; box-shadow: 0 0 3px 0 black; border-radius: 3px; background-color: #FFFFAF; padding: 0.25em 0.5em; color: Black; font-family: Consolas, Courier New, Monospace, Verdana, Tahoma, Aria; font-size: 14px; line-height: 1.62; text-shadow: -1px -1px White;\"><span style=\"color: Maroon; font-weight: bold; cursor: pointer;\" title=\"Close\" onclick=\"this.parentElement.style.display = 'none';\">⛿ {$this->MessageCounter}</span> {$TimeMessage}<a href=\"vscode://file/{$SourceFile}:{$SourceLine}\" style=\"color: Blue; text-decoration: none;\">{$Source}</a>{$Message}</div>";
			}
		}
	}
	#endregion Method

	#region Property

	#region Read only property

	#endregion Read only property
	#endregion Property

	#region Private function
	
	#endregion Private function
};

abstract class ObjectBase extends ObjectBase1{
	#region Method
	#region System
	public function __construct(array $Property){
		#region Set default property value
		$this->Property["_Log"] = new ObjectBase1Helper_Log();
		#endregion Set default property value

		parent::__construct($Property); // Call parent constructor with arguments provided with
	}

	public function __destruct(){
		parent::__destruct(); // Call parent destructor upon destruction
	}
	#endregion System

	public function _WriteLog(string $Message, ?bool $OnConsole = null, ?bool $InFile = null){
		$this->Property["_Log"]->Write($Message, $OnConsole, $InFile, 1);
	}
	#endregion Method

	#region Property

	#region Read only property

	#endregion Read only property
	#endregion Property

	#region Private function
	
	#endregion Private function
};
?>