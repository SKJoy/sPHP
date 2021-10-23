<?php
namespace sPHP\API;

class V2 extends \sPHP\Basic{
	#region Public constant
	#region Error
	public const ERROR_ACCESS_DENIED = ["Code" => 99999, "Description" => "Access denied", ];
	public const ERROR_UNAUTHORIZED = ["Code" => 99998, "Description" => "Unauthorized", ];
	public const ERROR_INSUFFICIENT_DATA = ["Code" => 89999, "Description" => "Insufficient data", ];
	public const ERROR_SUBJECT_NOT_FOUND = ["Code" => 89998, "Description" => "Subject not found", ];
	public const ERROR_REQUIRED_EMAIL = ["Code" => 79999, "Description" => "Email address is required", ];
	public const ERROR_REQUIRED_PASSWORD = ["Code" => 79999, "Description" => "Password is required", ];
	#endregion Error

	public const OUTPUT_FORMAT_ARRAY = "ARRAY";
	public const OUTPUT_FORMAT_JSON = "JSON";
	public const OUTPUT_FORMAT_OBJECT = "OBJECT";
	#endregion Public constant

	#region Method
	#region System
	public function __construct(?string $OutputFormat = null, ?string $LastName = null){
		parent::{__FUNCTION__}([ // Set initial property values upon object instantiation
			"OutputFormat"	=>	is_null($OutputFormat) ? $this::OUTPUT_FORMAT_ARRAY : $OutputFormat, 
			//"LastName"	=>	$LastName, 
			"Response"		=>	[
				"Error" => [], 
				"Documentation" => [
					"Description" => null, 
					"Method" => "Argument/GET/POST", 
					"Argument" => [], 
					"Note" => [], 
				], 
				"Response" => [], 
				"Diagnostics" => [
					"Time" => [
						"Begin" => date("r"), 
						"End" => null, 
						"DurationSecond" => 0, 
					], 
					//"GET" => $_GET, 
					//"POST" => $_POST, 
					"RemoteIP" => $_SERVER["REMOTE_ADDR"], 
					"Host" => $_SERVER["HTTP_HOST"], 
					"Server" => $_SERVER["SERVER_NAME"], 
					"User" => [
						"ID" => \sPHP::$User->ID(), 
					], 
				], 
			], 
		]);

		#region Property dependancy
		//* Add properties that will reset other properties when set/changed
		//parent::AddPropertyDependancy("FirstName", ["Name", "Caption", ]); //? Name & Caption will be reset when FirstName changes
		//parent::AddPropertyDependancy("LastName", ["Name", "Caption", ]); //? Keep adding dependant properties as array elements
		#endregion Property dependancy
	}
	
	public function __destruct(){
		parent::{__FUNCTION__}();
	}
	#endregion System

	public function Output($Response = null){
		if(is_null($Response))$Response = $this->Response;

		return $this->OutputFormat == $this::OUTPUT_FORMAT_JSON ? json_encode($Response) : (
			$this->OutputFormat == $this::OUTPUT_FORMAT_OBJECT ? json_decode(json_encode($Response)) : 
				$Response
		);
	}
	#endregion Method

	#region Property
	#region Read only
	public function xCaption(){ parent::DebugLogCall();
		if(is_null($this->Property[__FUNCTION__])){ // Generate property value if not set
			$this->Property[__FUNCTION__] = "{$this->Property["Name"]} [{$this->Property["Email"]}]";
		}

		return $this->Property[__FUNCTION__];
	}
	#endregion Read only

	public function xComment($Value = null){ parent::DebugLogCall($Value);
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
?>