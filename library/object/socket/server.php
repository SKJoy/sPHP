<?php
/*
    Name:           SocketServer
    Purpose:        Socket server object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  November 3, 2018 01:14 AM
*/

namespace sPHP\Socket;

const SOCKET_STATE_RUNNING = "RUNNING";
const SOCKET_STATE_STOPPED = "STOPPED";

class Server{
    #region Property
    private $Property = [
        "Port"					=>	65534,
		"TerminalType"			=>	[], // Default value is set in the __construct method due to function name
		"OnData"				=>	null,
		"DataLogPath"			=>	null,
		"OnLog"					=>	null,
		"OnConnect"				=>	null,
		"OnError"				=>	null,
		"OnActivity"			=>	null,
		"StartTimeFile"			=>	"./server_starttime.txt",
		"Timeout"				=>	0, // Execute for indefinite period of time
		"OutputTerminator"		=>	"\r\n> ",
		"CommandFile"			=>	"./socketserver_command.txt",
		"StateFile"				=>	"./socketserver_state.txt",
		"ClientCountFile"		=>	"./socketserver_clientcount.txt",
		"MemoryUsageFile"		=>	"./socketserver_memoryusage.txt",
        "IP"					=>	"0.0.0.0",
		"OnDisconnect"			=>	null,
		"ShutdownCommand"		=>	"SHUTDOWN",
		"DisconnectCommand"		=>	"QUIT",
		"ConnectionQueuLimit"	=>	0,
        "Type"					=>	NETWORK_TRANSPORT_PROTOCOL_TCP,
		"EchoCommand"			=>	"ECHO",
		"CommandSeparator"		=>	" ",
		"ArgumentSeparator"		=>	" ",
		"ReadLength"			=>	1024 * 1024, // How many bytes to read from a cient at a time
		"ReadTimeout"			=>	20, // Waits a maximum of 60 seconds for socket activity
		"ServerCommand"			=>	"SERVER",
		"StatusFile"			=>	"./socketserver_status.json",
		"LogFile"				=>	"./socketserver_activity.log",
		"Client"				=>	["Socket"=>[], "TerminalType"=>[], "Data"=>[], "PacketNumber"=>[], "LastReceptionTime"=>[], ],
		"ReceivedByte"			=>	0,
		"SentByte"				=>	0,
    ];
    #endregion Property

    #region Variable
    //private $Buffer = [];
    #endregion Variable

    #region Method
    public function __construct($Port = null, $TerminalType = null, $OnData = null, $DataLogPath = null, $OnLog = null, $OnConnect = null, $OnError = null, $OnActivity = null, $StartTimeFile = null, $Timeout = null, $OutputTerminator = null, $CommandFile = null, $StateFile = null, $ClientCountFile = null, $MemoryUsageFile = null, $IP = null, $OnDisconnect = null, $ShutdownCommand = null, $DisconnectCommand = null, $ConnectionQueuLimit = null, $Type = null, $EchoCommand = null, $CommandSeparator = null, $ArgumentSeparator = null, $ReadLength = null, $ReadTimeout = null, $ServerCommand = null, $StatusFile = null, $LogFile = null){
		$this->Property["TerminalType"]["Default"] = ["LogSuffix"=>"\r\n", "IgnoreOnly"=>[chr(10), ], ];

        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

        return true;
    }

	public function Listen(){
		$Result = true;

		if(($MasterSocket = \socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false){
			$this->RaiseEvent("Error", "socket_create() failed! " . socket_strerror(socket_last_error()) . "");
			$Result = false;
		}
		else{
			if(@socket_bind($MasterSocket, $this->Property["IP"], $this->Property["Port"]) === false){
				$this->RaiseEvent("Error", "socket_bind() failed! " . socket_strerror(socket_last_error($MasterSocket)) . "", false);
				$Result = false;
			}
			else{
				if(socket_listen($MasterSocket, $this->Property["ConnectionQueuLimit"] ? $this->Property["ConnectionQueuLimit"] : 0) === false){
					$this->RaiseEvent("Error", "socket_listen() failed! " . socket_strerror(socket_last_error($MasterSocket)) . "");
					$Result = false;
				}
				else{
                    ignore_user_abort(true); // Continue execution even after the client (invoker) is closed
					$OriginalScriptTimeout = ini_get("max_execution_time");
					set_time_limit($this->Property["Timeout"]); // Set maximum execution time
					$StartTime = time();
					$this->Property["ReceivedByte"] = $this->Property["SentByte"] = 0;
					$this->RaiseEvent("Activity", "Server started and listening");

					// Keep listenning for incoming data transmission
					while(!file_exists($this->Property["CommandFile"]) || file_get_contents($this->Property["CommandFile"]) != $this->Property["ShutdownCommand"]){
						$InputSocket = array_merge([$MasterSocket], $this->Property["Client"]["Socket"]);
						$OutputSocket = null;
						$ExceptionSocket = null;

						// Check if we have any socket activity
						if(socket_select($InputSocket, $OutputSocket, $ExceptionSocket, !$this->Property["ReadTimeout"] ? null : $this->Property["ReadTimeout"])){
							if(in_array($MasterSocket, $InputSocket)){ // // Allow new client connection
								if(($NewClientSocket = socket_accept($MasterSocket)) === false){
									$this->RaiseEvent("Error", "socket_accept() failed! " . socket_strerror(socket_last_error($MasterSocket)) . "");
									$Result = false;
									break; // We were unable to accept new connections! Terminate execution
								}
								else{ // New client connection accepted
									$this->Property["Client"]["Socket"][] = $NewClientSocket; // Update client list
									$NewClientSocketKey = array_search($NewClientSocket, $this->Property["Client"]["Socket"]);
									$this->RaiseEvent("Activity", "New Client[{$NewClientSocketKey}] connection accepted", false);

									// Set default properties for the new client
									$this->Property["Client"]["Identifier"][$NewClientSocketKey] = null;

									// Trigger OnConnect event
									if(!is_null($this->Property["OnConnect"]) && ($Response = $this->Property["OnConnect"]($NewClientSocketKey)))$this->Send($NewClientSocketKey, $Response);
								}

								// Remove master socket from reading for data in input sockets
								unset($InputSocket[array_search($MasterSocket, $InputSocket)]);
							}

							foreach($InputSocket as $Socket){ // Iterate through input sockets
								$SocketKey = array_search($Socket, $this->Property["Client"]["Socket"]);

								if(strlen($RawData = @socket_read($Socket, $this->Property["ReadLength"], PHP_BINARY_READ)) == 0){
									// This error may also be generated if the client closed the connection
									// deliberately, thus may not be eligible to be evaluated as an actual error!
									$this->RaiseEvent("Error", "socket_read() failed for Client[{$SocketKey}]! " . socket_strerror(socket_last_error($Socket)) . "", false);
									$this->Disconnect($SocketKey); // Remove associated client
								}
								else{ // Some data is available
									$this->Property["ReceivedByte"] = $this->Property["ReceivedByte"] + ($RawDataLength = strlen($RawData));
									//$this->RaiseEvent("Activity", "{$RawDataLength} byte(s) received from Client[{$SocketKey}]", false);
//var_dump($RawData);
									// Update data packet count per client connection
									$this->Property["Client"]["Buffer"][$SocketKey][] = $RawData;
									$this->Property["Client"]["LastReceptionTime"][$SocketKey] = time();

									// Count packet on buffer receiption
									$this->Property["Client"]["PacketNumber"][$SocketKey] = isset($this->Property["Client"]["PacketNumber"][$SocketKey]) ? $this->Property["Client"]["PacketNumber"][$SocketKey] + 1 : 1;

                                    $BufferData = implode("", $this->Property["Client"]["Buffer"][$SocketKey]);

                                    // Detect terminal type
                                    if($this->Property["Client"]["PacketNumber"][$SocketKey] == 1){
                                        $this->Property["Client"]["TerminalType"][$SocketKey] = array_keys($this->Property["TerminalType"])[0]; // Assume default terminal type

                                        // Check for each terminal type
                                        foreach($this->Property["TerminalType"] as $TerminalTypeName=>$TerminalType){
                                            if(isset($TerminalType["Beginner"]) && $TerminalType["Beginner"] == substr($BufferData, 0, strlen($TerminalType["Beginner"]))){
                                                $this->Property["Client"]["TerminalType"][$SocketKey] = $TerminalTypeName;
                                                break; // Match found, no need to look for more
                                            }
                                        }

                                        if( // Send first reply upon each terminal type
                                                isset($this->Property["TerminalType"][$this->Property["Client"]["TerminalType"][$SocketKey]]["FirstReply"])
                                            &&  $this->Property["TerminalType"][$this->Property["Client"]["TerminalType"][$SocketKey]]["FirstReply"]
                                        ){
                                            $this->Send($SocketKey, $this->Property["TerminalType"][$this->Property["Client"]["TerminalType"][$SocketKey]]["FirstReply"]);
                                        }
                                    }

                                    // Identify fields
                                    if(
                                            $this->Property["Client"]["PacketNumber"][$SocketKey] == 1
                                        &&  isset($this->Property["TerminalType"][$this->Property["Client"]["TerminalType"][$SocketKey]]["IdentifierField"])
                                    ){
                                        // Break field by separator
                                        if(isset($this->Property["TerminalType"][$this->Property["Client"]["TerminalType"][$SocketKey]]["FieldSeparator"])){
                                            $Field = explode($this->Property["TerminalType"][$this->Property["Client"]["TerminalType"][$SocketKey]]["FieldSeparator"], substr($BufferData, isset($this->Property["TerminalType"][$this->Property["Client"]["TerminalType"][$SocketKey]]["Beginner"]) ? strlen($this->Property["TerminalType"][$this->Property["Client"]["TerminalType"][$SocketKey]]["Beginner"]) : 0));
                                            if(count($Field) >= $this->Property["TerminalType"][$this->Property["Client"]["TerminalType"][$SocketKey]]["IdentifierField"])$this->Property["Client"]["Identifier"][$SocketKey] = $Field[$this->Property["TerminalType"][$this->Property["Client"]["TerminalType"][$SocketKey]]["IdentifierField"] - 1];
                                        }
                                        // Identify from fixed length separatorless data
                                        elseif(isset($this->Property["TerminalType"][$this->Property["Client"]["TerminalType"][$SocketKey]]["IdentifierFieldLength"])){
                                            $IdentifierField = substr($BufferData, $this->Property["TerminalType"][$this->Property["Client"]["TerminalType"][$SocketKey]]["IdentifierField"] - 1, $this->Property["TerminalType"][$this->Property["Client"]["TerminalType"][$SocketKey]]["IdentifierFieldLength"]);

                                            if(isset($this->Property["TerminalType"][$this->Property["Client"]["TerminalType"][$SocketKey]]["IdentifierInHEX"])){
                                                $IdentifierHEX = array();

                                                for($IdentifierByteCounter = 0; $IdentifierByteCounter < strlen($IdentifierField); $IdentifierByteCounter++){
                                                    $IdentifierHEX[] = str_pad(dechex(ord($IdentifierField[$IdentifierByteCounter])), 2, "0", STR_PAD_LEFT);
                                                }

                                                // Do not convert Identifier into a number, device identifiers can be of any type (should be)
                                                $IdentifierField = implode("", $IdentifierHEX);
                                            }

                                            $this->Property["Client"]["Identifier"][$SocketKey] = $IdentifierField;
                                        }
                                        // Can't detect an identifier!
                                        else{

                                        }
                                    }

									if( // Trigger data events
										// If this is the first packet and the start marker is found
                                            (
                                                    $this->Property["Client"]["PacketNumber"][$SocketKey] == 1
                                                &&  isset($this->Property["TerminalType"][$this->Property["Client"]["TerminalType"][$SocketKey]]["Beginner"])
                                                &&  substr($BufferData, 0, strlen($this->Property["TerminalType"][$this->Property["Client"]["TerminalType"][$SocketKey]]["Beginner"])) == $this->Property["TerminalType"][$this->Property["Client"]["TerminalType"][$SocketKey]]["Beginner"]
                                            )
										// Or no end marker is defined
                                        ||  !isset($this->Property["TerminalType"][$this->Property["Client"]["TerminalType"][$SocketKey]]["PacketEndMarker"])
										// Or the end marker is found
                                        ||  $this->Property["TerminalType"][$this->Property["Client"]["TerminalType"][$SocketKey]]["PacketEndMarker"] == substr($BufferData, strlen($BufferData) - strlen($this->Property["TerminalType"][$this->Property["Client"]["TerminalType"][$SocketKey]]["PacketEndMarker"]))
                                    ){
                                        // Check required data length
                                        if(!$this->Property["TerminalType"][$this->Property["Client"]["TerminalType"][$SocketKey]]["MinimumPacketLength"] || (strlen($BufferData) >= $this->Property["TerminalType"][$this->Property["Client"]["TerminalType"][$SocketKey]]["MinimumPacketLength"])){
                                            $this->TriggerOnData($SocketKey);
                                            $this->TriggerOnLog($SocketKey, $BufferData);

                                            $this->Property["Client"]["Buffer"][$SocketKey] = []; // Reset buffer
                                        }
                                    }
								}
							}

							$this->SetStatus( // Update server status
								$StartTime, // Socket listenning start time
								SOCKET_STATE_RUNNING, // Server is listenning or not
								count($this->Property["Client"]["Socket"]), // Number of client connection
								memory_get_usage(), // Current memory usage of this application
								function_exists("sys_getloadavg") ? sys_getloadavg()[0] : 0 // Current overall system load
							);
						}
					}

					file_put_contents($this->Property["CommandFile"], null); // Clear command file
					socket_close($MasterSocket); // Close master socket

					foreach($this->Property["Client"]["Socket"] as $SocketKey=>$Socket){ // Close client connections
						$this->Send($SocketKey, "Shutting down{$this->Property["OutputTerminator"]}");
						$this->Disconnect($SocketKey);
					}

					$Duration["Seconds"] = time() - $StartTime;
					$Duration["Hour"] = intval($Duration["Seconds"] / 60 / 60);
					$Duration["HourSeconds"] = $Duration["Hour"] * 60 * 60;
					$Duration["Minute"] = intval(($Duration["Seconds"] - $Duration["HourSeconds"]) / 60);
					$Duration["Second"] = $Duration["Seconds"] - $Duration["HourSeconds"] - ($Duration["Minute"] * 60);

					$this->RaiseEvent("Activity", "Server stopped after {$Duration["Hour"]}h {$Duration["Minute"]}m {$Duration["Second"]}s");
					$this->SetStatus($StartTime, null, 0, 0, 0); // Reset server status
					set_time_limit($OriginalScriptTimeout); // Reset maximum execution time to original
				}
			}
		}

		return $Result;
	}

	public function Disconnect($Key){ // Remove client
		if(isset($this->Property["Client"]["Socket"][$Key])){
			socket_close($this->Property["Client"]["Socket"][$Key]); // Close client connection

			// Process partial data buffer
			if(isset($this->Property["Client"]["Buffer"][$Key]) && count($this->Property["Client"]["Buffer"][$Key])){
				// Count packet on buffer receiption
				$this->Property["Client"]["PacketNumber"][$Key] = isset($this->Property["Client"]["PacketNumber"][$Key]) ? $this->Property["Client"]["PacketNumber"][$Key] + 1 : 1;

                $this->TriggerOnData($Key);
                $this->TriggerOnLog($Key, implode("", $this->Property["Client"]["Buffer"][$Key]));
			}

			// Clear information of the disconnected socket
			unset($this->Property["Client"]["Socket"][$Key]);
			unset($this->Property["Client"]["Buffer"][$Key]);
			unset($this->Property["Client"]["PacketNumber"][$Key]);
			unset($this->Property["Client"]["LastReceptionTime"][$Key]);

			$this->RaiseEvent("Activity", "Client[{$Key}] removed and socket closed", false);
			$Result = true;
		}
		else{
			$this->RaiseEvent("Error", "Client[{$Key}] doesn't exist!");
			$Result = false;
		}

		// Trigger OnDisconnect event
		if(!is_null($this->Property["OnDisconnect"]))$this->Property["OnDisconnect"]($Key);

		return $Result;
	}

	public function Send($Key, $Message){
		$Result = true;

		if(@socket_write($this->Property["Client"]["Socket"][$Key], $Message, $MessageLength = strlen($Message)) === false){
			// This error can happen if the connection has been dropped thus may not be a real error!
			$Result = false;
			$this->RaiseEvent("Error", "socket_write() failed! " . socket_strerror(socket_last_error($this->Property["Client"]["Socket"][$Key])) . "", false);
			$this->Disconnect($Key); // Remove associated client
		}
		else{
			$this->Property["SentByte"] = $this->Property["SentByte"] + $MessageLength;
			//$this->RaiseEvent("Activity", "{$MessageLength} byte(s) sent to Client[{$Key}]", false);
		}

		return $Result;
	}
    #endregion Method

    #region Property
    public function Port($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function TerminalType($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			// Add missing attributes to reduce 'isset()' check later (especially in a loop)
			if(is_array($this->Property[__FUNCTION__]))foreach($this->Property[__FUNCTION__] as $Key=>$TerminalType){
				if(!isset($this->Property[__FUNCTION__][$Key][$Attribute = "FirstReply"]))$this->Property[__FUNCTION__][$Key][$Attribute] = null;
				if(!isset($this->Property[__FUNCTION__][$Key][$Attribute = "LogSuffix"]))$this->Property[__FUNCTION__][$Key][$Attribute] = null;
				if(!isset($this->Property[__FUNCTION__][$Key][$Attribute = "MinimumPacketLength"]))$this->Property[__FUNCTION__][$Key][$Attribute] = null;
				if(!isset($this->Property[__FUNCTION__][$Key][$Attribute = "PacketSeparator"]))$this->Property[__FUNCTION__][$Key][$Attribute] = null;
			}

            $Result = true;
        }

        return $Result;
    }

    public function OnData($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function DataLogPath($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function OnLog($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function OnConnect($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function OnError($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function OnActivity($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function StartTimeFile($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Timeout($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function OutputTerminator($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function DiscardOnlyLineFeed($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function CommandFile($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function StateFile($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function ClientCountFile($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function MemoryUsageFile($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function IP($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function OnDisconnect($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function ShutdownCommand($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function DisconnectCommand($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function ConnectionQueuLimit($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Type($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function EchoCommand($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function CommandSeparator($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function ArgumentSeparator($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function ReadLength($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function ReadTimeout($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function ServerCommand($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function StatusFile($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function LogFile($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Client($Key){
		$Result = [
			"TerminalType"   =>	$this->Property[__FUNCTION__]["TerminalType"][$Key],
			"Identifier"     =>	$this->Property[__FUNCTION__]["Identifier"][$Key],
			"Data"		     =>	implode("", $this->Property[__FUNCTION__]["Buffer"][$Key]),
			"Number"         =>	$this->Property[__FUNCTION__]["PacketNumber"][$Key],
			"Time"           =>	$this->Property[__FUNCTION__]["LastReceptionTime"][$Key],
		];

        return $Result;
    }

    public function ReceivedByte(){
         $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function SentByte(){
         $Result = $this->Property[__FUNCTION__];

        return $Result;
    }
    #endregion Property

	#region Function
    private function TriggerOnData($Key){
        if(!is_null($this->Property["OnData"]) && $OnDataResponse = $this->Property["OnData"]($Key, $this->Client($Key)))$this->Send($Key, $OnDataResponse);

        return true;
    }

    private function TriggerOnLog($Key, $Data){
        if($this->Property["DataLogPath"]){
            $LogFileName = "{$this->Property["Client"]["TerminalType"][$Key]}_{$this->Property["Client"]["Identifier"][$Key]}.log";
            $NewLogFileName = is_null($this->Property["OnLog"]) ? $LogFileName : $this->Property["OnLog"]($LogFileName, $this->Property["Client"]["Identifier"][$Key], $this->Property["Client"]["TerminalType"][$Key], $Key);
            $LogFileName = $NewLogFileName === false ? null : ($NewLogFileName === true ? $LogFileName : $NewLogFileName);

            if($LogFileName)file_put_contents("{$this->Property["DataLogPath"]}{$LogFileName}", "{$this->Property["TerminalType"][$this->Property["Client"]["TerminalType"][$Key]]["PacketSeparator"]}{$Data}", FILE_APPEND);
        }

        return true;
    }

	private function RaiseEvent($Name, $Message, $Log = true){
		if($Log){ // Keep log of the event
			// Delete old log file if older than an hour
			if(file_exists($this->Property["LogFile"]) && (time() - filemtime($this->Property["LogFile"])) > 60 * 60)unlink($this->Property["LogFile"]);
			file_put_contents($this->Property["LogFile"], date("Y-m-d H:i:s") . " [{$Name}] {$Message}\n", FILE_APPEND);
		}

		$Result = is_null($this->Property[$EventName = "On{$Name}"]) ? true : $this->Property[$EventName]($Message);

		return $Result;
	}

	private function SetStatus($StartTime, $State, $ClientCount, $MemoryUsage, $SystemLoad){
		$Result = file_put_contents($this->Property["StatusFile"], json_encode([ // Update status file
			"StartTime"=>$StartTime, // Socket listenning start time
			"ServerState"=>$State, // Server is listenning or not
			"ClientCount"=>$ClientCount, // Number of client connection
			"ReceivedByte"=>$this->Property["ReceivedByte"], // Total bytes received during current session
			"SentByte"=>$this->Property["SentByte"],  // Total bytes sent during current session
			"MemoryUsage"=>$MemoryUsage, // Current memory usage of this application
			"SystemLoad"=>$SystemLoad, // Current overall system load
			"TimeZone"=>ini_get("date.timezone"), // Application time zone
			"StatusTime"=>time(), // Update time of this status
			"OSProcessID"=>getmypid(), // Self process ID
		]));

		return $Result;
	}
	#endregion Function
}
?>