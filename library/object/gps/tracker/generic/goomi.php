<?php
namespace sPHP\GPS\Tracker\Generic;

class GOOMI{
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
		$StartMarkerLength = strlen($this->Property["PacketStartMarker"]); //var_dump("StartMarkerLength = {$StartMarkerLength}");

		// If BIN (raw) data is passed, convert to double byte HEX series
		if(substr($Data, 0, $StartMarkerLength) != $this->Property["PacketStartMarker"])$Data = strtoupper(bin2hex($Data));

		// Extract the first/initial valid packet wrapped by valid start and end markers
		$StartMarkerPosition = strpos($Data, $this->Property["PacketStartMarker"]); //var_dump("StartMarkerPosition = {$StartMarkerPosition}");
		$EndMarkerLength = strlen($this->Property["PacketEndMarker"]); //var_dump("EndMarkerLength = {$EndMarkerLength}");

		$Data = substr(
			$Data, 
			$StartMarkerPosition, 
			strpos(
				$Data, 
				$this->Property["PacketEndMarker"], 
				$StartMarkerPosition + $StartMarkerLength
			) - $StartMarkerPosition + $EndMarkerLength
		); //var_dump("Data = {$Data}");

		$CommonResponsePacketLengthDataLength = strlen($this->Property["CommonResponsePacketLength"]);
		// Minimum valid length = START + Packet length + Protocol number + Minimum 1 byte data + Serial number + Error check + STOP
		if(strlen($Data) >= $StartMarkerLength + $CommonResponsePacketLengthDataLength + $this->Property["ProtocolNumberDataLength"] + 1 + $this->Property["SerialNumberDataLength"] + strlen($this->Property["CommonResponseErrorCheck"]) + $EndMarkerLength){
			$ProtocolNumber = substr($Data, 6, 2); //var_dump("ProtocolNumber = {$ProtocolNumber}");
			$ProtocolData = substr($Data, $StartMarkerLength + $CommonResponsePacketLengthDataLength + $this->Property["ProtocolNumberDataLength"], strlen($Data) - ($StartMarkerLength + $CommonResponsePacketLengthDataLength + $this->Property["ProtocolNumberDataLength"] + $EndMarkerLength)); //var_dump("ProtocolData = {$ProtocolData}");
			$ProtocolDataLength = strlen($ProtocolData); //var_dump("ProtocolDataLength = {$ProtocolDataLength}");
			//var_dump(["Data" => $Data, "ProtocolNumber" => $ProtocolNumber, "ProtocolData" => $ProtocolData, ]);
			if(
					$ProtocolNumber == "01" 
				&&	(
							$ProtocolDataLength == 24 // Original GOOMI
						||	$ProtocolDataLength == 32 // Concox OBD22
					)
			){
				$Result = $this->DecodeMessage_LogIn($ProtocolData, $ProtocolDataLength);
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
			elseif($ProtocolNumber == "22" && $ProtocolDataLength >= 66){ // Mileage data is optional!
				$Result = $this->DecodeMessage_Location_x22($ProtocolData);
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
		], $Result); //var_dump("Result", $Result);

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

	private function Decode_LBS($Data){
		if(strlen($Data) == 16)$Data = "  {$Data}";
		$LengthBytes = trim(hexdec(substr($Data, 0, 2)));

		$Result = [
			"Length" => $LengthBytes ? hexdec(substr($Data, 0, 2)) : null, // Nowhere in the documentation says what it is!
			"MobileCountryCode" => hexdec(substr($Data, 2, 4)),
			"MobileNetworkCode" => hexdec(substr($Data, 6, 2)),
			"LocationAreaCode" => hexdec(substr($Data, 8, 4)),
			"CellID" => hexdec(substr($Data, 12, 6)),
		];
		//var_dump($Data);
		return $Result;
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

		$Bit = str_pad(base_convert($Byte, 16, 2), 8, "0", STR_PAD_LEFT); //var_dump($Bit);
		$Bit3to5 = substr($Bit, 2, 3);
		//var_dump(["Byte" => $Byte, "Bit" => $Bit, "Bit3to5" => $Bit3to5, ]);
		$Result = [
			"Acc"			=>	$Bit[6] ? true : false,
			"GPS"			=>	$Bit[1] ? true : false,
			"Power"			=>	$Bit3to5 == "010" ? false : null,
			"Panic"			=>	$Bit3to5 == "100" ? true : null, // Check and confirm
			"Shock"			=>	$Bit3to5 == "001" ? true : null, // Check and confirm
			"SecureMode"	=>	$Bit[7] ? true : false, 
		];

		return $Result;
	}

	private function Decode_CourseStatus($Data){
		$Bit = str_pad(base_convert(substr($Data, 0, 2), 16, 2), 8, "0", STR_PAD_LEFT) . str_pad(base_convert(substr($Data, 2, 2), 16, 2), 8, "0", STR_PAD_LEFT);
		//var_dump($Bit);

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

	private function DecodeMessage_LogIn($Data, $ProtocolDataLength = 24){
		/*
		Data must be a continuous STRING of double byte HEX representation of the single byte BINARY (raw) data seriese

		78780D010866005040143457002C50340D0A
		*/

		// Packet serial number
		if($ProtocolDataLength == 24){ // Original GOOMI
			$PacketSerial = substr($Data, 16, 4);
			$ErrorCheck = substr($Data, 20, 4);
		}
		elseif($ProtocolDataLength == 32){ // Concox OBD22
			$PacketSerial = substr($Data, 24, 4);
			$ErrorCheck = substr($Data, 28, 4);
		}
		else{
			$PacketSerial = null;
			$ErrorCheck = null;
		}

		$Result = [
			"ProtocolName" => "Log in",
			"TerminalID" => substr($Data, 0, 16),
			"Serial" => $PacketSerial, // Packet serial number
			"ErrorCheck" => $ErrorCheck, // Error check data
			"Raw" => $Data, 
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
			"GSM" => array_merge(["Signal" => substr($Data, 4, 2) * 25, ], ["Length" => null, "MobileCountryCode" => null, "MobileNetworkCode" => null, "LocationAreaCode" => null, "CellID" => null, ]),
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
			"GPSSatellite" => hexdec(substr($Data, 12, 2)),
			"Latitude" => (hexdec(substr($Data, 14, 8)) / 30000 / 60) * $CourseStatus["Course"]["LatitudeMultiplier"],
			"Longitude" => (hexdec(substr($Data, 22, 8)) / 30000 / 60) * $CourseStatus["Course"]["LongitudeMultiplier"],
			"Course" => $CourseStatus["Course"],
			"Status" => $CourseStatus["Status"],
			"GSM" => array_merge(["Signal" => substr($Data, 58, 2) * 25, ], $this->Decode_LBS(substr($Data, 36, 18))),
			"Language" => $this->Decode_Language(substr($Data, 62, 2)),
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
			"GSM" => array_merge(["Signal" => null, ], $this->Decode_LBS(substr($Data, 36, 16))),
			"Serial" => substr($Data, 52, 4), // Packet serial number
			"ErrorCheck" => substr($Data, 56, 4), // Error check data
		];

		return $Result;
	}

	private function DecodeMessage_Location_x22($Data){
		/*
		Data must be a continuous STRING of double byte HEX representation of the single byte BINARY (raw) data seriese

		78782222140B0C11192DCF028F1E0009B33D5012D56201D601521400A2E201000001C74B870D0A
		*/

		$HasMileage = strlen($Data) >= 66 + 8 ? true : false; // Mileage data available?

		$DataPart = $this->SplitString(
			$Data, // String to split
			null, // Separator
			"6, 1, 4, 4, 1, 2, 2, 1, 2, 3, 1, 1, 1, " . ($HasMileage ? 4 : null) . ", 2, 2", // Seriese of lengths to split into
			2 // Byte length per character, useful when denoting HEX value of string, 2 bytes per character
		);

		$CourseStatus = $this->Decode_CourseStatus($DataPart[5]);

		/*
		$Result = [
			"ProtocolName" => "Location x22",
			"DateTime" => $this->Decode_DateTime(substr($Data, 0, 12)),
			"Velocity" => hexdec(substr($Data, 30, 2)),
			"GPSSatellite" => hexdec(substr($Data, 12, 2)),
			"Latitude" => (hexdec(substr($Data, 14, 8)) / 30000 / 60) * $CourseStatus["Course"]["LatitudeMultiplier"],
			"Longitude" => (hexdec(substr($Data, 22, 8)) / 30000 / 60) * $CourseStatus["Course"]["LongitudeMultiplier"],
			"Course" => $CourseStatus["Course"],
			"Status" => $CourseStatus["Status"],
			"GSM" => array_merge(["Signal" => null, ], $this->Decode_LBS(substr($Data, 36, 16))),

			"Acc" => substr($Data, 52, 2), 
			"UploadMode" => substr($Data, 54, 2), 
			"GPSMode" => substr($Data, 56, 2), 
			"Mileage" => $MileageDataLength ? hexdec(substr($Data, 58, $MileageDataLength * 2)) / 100 : null, 

			"Serial" => substr($Data, 58 + ($MileageDataLength * 2), 4), // Packet serial number
			"ErrorCheck" => substr($Data, 62 + ($MileageDataLength * 2), 4), // Error check data
		];
		*/

		$Result = [
			"ProtocolName" => "Location x22",
			"DateTime" => $this->Decode_DateTime($DataPart[0]),
			"Velocity" => hexdec($DataPart[4]),
			"GPSSatellite" => hexdec($DataPart[1]),
			"Latitude" => (hexdec($DataPart[2]) / 30000 / 60) * $CourseStatus["Course"]["LatitudeMultiplier"],
			"Longitude" => (hexdec($DataPart[3]) / 30000 / 60) * $CourseStatus["Course"]["LongitudeMultiplier"],
			"Course" => $CourseStatus["Course"],
			"Status" => $CourseStatus["Status"],
			"GSM" => array_merge(["Signal" => null, ], $this->Decode_LBS(substr($Data, 36, 16))),
			//"Acc" => $DataPart[10] == "00" ? false : ($DataPart[10] == "01" ? true : null), 
			"UploadMode" => $DataPart[11], 
			//"GPSMode" => $DataPart[12] == "00" ? "Realtime" : ($DataPart[12] == "01" ? "Reupload" : null), 
			"Mileage" => $HasMileage ? hexdec($DataPart[13]) / 100 : null, 
			"Serial" => $DataPart[13 + $HasMileage], 
			"ErrorCheck" => $DataPart[14 + $HasMileage], 
		];

		// Override status Acc with direct Acc (Should we? Consult with manufacturer)
		$Result["Status"]["Acc"] = $DataPart[10] == "00" ? false : ($DataPart[10] == "01" ? true : null);

		// Override course GPSMode with direct GPSMode (Should we? Consult with manufacturer)
		$Result["Course"]["GPSMode"] = $DataPart[12] == "00" ? "Realtime" : ($DataPart[12] == "01" ? "Reupload" : null);

		return $Result;
	}

	private function ResponseMessage_LogIn($DecodedData){
		$Data = "{$this->Property["CommonResponsePacketLength"]}{$DecodedData["ProtocolNumber"]}{$DecodedData["Serial"]}";
		$ErrorCheck = strtoupper(dechex($this->CRC_16(str_split($Data, 2))));
		//var_dump($Data, $ErrorCheck);
		//var_dump($DecodedData, CRC_16(str_split($DecodedData[""], 2)));

		$Result = "{$this->Property["PacketStartMarker"]}{$this->Property["CommonResponsePacketLength"]}{$DecodedData["ProtocolNumber"]}{$DecodedData["Serial"]}{$ErrorCheck}{$this->Property["PacketEndMarker"]}";
		//$Result = "{$this->Property["PacketStartMarker"]}{$this->Property["CommonResponsePacketLength"]}{$DecodedData["ProtocolNumber"]}{$DecodedData["Serial"]}{$this->Property["CommonResponseErrorCheck"]}{$this->Property["PacketEndMarker"]}"; // Static D9DC as Error check!!!
		//$Result = "{$this->Property["PacketStartMarker"]}{$this->Property["CommonResponsePacketLength"]}{$DecodedData["ProtocolNumber"]}{$DecodedData["Serial"]}{$DecodedData["ErrorCheck"]}{$this->Property["PacketEndMarker"]}";

		return $Result;
	}

	private function ResponseMessage_Status($DecodedData){
		$Data = "{$this->Property["CommonResponsePacketLength"]}{$DecodedData["ProtocolNumber"]}{$DecodedData["Serial"]}";
		$ErrorCheck = strtoupper(dechex($this->CRC_16(str_split($Data, 2))));
		
		// Looks like the Chinese technical manual is wrong/confusing about the example of the Protocol number for this response!
		$Result = "{$this->Property["PacketStartMarker"]}{$this->Property["CommonResponsePacketLength"]}{$DecodedData["ProtocolNumber"]}{$DecodedData["Serial"]}{$ErrorCheck}{$this->Property["PacketEndMarker"]}"; // Static D9DC as Error check!!!
		//$Result = "{$this->Property["PacketStartMarker"]}{$this->Property["CommonResponsePacketLength"]}{$DecodedData["ProtocolNumber"]}{$DecodedData["Serial"]}{$this->Property["CommonResponseErrorCheck"]}{$this->Property["PacketEndMarker"]}"; // Static D9DC as Error check!!!

		return $Result;
	}
	#endregion Function

	#region Private function
	private function SplitString($Data, $Separator = null, $LengthSeries = null, $LengthMultiplier = 1){
		$Result = [];

		if(is_null($LengthSeries) && $Separator){ // Split by separator
			$Result = explode($Separator, $Data);
		}
		else{
			if(!is_array($LengthSeries))$LengthSeries = explode(",", str_replace(" ", null, $LengthSeries));

			$LengthRead = 0;

			foreach(array_filter($LengthSeries) as $LengthIndex => $Length){
				$Result[] = substr($Data, $LengthRead + ($LengthIndex * strlen($Separator)), $Length * $LengthMultiplier);
				$LengthRead = $LengthRead + ($Length * $LengthMultiplier);
			}
		}

		return count($Result) ? $Result : false;
	}

	private function CRC_16($data){
		$CRCTable = array(  
			0x0000, 0x1189, 0x2312, 0x329B, 0x4624, 0x57AD,
			0x6536, 0x74BF, 0x8C48, 0x9DC1, 0xAF5A, 0xBED3,
			0xCA6C, 0xDBE5, 0xE97E, 0xF8F7, 0x1081, 0x0108,
			0x3393, 0x221A, 0x56A5, 0x472C, 0x75B7, 0x643E,
			0x9CC9, 0x8D40, 0xBFDB, 0xAE52, 0xDAED, 0xCB64,
			0xF9FF, 0xE876, 0x2102, 0x308B, 0x0210, 0x1399,
			0x6726, 0x76AF, 0x4434, 0x55BD, 0xAD4A, 0xBCC3,
			0x8E58, 0x9FD1, 0xEB6E, 0xFAE7, 0xC87C, 0xD9F5,
			0x3183, 0x200A, 0x1291, 0x0318, 0x77A7, 0x662E,
			0x54B5, 0x453C, 0xBDCB, 0xAC42, 0x9ED9, 0x8F50,
			0xFBEF, 0xEA66, 0xD8FD, 0xC974, 0x4204, 0x538D,
			0x6116, 0x709F, 0x0420, 0x15A9, 0x2732, 0x36BB,
			0xCE4C, 0xDFC5, 0xED5E, 0xFCD7, 0x8868, 0x99E1,
			0xAB7A, 0xBAF3, 0x5285, 0x430C, 0x7197, 0x601E,
			0x14A1, 0x0528, 0x37B3, 0x263A, 0xDECD, 0xCF44,
			0xFDDF, 0xEC56, 0x98E9, 0x8960, 0xBBFB, 0xAA72,
			0x6306, 0x728F, 0x4014, 0x519D, 0x2522, 0x34AB,
			0x0630, 0x17B9, 0xEF4E, 0xFEC7, 0xCC5C, 0xDDD5,
			0xA96A, 0xB8E3, 0x8A78, 0x9BF1, 0x7387, 0x620E,
			0x5095, 0x411C, 0x35A3, 0x242A, 0x16B1, 0x0738,
			0xFFCF, 0xEE46, 0xDCDD, 0xCD54, 0xB9EB, 0xA862,
			0x9AF9, 0x8B70, 0x8408, 0x9581, 0xA71A, 0xB693,
			0xC22C, 0xD3A5, 0xE13E, 0xF0B7, 0x0840, 0x19C9,
			0x2B52, 0x3ADB, 0x4E64, 0x5FED, 0x6D76, 0x7CFF,
			0x9489, 0x8500, 0xB79B, 0xA612, 0xD2AD, 0xC324,
			0xF1BF, 0xE036, 0x18C1, 0x0948, 0x3BD3, 0x2A5A,
			0x5EE5, 0x4F6C, 0x7DF7, 0x6C7E, 0xA50A, 0xB483,
			0x8618, 0x9791, 0xE32E, 0xF2A7, 0xC03C, 0xD1B5,
			0x2942, 0x38CB, 0x0A50, 0x1BD9, 0x6F66, 0x7EEF,
			0x4C74, 0x5DFD, 0xB58B, 0xA402, 0x9699, 0x8710,
			0xF3AF, 0xE226, 0xD0BD, 0xC134, 0x39C3, 0x284A,
			0x1AD1, 0x0B58, 0x7FE7, 0x6E6E, 0x5CF5, 0x4D7C,
			0xC60C, 0xD785, 0xE51E, 0xF497, 0x8028, 0x91A1,
			0xA33A, 0xB2B3, 0x4A44, 0x5BCD, 0x6956, 0x78DF,
			0x0C60, 0x1DE9, 0x2F72, 0x3EFB, 0xD68D, 0xC704,
			0xF59F, 0xE416, 0x90A9, 0x8120, 0xB3BB, 0xA232,
			0x5AC5, 0x4B4C, 0x79D7, 0x685E, 0x1CE1, 0x0D68,
			0x3FF3, 0x2E7A, 0xE70E, 0xF687, 0xC41C, 0xD595,
			0xA12A, 0xB0A3, 0x8238, 0x93B1, 0x6B46, 0x7ACF,
			0x4854, 0x59DD, 0x2D62, 0x3CEB, 0x0E70, 0x1FF9,
			0xF78F, 0xE606, 0xD49D, 0xC514, 0xB1AB, 0xA022,
			0x92B9, 0x8330, 0x7BC7, 0x6A4E, 0x58D5, 0x495C,
			0x3DE3, 0x2C6A, 0x1EF1, 0x0F78,
		);

        $crc = 0xFFFF;
    
        foreach ($data as $d) {
            $d = hexdec($d); //<-- This did the trick
            $crc = $CRCTable[($d ^ $crc) & 0xFF] ^ ($crc >> 8 & 0xFF);
        }
    
        $crc = $crc ^ 0xFFFF;
        $crc = $crc & 0xFFFF;

        return $crc;
    }
	#endregion Private function
}
?>