<?php
/*
    Name:           Protocol
    Purpose:        Protocol definer object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 8, 2019 02:46 AM
*/

namespace sPHP\Socket;

class Protocol{
    #region Property
    private $Property = [
		"Preset"					=>	null,
		"StartMarker"				=>	null,
		"StopMarker"				=>	null,
		"PacketStopMarker"			=>	null,
		"PacketSeparator"			=>	null,
		"MinimumPacketLength"		=>	1,
		"FieldSeparator"			=>	null,
		"IdentifierFieldPosition"	=>	null,
		"IdentifierFieldLength"		=>	null,
		"LogEntrySuffix"			=>	null, // Suffix to be added upon each log (line) entry
		"IgnoreOnly"				=>	null, // Array of characters/words to be ignored when received data has nothing else than these
		"HEXFormat"					=>	false,
		"FirstResponse"				=>	null, // Response/reply to be sent upon first data receiption
    ];
    #endregion Property

    #region Variable
    //private $Buffer = [];
    #endregion Variable

    #region Method
    public function __construct($Preset = null, $StartMarker = null, $StopMarker = null, $PacketStopMarker = null, $PacketSeparator = null, $MinimumPacketLength = null, $FieldSeparator = null, $IdentifierFieldPosition = null, $IdentifierFieldLength = null, $LogEntrySuffix = null, $IgnoreOnly = null, $HEXFormat = null, $FirstResponse = null){
		// Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

        return true;
    }

	// Decode data consisting one or more packets
	public function Decode($Data){
		$Result = true;

		return $Result;
	}

	// Decode a packet consisting one or more fields
	public function DecodePacket($Packet){
		$Result = true;

		return $Result;
	}

	// Create sample data with random values
	public function Generate(){
		$Result = true;

		return $Result;
	}
    #endregion Method

    #region Property
    public function Preset($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;

			$this->Reset();

			switch($this->Property[__FUNCTION__]){
				case SOCKET_PROTOCOL_DEFAULT:
					$this->Property["PacketSeparator"] = "\r\n";
					$this->Property["LogEntrySuffix"] = "\r\n";
					$this->Property["IgnoreOnly"] = [chr(10), ];
					$this->Property["FirstResponse"] = "Welcome to TCP Server";

					break;
				case SOCKET_PROTOCOL_TRACKER_COBAN:
					$this->Property["StartMarker"] = "##,imei:";
					$this->Property["StopMarker"] = ";";
					$this->Property["PacketStopMarker"] = ";";
					$this->Property["MinimumPacketLength"] = 27;
					$this->Property["FieldSeparator"] = ",";
					$this->Property["IdentifierFieldPosition"] = 1;
					$this->Property["FirstResponse"] = "LOAD";

					break;
				case SOCKET_PROTOCOL_TRACKER_GOOMI:
					$this->Property["StartMarker"] = "" . chr(hexdec("78")) . chr(hexdec("78")) . "";
					$this->Property["StopMarker"] = "" . chr(hexdec("0D")) . chr(hexdec("0A")) . "";
					$this->Property["PacketStopMarker"] = "" . chr(hexdec("0D")) . chr(hexdec("0A")) . "";
					$this->Property["MinimumPacketLength"] = 18;
					$this->Property["IdentifierFieldPosition"] = 5;
					$this->Property["IdentifierFieldLength"] = 8;
					$this->Property["HEXFormat"] = true;

					break;
				case SOCKET_PROTOCOL_TRACKER_Y202:
					$this->Property["StartMarker"] = "*HQ,";
					$this->Property["PacketStopMarker"] = "#";
					$this->Property["MinimumPacketLength"] = 20;
					$this->Property["FieldSeparator"] = ",";
					$this->Property["IdentifierFieldPosition"] = 1;

					break;
				default:
					$Result = false;

					break;
			}
        }

        return $Result;
    }

    public function StartMarker($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function StopMarker($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function PacketStopMarker($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function PacketSeparator($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function MinimumPacketLength($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function FieldSeparator($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function IdentifierFieldPosition($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function IdentifierFieldLength($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function LogEntrySuffix($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function IgnoreOnly($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function HEXFormat($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function FirstResponse($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }
    #endregion Property

	#region Function
    // Reset to default configuration
	private function Reset($Name){
		$Result = true;

		$this->Property["Preset"] = null;
		$this->Property["StartMarker"] = null;
		$this->Property["StopMarker"] = null;
		$this->Property["PacketStopMarker"] = null;
		$this->Property["PacketSeparator"] = null;
		$this->Property["MinimumPacketLength"] = 1;
		$this->Property["FieldSeparator"] = null;
		$this->Property["IdentifierFieldPosition"] = null;
		$this->Property["IdentifierFieldLength"] = null;
		$this->Property["LogEntrySuffix"] = null;
		$this->Property["IgnoreOnly"] = null;
		$this->Property["HEXFormat"] = false;
		$this->Property["FirstResponse"] = null;

		return $Result;
	}
	#endregion Function
}
?>