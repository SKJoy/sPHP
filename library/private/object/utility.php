<?php
namespace sPHP;

class Utility{
	private $Property = [
        "Debug"				=>	null,
        "Graphic"			=>	null,
    ];

	#region Private variable
	private $MaxMindGeoIP2Reader = false;
	#endregion Private variable

    #region Method
    public function __construct($Debug = null){
		// Set default property values
		$this->Property["Graphic"] = new Graphic();

        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

        return true;
    }

	public function Upload($Path, $Field = null, $SetPOST = null, $MustRename = null, $AllowedExtension = null, $ForbiddenExtension = null, $ByteLimit = null){
        #region Set default argument values
		if(is_null($SetPOST))$SetPOST = true;
		if(is_null($MustRename))$MustRename = true;
		if(is_null($ForbiddenExtension))$ForbiddenExtension = "asp, aspx, bat, bin, cfm, cfc, com, exe, jsp, pl, py, sh, shtml";
        if(is_null($ByteLimit))$ByteLimit = false;
        #endregion Set default argument values
        //var_dump($_FILES);
		$POSTFileField = array_keys($_FILES);
		if(count($POSTFileField) && !is_dir($Path))mkdir($Path, 0777, true); // Create the target folder it doesn't exist
        //var_dump($POSTFileField);
		foreach(isset($Field) ? array_intersect(explode(",", str_replace(" ", "", $Field)), $POSTFileField) : $POSTFileField as $Field){ //var_dump($Field);
			if(is_array($_FILES[$Field]["name"])){ //var_dump($_FILES[$Field]["name"]);
				foreach($_FILES[$Field]["name"] as $Key => $Value){ //DebugDump($_FILES[$Field]["size"][$Key]);
					$Result[$Field][$Key] = (!$ByteLimit || $_FILES[$Field]["size"][$Key] <= $ByteLimit) ? $this->MoveUploadedItem($Path, $Value, $_FILES[$Field]["tmp_name"][$Key], $MustRename, $AllowedExtension, $ForbiddenExtension) : false;
					if($SetPOST)$_POST[$Field][$Key] = $Result[$Field][$Key];
				}
			}
			else{ //DebugDump($_FILES[$Field]["size"]); //var_dump($Path, $_FILES[$Field]["name"], $_FILES[$Field]["tmp_name"]);
				$Result[$Field] = (!$ByteLimit || $_FILES[$Field]["size"] <= $ByteLimit) ? $this->MoveUploadedItem($Path, $_FILES[$Field]["name"], $_FILES[$Field]["tmp_name"], $MustRename, $AllowedExtension, $ForbiddenExtension) : false;
				if($SetPOST)$_POST[$Field] = $Result[$Field];
			}
		} //var_dump($Result);

		return isset($Result) ? $Result : false;
	}

    public function ValidFileName($Name, $ReplacementCharacter = "_"){
		$Result = str_replace(str_split("~@#%^&*+`={}[];':\"<>?,/|\\ "), $ReplacementCharacter, $Name);

		return $Result;
	}

	public function UniqueFileName($File){
		$FileInformation = pathinfo($File);
		while(file_exists("" . ($File = "{$FileInformation["dirname"]}/{$this->GUID()}{$this->GUID()}" . ($FileInformation["extension"] ? ".{$FileInformation["extension"]}" : null) . "") . ""));

		return $File;
	}

    public function ValidVariableName($Name, $ReplacementCharacter = "_"){
		$Result = str_replace(str_split(".~@#%^&*+`={}[];':\"<>?,/|\\ "), $ReplacementCharacter, $Name);

		return $Result;
	}

	public function CleanPath($Path){
		$Result = true;

		foreach(glob("{$Path}*") as $Item){
			if(is_file($Item)){
				@unlink($Item);
			}
			else{
				$this->CleanPath("{$Item}/");
				@rmdir($Item);
			}
		}

		return $Result;
	}

	public function GUID($Hyphen = false, $CurlyBrace = false){
		/*
			Create a globally unique string

			Hyphen			[BOOLEAN]		=		TRUE = Add hyphens like Microsoft SQL Server UUID like UNIQUE-ID-VALUE; FALSE = Don't use hyphens
			CurlyBrace		[BOOLEAN]		=		TRUE = Wrap within curly braces like {UNIQUE ID}; FALSE = Don't use curly braces
		*/

		$HyphenCharacter = "";
		if($Hyphen)$HyphenCharacter = "-";
		$GUID = sprintf("%04x%04x".$HyphenCharacter."%04x".$HyphenCharacter."%04x".$HyphenCharacter."%04x".$HyphenCharacter."%04x%04x%04x", mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
		if($CurlyBrace)$GUID = "{" . $GUID . "}";

		return strtoupper($GUID);
	}

	public function ListToArray($List, $Separator = null, $DiscardSpace = null, $IgnoreEmpty = null){
		if(is_null($Separator))$Separator = ",";
		if(is_null($DiscardSpace))$DiscardSpace = true;
		if(is_null($IgnoreEmpty))$IgnoreEmpty = true;

		if($DiscardSpace)$List = str_replace(" ", "", $List);
		$List = explode($Separator, trim($List));
		if($IgnoreEmpty)$List = array_filter($List);

		return $List;
	}

	public function IP2Geo($IP = null){
		//var_dump("" . __CLASS__ . "->" . __FUNCTION__ . "('{$IP}')");
		if($this->MaxMindGeoIP2Reader === false){ // Load MaxMindGeoIP2Reader if not already loaded
			#region Extract MaxMind GeoLite2 City database if not exists
			$GeoLite2CityDatabasePath = __DIR__ . "/../../../library/3rdparty/maxmind/";
			$GeoLite2CityDatabase = "{$GeoLite2CityDatabasePath}GeoLite2-City.mmdb";

			if(!file_exists($GeoLite2CityDatabase)){
				//var_dump($GeoLite2CityDatabase, "GeoLite2 City database missing");
				$ZipArchive = new \ZipArchive;
				if($ZipArchive->open("{$GeoLite2CityDatabasePath}GeoLite2-City.zip") === true){
					$ZipArchive->extractTo($GeoLite2CityDatabasePath);
					//var_dump("GeoLite2 City database decompression successful");
					$ZipArchive->close();
				}
				else{
					//var_dump("Failed to decompress MaxMind GeoLite2 City database");
					trigger_error("Failed to decompress MaxMind GeoLite2 City database");
				}
			}
			else{
				//var_dump("GeoLite2 City database found");
			}
			#endregion Extract MaxMind GeoLite2 City database if not exists

			require __DIR__ . "/../../../library/3rdparty/maxmind/geoip2.phar";
			$this->MaxMindGeoIP2Reader = new \GeoIp2\Database\Reader($GeoLite2CityDatabase);
		}

		try{
			$Result = $this->MaxMindGeoIP2Reader->city($IP ? $IP : $_SERVER["REMOTE_ADDR"]);
		}
		catch(\Exception $e){
			$Result = false;
		}

		return $Result;
	}

	public function NumberInWord($num = false){
		// Credit goes to: http://stackoverflow.com/questions/11500088/php-express-number-in-words

		$num = str_replace(array(",", " "), null , trim($num));

		if(!$num)return false;

		$num = (int) $num;
		$words = array();

		$list1 = array(null, "one", "two", "three", "four", "five", "six", "seven", "eight", "nine", "ten", "eleven", "twelve", "thirteen", "fourteen", "fifteen", "sixteen", "seventeen", "eighteen", "nineteen");
		$list2 = array(null, "ten", "twenty", "thirty", "forty", "fifty", "sixty", "seventy", "eighty", "ninety", "hundred");
		$list3 = array(null, "thousand", "million", "billion", "trillion", "quadrillion", "quintillion", "sextillion", "septillion", "octillion", "nonillion", "decillion", "undecillion", "duodecillion", "tredecillion", "quattuordecillion", "quindecillion", "sexdecillion", "septendecillion", "octodecillion", "novemdecillion", "vigintillion");

		$num_length = strlen($num);
		$levels = (int) (($num_length + 2) / 3);
		$max_length = $levels * 3;
		$num = substr("00" . $num, -$max_length);
		$num_levels = str_split($num, 3);

		for($i = 0; $i < count($num_levels); $i++){
			$levels--;
			$hundreds = (int) ($num_levels[$i] / 100);
			$hundreds = ($hundreds ? " {$list1[$hundreds]} hundred" . ($hundreds == 1 ? null : "s") . " " : null);
			$tens = (int) ($num_levels[$i] % 100);
			$singles = null;

			if($tens < 20){
				$tens = ($tens ? " {$list1[$tens]} " : null);
			}
			else{
				$tens = (int)($tens / 10);
				$tens = " {$list2[$tens]} ";
				$singles = (int) ($num_levels[$i] % 10);
				$singles = " {$list1[$singles]} ";
			}

			$words[] = $hundreds . $tens . $singles . (($levels && (int) ($num_levels[$i])) ? " {$list3[$levels]} " : null);
		}

		$commas = count($words);
		if($commas > 1)$commas = $commas - 1;

		return implode(" ", $words);
	}

	public function RandomString($Length = 8, $Number = true, $Lowercase = true, $Uppercase = true, $Symbol = true, $TagSafe = true){
		if($Number)$Character[] = "0123456789";
		if($Lowercase)$Character[] = "abcdefghijklmnopqrstuvwxyz";
		if($Uppercase)$Character[] = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		if($Symbol)$Character[] = "`-=~!@#\$%^&*()_+[]\\{}|;':\",./?";
		if($Symbol && !$TagSafe)$Character[] = "<>";

		if(!isset($Character)){
			var_dump("function " . __FUNCTION__ . "()- At least one scope is required to generate a random string.");
			$RandomString = false;
		}
		else{
			$Character = implode("", $Character);
		    $CharacterLength = strlen($Character);

		    for($CharacterCount = 0; $CharacterCount < $Length; $CharacterCount++)$RandomString[] = $Character[rand(0, $CharacterLength - 1)];

		    $RandomString = implode("", $RandomString);
		}

		return $RandomString;
	}

	public function IsALPHABETIC($Value){
		return preg_match("/^[a-zA-Z ]*$/", $Value);
	}

	public function IsEMAIL($Value){
		return filter_var($Value, FILTER_VALIDATE_EMAIL);
	}

	public function IsNUMBER($Value){
		return is_numeric($Value);
	}

	public function IsNUMERIC($Value){
		return is_numeric($Value);
	}

	public function IsURL($Value){
		return preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $Value);
	}

	public function IsINTEGER($Value){
		return $this->IsNUMBER($Value) && preg_match("/[0-9]/i", $Value);
	}

	public function IsPOSITIVE($Value){
		return $this->IsNUMBER($Value) && $Value > 0;
	}

	public function IsFLOAT($Value){
		return $this->IsNUMBER($Value);
	}

	public function IsNONNEGATIVE($Value){
		return $this->IsNUMBER($Value) && $Value >= 0;
	}

	// Distance unit = Meter
	public function HaversineGreatCircleDistance($FromLatitude, $FromLongitude, $ToLatitude, $ToLongitude, $EarthRadius = 6371000){
		$FromLatitude = deg2rad($FromLatitude);
		$FromLongitude = deg2rad($FromLongitude);
		$ToLatitude = deg2rad($ToLatitude);
		$ToLongitude = deg2rad($ToLongitude);

		return 2 * asin(sqrt(pow(sin(($ToLatitude - $FromLatitude) / 2), 2) + cos($FromLatitude) * cos($ToLatitude) * pow(sin(($ToLongitude - $FromLongitude) / 2), 2))) * $EarthRadius;
    }

	public function SecondToTime($Second = 0){
		$Second = intval($Second);

		$Hour = str_pad(floor($Second / 60 / 60), 2, "0", STR_PAD_LEFT);
		$Minute = str_pad(floor(($Second - ($Hour * 60 * 60)) / 60), 2, "0", STR_PAD_LEFT);
		$Second = str_pad($Second - ($Hour * 60 * 60) - ($Minute * 60), 2, "0", STR_PAD_LEFT);

		return "{$Hour}:{$Minute}:{$Second}";
	}

	// Calculate angle/segment of direction for two points, coordinates; from one to another
    public function PointAngle(
		$X2, // X to
		$Y2, // Y to
		$X1 = null, // X from, defaults to 0
		$Y1 = null, // Y from, defaults to 0
		$Segment = null, // Convert angle into number of segment of angle amount
		$FullCircle = true // Use 360 degree range instead of negative angle value
	){
		if(is_null($X1))$X1 = 0;
		if(is_null($Y1))$Y1 = 0;

		$Angle = rad2deg(atan2($Y2 - $Y1, $X2 - $X1));
		if($Angle < 0 && $FullCircle)$Angle = $Angle + 360;

		$Result = $Segment ? round($Angle / $Segment, 0) : $Angle;

		return $Result;
	}

	public function ReplaceByKey($Content, $Data, $BeginEnclosure = "%", $EndEnclosure = "%"){
		foreach(array_keys($Data) as $Key)$Field[] = "{$BeginEnclosure}{$Key}{$EndEnclosure}";

		return str_replace($Field, $Data, $Content);
	}

	public function CreatePath($Path){
		if(!is_dir($Path))mkdir($Path, 0777, true);

		return true;
    }
    
    public function ArrayToCSV($Data, $Header = true, $Delimiter = ",", $Enclosure = "\"", $EscapeCharacter = "\\"){
        $FileHandle = fopen('php://memory', 'r+'); // Create the file in memory for better performance

        // Dynamic header: Disable | User defined | Auto from row keys
        if($Header !== false)fputcsv($FileHandle, $Header === true && count($Data) ? array_keys($Data[0]) : explode(",", $Header), $Delimiter, $Enclosure, $EscapeCharacter);

        //if(count($Data))fputcsv($FileHandle, array_keys($Data[0]), $Delimiter, $Enclosure, $EscapeCharacter); // Header
        foreach($Data as $Row)fputcsv($FileHandle, $Row, $Delimiter, $Enclosure, $EscapeCharacter); // Rows
        rewind($FileHandle); // Put file read pointer to beginning
        $Result = stream_get_contents($FileHandle);
        fclose($FileHandle); // Relase memory occupied

        return $Result;
    }

    /*
        Get process information from OS
    
        Argument
            ProcessID INT OPTIONAL	Process ID to get information for. Defaults to own process ID if not provided with.
    */
    function OSProcess($ProcessID = null){
        $Process = [ // Initialize process information array
            "ID" => null, 
            "File" => null, 
            "Path" => null, 
            "Filename" => null, 
            "Extension" => null, 
        ];
        
        $Process["ID"] = is_null($ProcessID) ? getmypid() : intval($ProcessID); // Validate and default to own process ID
    
        #region Get process information from OS
        $ExecStatus = false;
        
        if(PHP_OS_FAMILY === "Windows"){ // Command for Windows
            $ExecStatus = exec("tasklist /fi \"pid eq {$Process["ID"]}\"", $ExecInformation, $ExecReturn);
        }
        elseif(PHP_OS_FAMILY === "Linux"){ // Command for Linux
            try{
                $ExecStatus = @exec("ps -f -p {$Process["ID"]}", $ExecInformation, $ExecReturn);
            }
            catch(\Exception $Error){
                $ExecStatus = false;
            }
        }
        else{ // Unknown OS
            $ExecStatus = false;
        }
        #endregion Get process information from OS
    
        if($ExecStatus !== false && is_array($ExecInformation) && count($ExecInformation) > 1){ // OS command successful, populate process information
            $ExecInformation = $ExecInformation[count($ExecInformation) - 1];
            $ExecInformation = array_filter(explode(" ", $ExecInformation));
            $ExecInformation = array_values($ExecInformation); //var_dump($ExecInformation);
            $Process["File"] = $ExecInformation[["Linux" => 6, "Windows" => 0, ][PHP_OS_FAMILY]];
    
            #region Generate additional process information
            $ProcessPathInfo = pathinfo($Process["File"]); //var_dump($ProcessPathInfo);
            $Process["Path"] = "{$ProcessPathInfo["dirname"]}" . (PHP_OS_FAMILY === "Windows" ? "\\" : "/") . "";
            $Process["Filename"] = $ProcessPathInfo["filename"];
            $Process["Extension"] = isset($ProcessPathInfo["extension"]) ? $ProcessPathInfo["extension"] : "";
            #endregion Generate additional process information
        }
    
        return $Process;
    }

    /*
        Kill an Operating system process
    
        Argument
            ProcessID INT REQUIRED	Process ID to kill.
    */
    function OSProcessKill($ProcessID){
        if(PHP_OS_FAMILY === "Linux"){
            $ExecStatus = exec("kill {$ProcessID}", $ExecInformation, $ExecReturn);
        }
        elseif(PHP_OS_FAMILY === "Windows"){
            $ExecStatus = exec("taskkill /F /PID {$ProcessID}", $ExecInformation, $ExecReturn);
        }
        else{
            $ExecStatus = false;
        }
    
        return $ExecStatus !== false ? true : false;
    }

    /*
        Encode special characters in string

        Argument (NULL for default)			Type		Required	Optional	Description
            Value							STRING		REQUIRED				String to encode
            EncodeSymbol					BOOLEAN		OPTIONAL	FALSE		Encode symbols
            EncodePath						BOOLEAN		OPTIONAL	FALSE		Encode path character
            HEX								BOOLEAN		OPTIONAL	TRUE		Encode with double byte HEX character code

        Return
            STRING		Value replacing special characters with double byte HEX code (when HEX = TRUE)
            BOOELAN		FALSE on failure
    */ function EncodeSpecialCharacter(string $Value, ?bool $EncodeSymbol = false, ?bool $EncodePath = false, ?bool $HEX = true, ?string $_CachePath = null){ 
        #region Set default value
        if(is_null($EncodeSymbol))$EncodeSymbol = false;
        if(is_null($EncodePath))$EncodePath = false;
        if(is_null($HEX))$HEX = true;
        if(is_null($_CachePath))$_CachePath = null;
        #endregion Set default value

        static $_Cache = []; // Memory cache

        if($_CachePath){ // Use disk cache
            @mkdir($_CachePath = implode("/", [$_CachePath, "FUNCTION", __FUNCTION__, $_CacheName = implode("/", [ // Ensure cache path exists
                "EncodeSymbol_" . ($EncodeSymbol ? "TRUE" : "FALSE"), 
                "EncodePath_" . ($EncodePath ? "TRUE" : "FALSE"), 
                "HEX_" . ($HEX ? "TRUE" : "FALSE"), 
            ]), ]), 0777, true);
            
            if(file_exists($_CacheFile = "{$_CachePath}/" . md5($Value) . ".cache"))$_Cache[$_CacheName] = json_decode(file_get_contents($_CacheFile)); // Load disk cache
        }

        if(isset($_Cache[$_CacheName])){ // Load from memory cache
            $Result = $_Cache[$_CacheName]; //var_dump("Loaded from cache");
        }
        else{ // No cache found
            #region Function body
            $Result = false; // Default return value; Process & set in Function body section
            
            $Invalid = ":*? \t\r\n" . ($EncodeSymbol ? "~`!@#\$%^&()-_+={[}];\"'|<,>." : null) . ($EncodePath ? "\\/" : null);
        
            if($HEX){ // Encode with double byte HEX character code
                $Encoded = str_split(strtoupper(bin2hex($Invalid)), 2);
                $Invalid = str_split($Invalid, 1);
                //var_dump($Encoded, $Invalid);
        
                $Result = str_replace($Invalid, $Encoded, $Value); // Return
            }
            #endregion Function body
        
            $_Cache[$_CacheName] = $Result; // Create memory cache
            if($_CachePath)file_put_contents($_CacheFile, json_encode($Result)); // Create disk cache
        }
        
        return $Result; // Return
    }

    function VariableSize($Variable){
        // Approximate memory consumption (bytes) of a variable    
        $Size = 0;
    
        if(is_array($Variable)){
            foreach($Variable as $Element)$Size = $Size + VariableSize($Element);
        }
        elseif(is_bool($Variable)){
            $Size = $Size + 1;
        }
        elseif(is_object($Variable)){
            foreach(get_object_vars($Variable) as $Element)$Size = $Size + VariableSize($Element);
        }
        else{
            $Size = $Size + strlen($Variable);
        }
    
        return $Size;
    }
    #endregion Method

    #region Property
    public function Debug($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$Result = true;
        }

        return $Result;
    }

    public function Graphic($Value = null){
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
	private function MoveUploadedItem($Path, $File, $TemporaryFile, $MustRename, $AllowedExtension, $ForbiddenExtension){
		$Result = false;
		$File = pathinfo($File);

		if(
				$File["basename"]
			&&	(!isset($AllowedExtension) || in_array($File["extension"], array_filter(explode(",", str_replace(" ", "", $AllowedExtension)))))
			&&	(!isset($ForbiddenExtension) || !in_array($File["extension"], array_filter(explode(",", str_replace(" ", "", $ForbiddenExtension)))))
		){
			if($MustRename || file_exists("{$Path}" . ($FileName = "{$File["basename"]}") . ""))while(file_exists("{$Path}" . ($FileName = "" . ($MustRename ? null : "{$File["filename"]}") . "{$this->GUID()}{$this->GUID()}" . ($File["extension"] ? ".{$File["extension"]}" : null) . "") . ""));
			$Result = move_uploaded_file($TemporaryFile, "{$Path}{$FileName}") ? $FileName : false;
		}
		else{
			$Result = false;
		}
        //var_dump($Result);
		return $Result;
	}
	#endregion Function
}
?>