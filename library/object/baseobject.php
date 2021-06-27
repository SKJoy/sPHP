<?php
namespace sPHP;

class Test{
	private $Property = [ // Internal property storage with default value
		//"FirstName" => "Default first name", 
	];

	// List of read only property name
	private $ReadOnlyPropertyList = "Name, HTML";

	#region Private variable
	//private $InternalVariable = "Default value";
	#endregion Private variable

	#region Public property
	//public $PublicProperty = "Default value";
	#endregion Public property

	#region Method
	public function __construct(?string $FirstName = null, ?string $LastName = null){ //var_dump("CONSTRUCT: Instantiating object");
		// Formalize read only property list
		$this->ReadOnlyPropertyList = explode(",", str_replace(" ", null, $this->ReadOnlyPropertyList));

		// Set property through argument passed
		foreach(get_defined_vars() as $Name => $Value)if(!is_null($Value))$this->$Name = $Value;
	}

	public function __destruct(){ //var_dump("DESTRUCT: Unloading object instance");

	}

	public function __get(string $Name){ //var_dump("GET: Property '{$Name}' is being get dynamically...");
		if(!isset($this->Property[$Name]))switch($Name){ // Set read only property where needed
			case "Name": $this->Property[$Name] = implode(" ", array_filter([$this->Property["FirstName"], $this->Property["LastName"], ])); break;
			case "HTML": $this->Property[$Name] = "<span>" . implode("</span><span>", array_filter([$this->Property["FirstName"], $this->Property["LastName"], ])) . "</span>"; break;
			default: $this->Property[$Name] = null; break; // Initialize inexistent property
		}

		return $this->Property[$Name]; // Return value from internal property storage
	}

	public function __set(string $Name, $Value = null){ //var_dump("SET: Property '{$Name}' is being set to '{$Value}' dynamically...");
		if(in_array($Name, $this->ReadOnlyPropertyList)){ // Read only property
			trigger_error("Cannot set read only property '{$Name}'"); // Trigger error
		}
		else{ // Property value can be set
			$this->Property[$Name] = $Value; // Set property value

			#region Custom code depending on property to set
			// Use any/mixed of of switch and/or condition patterns
			switch($Name){ // Switch pattern
				case "FirstName": $this->ClearOutput(); break;
				case "LastName": $this->ClearOutput(); break;
				default: break;
			}

			// Condition pattern
			if(in_array($Name, explode(",", str_replace(" ", null, "FirstName, LastName"))))foreach(explode(",", str_replace(" ", null, "Name, HTML")) as $ReadOnlyProperty)unset($this->Property[$ReadOnlyProperty]);
			#endregion Custom code depending on property to set
		}
	}
	#endregion Method

	#region Function
	private Function ClearOutput(){
		foreach(explode(",", str_replace(" ", null, "Name, HTML")) as $ReadOnlyProperty)unset($this->Property[$ReadOnlyProperty]);
	}
	#endregion Function
}

$Test = new Test(null, "Arrow");
//$Test = new Test();
var_dump($Test->FirstName, $Test->LastName, $Test->Name, $Test->HTML);

$Test->FirstName = "Tapat";
$Test->LastName = "Gubil";
var_dump($Test->Name, $Test->HTML);
?>