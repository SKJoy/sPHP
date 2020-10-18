<?php
/*
    Name:           Content
    Purpose:        Content object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 15, 2018 11:17 PM
*/

namespace sPHP;

class Content{
    #region Property
    private $Property = [
        "Name"				=>	null,
		"DefaultValue"		=>	null,
        "Path"				=>	"./content/",
		"Input"				=>	INPUT_TYPE_TEXTAREA,
        "Language"			=>	null,
		"FileName"			=>	null,
		"Value"				=>	null,
		"EditURL"			=>	"./?_Script=utility/content/input",
		"AnchorID"			=>	null,
		"EditAnchor"		=>	null,
    ];
    #endregion Property

    #region Variable
    private static $Cache = [];
    #endregion Variable

    #region Method
    public function __construct($Name = null, $DefaultValue = null, $Path = null, $Input = null, $Language = null, $FileName = null){
		$this->Property["Language"] = new Language();
		$this->Property["AnchorID"] = "ContentAnchorID_" . GUID() . "";

        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

        return true;
    }
    #endregion Method

    #region Property
    public function Name($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Reset();

            $Result = true;
        }

        return $Result;
    }

    public function DefaultValue($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Path($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Input($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Language($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function FileName($Value = null){
        if(is_null($Value)){
			if(is_null($this->Property[__FUNCTION__]))$this->Property[__FUNCTION__] = "" . ValidFileName(strtolower($this->Property["Name"])) . "_" . strtolower($this->Property["Language"]->HTMLCode()) . "";

            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Value($Value = null){
        $File = "{$this->Property["Path"]}{$this->FileName()}.php";
        //var_dump("Content file = {$File}");

        if(is_null($Value)){ // GET
			if(is_null($this->Property[__FUNCTION__])){
				if(isset(self::$Cache["Value"][$this->Property["FileName"]])){
                    //var_dump("Serve content for '{$this->Property["Name"]}' from cache");
					$this->Property[__FUNCTION__] = self::$Cache["Value"][$this->Property["FileName"]];
				}
				else{
					if(file_exists($File)){ // File exists
                        //var_dump("Serve content for '{$this->Property["Name"]}' from file");
						require $File;
						$this->Property[__FUNCTION__] = unserialize(base64_decode($___Content));

						if(is_array($this->Property["DefaultValue"])){
							if(is_array($this->Property[__FUNCTION__])){
								foreach(($DefaultValueExtraItem = array_diff_key($this->Property["DefaultValue"], $this->Property[__FUNCTION__])) as $Key=>$Value)$this->Property[__FUNCTION__][$Key] = $this->Property["DefaultValue"][$Key];
								if(count($DefaultValueExtraItem))$this->Value($this->Property[__FUNCTION__]);
							}
							else{
								$this->Value($this->Property["DefaultValue"]);
							}
						}
					}
                    else{ // File doesn't exist
                        //var_dump("Content file '{$File}' not found. Did you provide with Path property?");
                        //var_dump("Serve content for '{$this->Property["Name"]}' as default");
						//$this->Value(is_null($this->Property["DefaultValue"]) ? "" : $this->Property["DefaultValue"]);
						$this->Property[__FUNCTION__] = $this->Property["DefaultValue"];
					}

					self::$Cache["Value"][$this->Property["FileName"]] = $this->Property[__FUNCTION__];
				}
			}

			$Result = $this->Property[__FUNCTION__];
        }
        else{ // Set
            //var_dump("Write content for '{$this->Property["Name"]}'");
            $this->Property[__FUNCTION__] = self::$Cache["Value"][$this->Property["FileName"]] = $Value;
			$Result = file_put_contents($File, "<?php\n	\$___Content = \"" . str_replace(["\\", "\$", "\"", ], ["\\\\", "\\\$", "\\\"", ], base64_encode(serialize($this->Property[__FUNCTION__]))) . "\";\n\n	if(count(debug_backtrace()) == 0)print \"<html><body style=\\\"margin: 60px; color: Red; font-family: Sans-serif, Verdana, Tahoma, Arial; font-size: 24px; text-align: center;\\\">Please please please! I beg you, don't screw me.</body></html>\";\n?>") === false ? false : true;
        }

        return $Result;
    }

    public function EditURL(){
		if(is_null($this->Property[__FUNCTION__]))$this->Property[__FUNCTION__] = "./?_Script=utility/content/input&Name=" . urlencode($this->Property["Name"]) . "&LanguageHTMLCode={$this->Property["Language"]->HTMLCode()}&Input=" . (is_array($this->Property["Input"]) ? implode(",", $this->Property["Input"]) : $this->Property["Input"]) . "&Field=" . (is_array($this->Property["DefaultValue"]) ? urlencode(implode("\n", array_keys($this->Property["DefaultValue"]))) : "Value") . "&AnchorID={$this->Property["AnchorID"]}";

        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function AnchorID(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

	public function EditAnchor($Hide = false, $NewWindow = false){
		if(is_null($this->Property[__FUNCTION__]))$this->Property[__FUNCTION__] = "<a href=\"{$this->EditURL()}\" title=\"Edit {$this->Name()}\"" . ($NewWindow ? " target=\"_blank\"" : null) . " class=\"ContentEditAnchor\">✎</a>";

		$Result = $Hide ? null : $this->Property[__FUNCTION__];

        return $Result;
	}
    #endregion Property

	#region Function
	private function Reset(){
		$Result = true;

		$this->Property["FileName"] = null;
		$this->Property["EditURL"] = null;
		$this->Property["EditAnchor"] = null;
		$this->Property["Value"] = null;

		return $Result;
	}
	#endregion Function
}
?>