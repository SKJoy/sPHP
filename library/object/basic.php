<?php
namespace sPHP;

class Basic{
	#region Property
	protected $Property = []; //* Internal property storage
	protected $ReadOnlyProperty = []; //* Properties that cannot be set from outside the class
	protected $PropertyDependancy = []; //* Properties that affect/reset other properties when set/changed
	protected $Debug = false; //? Set to TRUE for testing execution flow
	#endregion Property

	#region Method
	#region System
	public function __construct(){ //? Pass arguments as associative array from child class in order to determine property name
		$Argument = func_get_args();
		$Argument = count($Argument) ? $Argument[0] : [];

		$this->DebugLogCall("Argument: " . json_encode($Argument) ."");
		foreach($Argument as $Property => $Value)$this->__set($Property, $Value); // Set initial property values upon object instantiation
		$Result = true;

		return $Result;
	}

	public function __destruct(){ $this->DebugLogCall();
		$Result = true;
		
		return $Result;
	}

	public function __set($Property, $Value){ $this->DebugLogCall("{$Property}: " . json_encode($Value));
		if(in_array($Property, $this->ReadOnlyProperty)){ // Property is read only, cannot be set outside the class
			$Result = false;
		}
		else{ // Regular property
			if(isset($this->PropertyDependancy[$Property])){ // Reset dependant property value that depend on property to be set
				foreach($this->PropertyDependancy[$Property] as $DependantProperty){ $this->DebugLogCall($DependantProperty);
					$this->Property[$DependantProperty] = null;
				}
			}
			
			if(method_exists($this, $Property)){ // Set property value through class method
				$Result = $this->$Property($Value);
			}
			else{
				$this->Property[$Property] = $Value; // Set property value through internal property storage
				$Result = true;
			}
		}

		return $Result;
	}
	
	public function __get($Property){ $this->DebugLogCall($Property);
		if(!isset($this->Property[$Property]))$this->Property[$Property] = null; // Enure the propery exists in internal propery storafge

		if(method_exists($this, $Property)){ // Property value comes from class method
			$Result = $this->$Property();
		}
		else{ // Property value comes from internal property storage
			$Result = $this->Property[$Property];
		}
		
		return $Result;
	}

	public function __call($Property, $Argument){ $this->DebugLogCall("{$Property}, " . json_encode($Argument) . ""); // Assume a property is GET/SET as a method
		if(count($Argument)){ // Set property value
			$Result = $this->__set($Property, $Argument[0]);
		}
		else{ // Get property value
			$Result = $this->__get($Property);
		}
		
		return $Result;
	}
	#endregion System

	/*
		Globally callable static function for debug log output
			Argument:	$FunctionComment: Optional comment with the log output
							Example: String: This is just a comment for this debug log output
		Usage: sPHP\Basic:DebugLog("Some comment goes here");
	*/static function DebugLog(?string $FunctionComment = null){
		$Caller = isset(debug_backtrace()[3]) ? debug_backtrace()[2] : false; //var_dump($Call);
		if($Caller)print "" . (isset($Caller["file"]) ? "{$Caller["file"]}:{$Caller["line"]} ▶ " : null) . "{$Caller["class"]}{$Caller["type"]}{$Caller["function"]}() ▷ ";

		$Call = debug_backtrace()[2]; //var_dump($Call);
		print "{$Call["file"]}:{$Call["line"]} ▶ {$Call["class"]}{$Call["type"]}{$Call["function"]}({$FunctionComment})";
		
		print PHP_EOL;
		$Result = true;

		return $Result;
	}
	#endregion Method

	#region Property
	#endregion Property

	#region Function
	/*
		Specify properties that are read only from outside the class. 
		Possible case is FullName cannot be set directly but generated when FirtName & LastName are set.
			Argument:	$Property: COMMA delimitted string of property name list, or an array of property names
							Example:	String: FirstName, LastName, MiddleName
										Array: ["FirstName", "LastName", "MiddleName", ]
	*/protected function SetReadOnlyProperty($Proeprty){
		if(!is_array($Proeprty))$Proeprty = array_filter(explode(",", str_replace(" ", "", $Proeprty)));

		if(count($Proeprty)){			
			$this->ReadOnlyProperty = $Proeprty;
			$Result = true;
		}
		else{
			$Result = false;
		}

		return $Result;
	}

	/*
		Specify properties that affect/reset other properties when set/changed. 
		When the property is set/changed, the dependant properties are reset to NULL.
			Argument:	$Property: Name of the property
							Example: String: FirstName
						$Dependancy: Array of proerties that get affected/reset
							Example: Array: ["LastName", "MiddleName", ]
	*/protected function AddPropertyDependancy(string $Property, array $Dependancy){
		if(count($Dependancy)){
			$this->PropertyDependancy[$Property] = $Dependancy;
			$Result = true;
		}
		else{
			$Result = false;
		}

		return $Result;
	}

	// Just a wrapper function for DebugLog() to output only when $this->Debug is TRUE to use internally
	protected function DebugLogCall(?string $FunctionComment = null){
		if($this->Debug){			
			$Result = self::DebugLog($FunctionComment);
		}
		else{
			$Result = false;
		}

		return $Result;
	}
	#endregion Function
}

class My extends Basic{
	#region Method
	#region System
	public function __construct(?string $FirstName, ?string $LastName, ?string $Email, ?string $Comment){
		parent::{__FUNCTION__}([ // Set initial property values upon object instantiation
			"FirstName"	=>	$FirstName, 
			"LastName"	=>	$LastName, 
			"Email"		=>	$Email, 
			"Comment"	=>	$Comment, 
		]);

		#region Property dependancy
		//* Add properties that will reset other properties when set/changed
		parent::AddPropertyDependancy("FirstName", ["Name", "Caption", ]); //? Name & Caption will be reset when FirstName changes
		parent::AddPropertyDependancy("LastName", ["Name", "Caption", ]); //? Keep adding dependant properties as array elements
		#endregion Property dependancy
	}
	
	public function __destruct(){
		parent::{__FUNCTION__}();
	}
	#endregion System
	#endregion Method

	#region Property
	#region Read only
	public function Name(){ parent::DebugLogCall();
		if(is_null($this->Property[__FUNCTION__])){ // Generate property value if not set
			$this->Property[__FUNCTION__] = "{$this->Property["FirstName"]} {$this->Property["LastName"]}";
		}

		return $this->Property[__FUNCTION__];
	}

	public function Caption(){ parent::DebugLogCall();
		if(is_null($this->Property[__FUNCTION__])){ // Generate property value if not set
			$this->Property[__FUNCTION__] = "{$this->Property["Name"]} [{$this->Property["Email"]}]";
		}

		return $this->Property[__FUNCTION__];
	}
	#endregion Read only

	public function Comment($Value = null){ parent::DebugLogCall($Value);
		if(is_null($Value)){ // Get
			$Result = $this->Property[__FUNCTION__];
		}
		else{ // Set
			$this->Property[__FUNCTION__] = $Value;
			$Result = true;
		}

		return $Result;
	}
	#endregion Property
}

//$My = new My("John", "Doe", "J.Doe@Home.Dom", "Male"); //var_dump($My);
//var_dump($My->FirstName, $My->LastName, $My->Email, $My->Name, $My->Caption, $My->Comment);

//$My->FirstName = "Jane";
//$My->Comment = "Female";
//var_dump($My->FirstName, $My->Name, $My->Caption, $My->Comment, $My->Comment);

//var_dump($My->FirstName());
//$My->FirstName("Jane");
//var_dump($My->FirstName);
?>