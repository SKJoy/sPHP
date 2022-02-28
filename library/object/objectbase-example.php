<?php
namespace sPHP;
require __DIR__ . "\objectbase.php";

class MyObject extends ObjectBase{ //* Example usage
	#region Method
	#region System
	public function __construct(?string $FirstName = null, ?string $LastName = null, $_OnGet = null, $_OnSet = null){
		#region Set default property value
		//$this->Property["FirstName"] = "John";
		//$this->Property["LastName"] = "Doe";
		#endregion Set default property value

		parent::__construct(get_defined_vars()); // Call parent constructor with arguments provided with; !!! Must be the first line in function

		#region Configuration
		parent::SetReadOnlyProperty(["Name", "ReadOnly", ]); // Set read only property list to prevent accidental property setting
		parent::SetDependantProperty("Name", ["FirstName", "LastName"]); // Set dependant property that gets reset by other properties
		#endregion Configuration
	}

	public function __destruct(){
		parent::__destruct(); // Call parent destructor upon destruction
	}
	#endregion System

	#endregion Method

	#region Property
	
	#region Read only property
	public function Name(){ $this->_WriteLog("Get Name");
		if(is_null($this->Property[__FUNCTION__])){ //? Prepare property value if not exists
			$this->Property["Name"] = implode(" ", array_filter([
				$this->Property["FirstName"], 
				$this->Property["LastName"], 
			]));
		}

		return $this->Property[__FUNCTION__];
	}
	#endregion Read only property
	#endregion Property

	#region Private function
	
	#endregion Private function
};

/* OnGet and OnSet event
$MyObject = new MyObject("Broken", "Arrow", function($Property){ // On get
	var_dump("Get property '{$Property}'");
	//return false; //* FALSE will cancel the operation
}, function($Property, $Value){ // On set	
	var_dump("Set property '{$Property}' = '{$Value}'");
	return false; //* FALSE will cancel the operation
}); 

var_dump($MyObject->FirstName);
$MyObject->FirstName = "John";
var_dump($MyObject->FirstName);
exit;
*/

$MyObject = new MyObject("Broken", "Arrow"); $MyObject->_WriteLog("Creating 'MyObject' object");
//$MyObject = new MyObject(); $MyObject->Log->Write("Creating 'MyObject' object");

//$MyObject->LastName = "Doe";
//$MyObject->ReadOnly("John Doe"); //! Try setting read only property
//$MyObject->ReadOnly = "John Doe"; //! Try setting read only property

//$MyObject->FirstName("Broken");
//$MyObject->LastName("Arrow");

/*
var_dump($MyObject->Name);
$MyObject->LastName = "Doe"; 
var_dump($MyObject->Name);
exit;
*/

var_dump(
	$MyObject->FirstName, 
	$MyObject->LastName, 
	$MyObject->Name, 
	null
);

var_dump(
	$MyObject->FirstName(), 
	$MyObject->LastName(), 
	$MyObject->Name(), 
	null
);
?>