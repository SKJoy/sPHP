<?php
namespace sPHP\GPS\Tracker\Generic;

class EELink{
	public const MODE_LOGIN = "01";

	private $Property = [
		"StartMarker"	=>	"6767", 
		"Data" 			=>	null, 
		"Mode"			=>	null, 
		"ModeName"		=>	null, 
		"PacketLength"	=>	0, 
		"DataSerial"	=>	null, 
		"Identifier"	=>	null, 
		"Response"		=>	null, 
	];

	#region Method
	public function __construct(){

	}

	public function __destruct(){

	}

	public function Decode(string $Data){
		$this->Property["Data"] = strtoupper($Data);
		if(substr($this->Property["Data"], 0, 4) != $this->Property["StartMarker"])$this->Property["Data"] = "{$this->Property["StartMarker"]}{$this->Property["Data"]}";
		$this->Property["Data"] = substr($this->Property["Data"], strpos($this->Property["Data"], $this->Property["StartMarker"]) + strlen($this->Property["StartMarker"]));
		$this->Property["Mode"] = substr($this->Property["Data"], 0, 2);
		$this->Property["Data"] = substr($this->Property["Data"], strlen($this->Property["Mode"]));
		$this->Property["PacketLength"] = hexdec(substr($this->Property["Data"], 0, 4));
		$this->Property["Data"] = substr($this->Property["Data"], 4);
		$this->Property["DataSerial"] = hexdec(substr($this->Property["Data"], 0, 4));
		$this->Property["Data"] = substr($this->Property["Data"], 4);

		if($this->Property["Mode"] == $this::MODE_LOGIN){
			$this->Decode_LogIn($this->Property["Data"]);
		}
		else{
			$this->Property["Data"] = null;
			$this->Property["Mode"] = null;
			$this->Property["ModeName"] = null;
			$this->Property["PacketLength"] = null;
			$this->Property["DataSerial"] = null;
			$this->Property["Identifier"] = null;
			$this->Property["Response"] = null;
		}

		return true;
	}
	#endregion Method

	#region Property
	public function Data(?string $Value = null){
		if(is_null($Value)){ // Get
			$Result = $this->Property[__FUNCTION__];
		}
		else{ // Set
			$this->Property[__FUNCTION__] = $Value;
			$Result = true;
		}

		return $Result;
	}

	public function Mode(){
		$Result = $this->Property[__FUNCTION__];

		return $Result;
	}

	public function ModeName(){
		$Result = $this->Property[__FUNCTION__];

		return $Result;
	}

	public function Response(){
		$Result = $this->Property[__FUNCTION__];

		return $Result;
	}
	#endregion Property

	#region Function
	private function Decode_LogIn(string $Data){ //var_dump($Data);
		$this->Property["ModeName"] = "Log in";
		$this->Property["Identifier"] = substr($Data, 0, 16);
		$this->Property["Response"] = "{$this->Property["StartMarker"]}{$this->Property["Mode"]}0002" . strtoupper(str_pad(dechex($this->Property["DataSerial"]), 4, "0", STR_PAD_LEFT)) . "";
	}
	#endregion Function
}

$EELink = new EELink();
$EELink->Decode("01000B060F086723205014490700");

var_dump(hex2bin($EELink->Response()));
?>