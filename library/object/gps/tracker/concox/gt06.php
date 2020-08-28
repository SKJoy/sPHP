<?php
namespace sPHP\GPS\Tracker\Concox;

class GT06{
	private $Property = [
		"HexData"							=>	null,
		"BinaryData"						=>	null,
		"PacketStartMarker"					=>	"7878",
		"PacketEndMarker"					=>	"0D0A",
		"CommonResponsePacketLength"		=>	"05",
		"CommonResponseErrorCheck"			=>	"D9DC",
		"ProtocolNumberDataLength"			=>	2,
		"SerialNumberDataLength"			=>	4,

		// READ ONLY
		"Message"							=>	null,
		"MessageResponse"					=>	null,
	];

	#region Method
	public function __construct($HexData = null, $BinaryData = null){
		$Result = true;

        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

		return $Result;
	}

	public function __destruct(){
		$Result = true;

		return $Result;
	}

	// On the fly method, does not affect any property, rather used by property setter and on demand
	public function DecodeMessage($Data){
		$StartMarkerLength = strlen($this->Property["PacketStartMarker"]);

		// If BIN (raw) data is passed, convert to double byte HEX series
		if(substr($Data, 0, $StartMarkerLength) != $this->Property["PacketStartMarker"])$Data = strtoupper(bin2hex($Data));

		// Extract the first/initial valid packet wrapped by valid start and end markers
		$StartMarkerPosition = strpos($Data, $this->Property["PacketStartMarker"]);
		$EndMarkerLength = strlen($this->Property["PacketEndMarker"]);
		$Data = substr($Data, $StartMarkerPosition, strpos($Data, $this->Property["PacketEndMarker"], $StartMarkerPosition + $StartMarkerLength) - $StartMarkerPosition + $EndMarkerLength);
//var_dump($Data);
		$CommonResponsePacketLengthDataLength = strlen($this->Property["CommonResponsePacketLength"]);
		// Minimum valid length = START + Packet length + Protocol number + Minimum 1 byte data + Serial number + Error check + STOP
		if(strlen($Data) >= $StartMarkerLength + $CommonResponsePacketLengthDataLength + $this->Property["ProtocolNumberDataLength"] + 1 + $this->Property["SerialNumberDataLength"] + strlen($this->Property["CommonResponseErrorCheck"]) + $EndMarkerLength){
			$ProtocolNumber = substr($Data, 6, 2);
			$ProtocolData = substr($Data, $StartMarkerLength + $CommonResponsePacketLengthDataLength + $this->Property["ProtocolNumberDataLength"], strlen($Data) - ($StartMarkerLength + $CommonResponsePacketLengthDataLength + $this->Property["ProtocolNumberDataLength"] + $EndMarkerLength));
			$ProtocolDataLength = strlen($ProtocolData);
//var_dump(["Data" => $Data, "ProtocolNumber" => $ProtocolNumber, "ProtocolData" => $ProtocolData, ]);
			if($ProtocolNumber == "01" && $ProtocolDataLength == 24){
				$Result = $this->DecodeMessage_LogIn($ProtocolData);
			}
			elseif($ProtocolNumber == "13" && $ProtocolDataLength == 18){
				$Result = $this->DecodeMessage_Status($ProtocolData);
			}
			elseif($ProtocolNumber == "12" && $ProtocolDataLength == 60){
				$Result = $this->DecodeMessage_Location($ProtocolData);
			}
			elseif($ProtocolNumber == "16" && $ProtocolDataLength == 72){
				$Result = $this->DecodeMessage_Alarm($ProtocolData);
			}
			else{
				$Result = false;
			}
		}
		else{
			$Result = false;
		}

		if($Result)$Result = array_merge([
			"ProtocolNumber" => $ProtocolNumber,
		], $Result);

		return $Result;
	}

	// On the fly method, does not affect any property, rather used by property setter and on demand
	public function EncodeMessageResponse($DecodedData){
		if($DecodedData["ProtocolNumber"] == "01"){
			$Result = $this->ResponseMessage_LogIn($DecodedData);
		}
		elseif($DecodedData["ProtocolNumber"] == "13"){
			$Result = $this->ResponseMessage_Status($DecodedData);
		}
		else{
			$Result = false;
		}

		return $Result;
	}

	// DEPRICATE THIS METHOD ASAP, should be replaced by $this->EncodeMessageResponse($DecodedData)
	public function ResponseMessage($DecodedData = null){
		return $this->EncodeMessageResponse($DecodedData);
	}
	#endregion Method

	#region Property
	public function HexData($Value = null){
		if(is_null($Value)){
			$Result = $this->Property[__FUNCTION__];
		}
		else{
			$this->Property[__FUNCTION__] = $Value;

			$this->Property["BinaryData"] = hex2bin($this->Property[__FUNCTION__]);
			$this->OnDataChange();

			$Result = true;
		}

		return $Result;
	}

	public function BinaryData($Value = null){
		if(is_null($Value)){
			$Result = $this->Property[__FUNCTION__];
		}
		else{
			$this->Property[__FUNCTION__] = $Value;

			$this->Property["HexData"] = strtoupper(bin2hex($this->Property[__FUNCTION__]));
			$this->OnDataChange();

			$Result = true;
		}

		return $Result;
	}

	public function PacketStartMarker($Value = null){
		if(is_null($Value)){
			$Result = $this->Property[__FUNCTION__];
		}
		else{
			$this->Property[__FUNCTION__] = $Value;

			$Result = true;
		}

		return $Result;
	}

	public function PacketEndMarker($Value = null){
		if(is_null($Value)){
			$Result = $this->Property[__FUNCTION__];
		}
		else{
			$this->Property[__FUNCTION__] = $Value;

			$Result = true;
		}

		return $Result;
	}

	public function Message(){
		if(is_null($this->Property[__FUNCTION__]))$this->Property[__FUNCTION__] = $this->DecodeMessage($this->Property["HexData"]);

		return $this->Property[__FUNCTION__];
	}

	public function MessageResponse(){
		if(is_null($this->Property[__FUNCTION__]))$this->Property[__FUNCTION__] = $this->EncodeMessageResponse($this->Message());

		return $this->Property[__FUNCTION__];
	}
	#endregion Property

	#region Function
	private function OnDataChange(){
		$this->Property["Message"] = null;
		$this->Property["MessageResponse"] = null;

		return true;
	}

	private function Decode_Alarm($Data){
		if($Data == "01"){
			$Result["Panic"] = true;
		}
		elseif($Data == "02"){
			$Result["Power"] = false;
		}
		elseif($Data == "03"){
			$Result["Shock"] = true;
		}
		else{
			$Result = [];
		}

		return array_merge($Result, [
			"Power" => null,
			"Panic" => null,
			"Shock" => null,
		]);
	}

	private function Decode_Language($Data){
		if($Data == "01"){
			$Result = "Chinese";
		}
		elseif($Data == "02"){
			$Result = "English";
		}
		else{
			$Result = null;
		}

		return $Result;
	}

	private function Decode_TerminalInformation($Byte){
		/*
		Data must be double byte HEX of the original single byte BINARY (raw) data

		Status
			Acc = Off; GPS mode = Off;
				START							7878
				PACKET LENGTH					0A
				PROTOCOL						13
				TERMINAL INFORMATION CONTENT	01
				VOLTAGE LEVEL					06
				GSM SIGNAL STRENGTH				01
				ALARM LANGUAGE					0002
				SERIAL NUMBER					002E
				ERROR CHECK						75D0
				END								0D0A
			Acc = On; GPS mode = On;
				START							7878
				PACKET LENGTH					0A
				PROTOCOL						13
				TERMINAL INFORMATION CONTENT	42
				VOLTAGE LEVEL					06
				GSM SIGNAL STRENGTH				01
				ALARM LANGUAGE					0002
				SERIAL NUMBER					002E
				ERROR CHECK						75D0
				END								0D0A
			Acc = Off; GPS mode = Off; Power = Off;
				START							7878
				PACKET LENGTH					0A
				PROTOCOL						13
				TERMINAL INFORMATION CONTENT	11
				VOLTAGE LEVEL					06
				GSM SIGNAL STRENGTH				01
				ALARM LANGUAGE					0002
				SERIAL NUMBER					002E
				ERROR CHECK						75D0
				END								0D0A
		*/

		$Bit = str_pad(base_convert($Byte, 16, 2), 8, "0", STR_PAD_LEFT);
		$Bit3to5 = substr($Bit, 2, 3);
	//var_dump(["Byte" => $Byte, "Bit" => $Bit, "Bit3to5" => $Bit3to5, ]);
		$Result = [
			"Acc"		=>	$Bit[6] ? true : false,
			"GPS"		=>	$Bit[1] ? true : false,
			"Power"		=>	$Bit3to5 == "010" ? false : null,
			"Panic"		=>	$Bit3to5 == "100" ? true : null, // Check and confirm
			"Shock"		=>	$Bit3to5 == "001" ? true : null, // Check and confirm
			//"IsSecureMode"	=>	$Bit[7] ? true : false, // Check and confirm, primary check showed reverse result
		];

		return $Result;
	}

	private function Decode_CourseStatus($Data){
		$Bit = str_pad(base_convert(substr($Data, 0, 2), 16, 2), 8, "0", STR_PAD_LEFT) . str_pad(base_convert(substr($Data, 2, 2), 16, 2), 8, "0", STR_PAD_LEFT);

		$Result = [
			"Course" => [
				"GPSIsValid" => $Bit[3] == 1 ? true : false,
				"GPSMode" => $Bit[2] == 0 ? "Realtime" : "Differantial",
				"LatitudeMultiplier" => $Bit[5] == 0 ? -1 : 1,
				"LongitudeMultiplier" => $Bit[4] == 1 ? -1 : 1,
				"Direction" => bindec(substr($Bit, 6)),
			],
			"Status" => [ // Not documented in Concox TR06, got from GOOMI documentation!
				"Acc" => $Bit[0] == 0 ? false : true,
				"Input2" => $Bit[1] == 0 ? false : true,
			],
		];

		return $Result;
	}

	private function Decode_DateTime($Data){
	//var_dump($Data);
		$Result = implode("-", [
			substr(date("Y"), 0, 2) . str_pad(hexdec(substr($Data, 0, 2)), 2, "0", STR_PAD_LEFT),
			str_pad(hexdec(substr($Data, 2, 2)), 2, "0", STR_PAD_LEFT),
			str_pad(hexdec(substr($Data, 4, 2)), 2, "0", STR_PAD_LEFT),
		]) . " " . implode(":", [
			str_pad(hexdec(substr($Data, 6, 2)), 2, "0", STR_PAD_LEFT),
			str_pad(hexdec(substr($Data, 8, 2)), 2, "0", STR_PAD_LEFT),
			str_pad(hexdec(substr($Data, 10, 2)), 2, "0", STR_PAD_LEFT),
		]);

		return $Result;
	}

	private function DecodeMessage_LogIn($Data){
		/*
		Data must be a continuous STRING of double byte HEX representation of the single byte BINARY (raw) data seriese

		78780D010866005040143457002C50340D0A
		*/

		$Result = [
			"ProtocolName" => "Log in",
			"TerminalID" => substr($Data, 0, 16),
			"Serial" => substr($Data, 16, 4), // Packet serial number
			"ErrorCheck" => substr($Data, 20, 4), // Error check data
		];

		return $Result;
	}

	private function DecodeMessage_Status($Data){
		/*
		Data must be a continuous STRING of double byte HEX representation of the single byte BINARY (raw) data seriese

		Acc = Off; GPS Mode = Off;
		78780A13010603000201D422550D0A

		Acc = On; GPS Mode = On;
		78780A1342060100020171A9120D0A

		Acc = Off; GPS Mode = Off; Power = Off;
		78780A1311060300020196F68A0D0A
		*/

		$Result = [
			"ProtocolName" => "Status",
			"Terminal" => $this->Decode_TerminalInformation(substr($Data, 0, 2)),
			"Voltage" => round(substr($Data, 2, 2) * 16.66, 2), // Percentile
			"GSMSignal" => substr($Data, 4, 2) * 25, // Percentile
			//"Alarm" => $this->Decode_Alarm(substr($Data, 6, 2)), // Manual has no translation about this!
			"Language" => $this->Decode_Language(substr($Data, 8, 2)),
			"Serial" => substr($Data, 10, 4), // Packet serial number
			"ErrorCheck" => substr($Data, 14, 4), // Error check data
		];

		return $Result;
	}

	private function DecodeMessage_Alarm($Data){
		/*
		Data must be a continuous STRING of double byte HEX representation of the single byte BINARY (raw) data seriese

		25161408050F1C22D3028F355C09B34AC00044DF0801D602002E0007F00106030202019525BD
		25161408050F1216D3028F355C09B34AC00044DF0801D602002E0007F00106030202018E8E9B
		25161408050F0D38D3028F355C09B34AC00044DF0801D602002E0007F10106040202018991B1
		*/

		$CourseStatus = $this->Decode_CourseStatus(substr($Data, 32, 4));

		$Result = [
			"ProtocolName" => "Alarm",
			"DateTime" => $this->Decode_DateTime(substr($Data, 0, 12)),
			"Velocity" => hexdec(substr($Data, 30, 2)),
			"Terminal" => $this->Decode_TerminalInformation(substr($Data, 54, 2)),
			"Voltage" => substr($Data, 56, 2) * 16.66, // Percentile
			"Alarm" => $this->Decode_Alarm(substr($Data, 60, 2)),
			"Language" => $this->Decode_Language(substr($Data, 62, 2)),
			"GPSSatellite" => hexdec(substr($Data, 12, 2)),
			"Latitude" => (hexdec(substr($Data, 14, 8)) / 30000 / 60) * $CourseStatus["Course"]["LatitudeMultiplier"],
			"Longitude" => (hexdec(substr($Data, 22, 8)) / 30000 / 60) * $CourseStatus["Course"]["LongitudeMultiplier"],
			"Course" => $CourseStatus["Course"],
			"Status" => $CourseStatus["Status"],
			"GSMSignal" => substr($Data, 58, 2) * 25, // Percentile
			"Serial" => substr($Data, 64, 4), // Packet serial number
			"ErrorCheck" => substr($Data, 68, 4), // Error check data
		];

		return $Result;
	}

	private function DecodeMessage_Location($Data){
		/*
		Data must be a continuous STRING of double byte HEX representation of the single byte BINARY (raw) data seriese

		78781F121408050D2E1FC6028FA1B809B2A0C000D4D201D602006F000F5300586FEA0D0A
		78781F121408050D3419D1028F44E809B2EA6005D48701D60200C90007E800870FAF0D0A
		78781F121408050D3337D0028F4FE409B2E0F028D49101D60200C90007E6008317420D0A
		*/

		$CourseStatus = $this->Decode_CourseStatus(substr($Data, 32, 4));

		$Result = [
			"ProtocolName" => "Location",
			"DateTime" => $this->Decode_DateTime(substr($Data, 0, 12)),
			"Velocity" => hexdec(substr($Data, 30, 2)),
			"GPSSatellite" => hexdec(substr($Data, 12, 2)),
			"Latitude" => (hexdec(substr($Data, 14, 8)) / 30000 / 60) * $CourseStatus["Course"]["LatitudeMultiplier"],
			"Longitude" => (hexdec(substr($Data, 22, 8)) / 30000 / 60) * $CourseStatus["Course"]["LongitudeMultiplier"],
			"Course" => $CourseStatus["Course"],
			"Status" => $CourseStatus["Status"],
			"Serial" => substr($Data, 52, 4), // Packet serial number
			"ErrorCheck" => substr($Data, 56, 4), // Error check data
		];

		return $Result;
	}

	private function ResponseMessage_LogIn($DecodedData){
		$Result = "{$this->Property["PacketStartMarker"]}{$this->Property["CommonResponsePacketLength"]}{$DecodedData["ProtocolNumber"]}{$DecodedData["Serial"]}{$this->Property["CommonResponseErrorCheck"]}{$this->Property["PacketEndMarker"]}"; // Static D9DC as Error check!!!

		return $Result;
	}

	private function ResponseMessage_Status($DecodedData){
		// Looks like the Chinese technical manual is wrong/confusing about the example of the Protocol number for this response!
		$Result = "{$this->Property["PacketStartMarker"]}{$this->Property["CommonResponsePacketLength"]}{$DecodedData["ProtocolNumber"]}{$DecodedData["Serial"]}{$this->Property["CommonResponseErrorCheck"]}{$this->Property["PacketEndMarker"]}"; // Static D9DC as Error check!!!

		return $Result;
	}
	#endregion Function
}
?>