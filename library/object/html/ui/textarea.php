<?php
/*
    Name:           HTML UI Textarea
    Purpose:        User object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 12, 2018 10:51 AM
*/

namespace sPHP\HTML\UI;

class Textarea{
    private $Property = [
        "Name"						=>	null,
        "Width"						=>	595,
        "Height"					=>	75,
        "DefaultValue"				=>	null,
        "Required"					=>	false,
        "CSSSelector"				=>	null,
        "Placeholder"				=>	null,
		"EventHandlerJavaScript"	=>	[],
        "ID"						=>	null,
        "HTML"						=>	null,
    ];

    #region Method
    public function __construct($Name = null, $Width = null, $Height = null, $DefaultValue = null, $Required = null, $CSSSelector = null, $Placeholder = null, $EventHandlerJavaScript = null, $ID = null){
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

			if(!$this->Property["ID"])$this->Property["ID"] = $this->Property[__FUNCTION__];
			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function Width($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function Height($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["HTML"] = null;

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

			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function Required($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function CSSSelector($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function Placeholder($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function EventHandlerJavaScript($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function ID($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function HTML(){
		if(is_null($this->Property[__FUNCTION__])){
			if(is_array($this->Property["EventHandlerJavaScript"])){
				foreach(array_filter($this->Property["EventHandlerJavaScript"]) as $EventName => $EventHandler){
					$EventHandlerHTML[] = "{$EventName}=\"{$EventHandler}\"";
				}
			}

			$this->Property[__FUNCTION__] = "<textarea id=\"{$this->Property["ID"]}\" name=\"{$this->Property["Name"]}\"" . ($this->Property["Required"] ? " required" : null) . " placeholder=\"{$this->Property["Placeholder"]}\" class=\"{$this->Property["CSSSelector"]}\" style=\"" . implode(" ", array_filter([
				$this->Property["Width"] ? "width: {$this->Property["Width"]}" . (strpos($this->Property["Width"], "%") === false ? "px" : null) . ";" : null,
				$this->Property["Height"] ? "height: {$this->Property["Height"]}" . (strpos($this->Property["Height"], "%") === false ? "px" : null) . ";" : null,
			])) . "\"" . (isset($EventHandlerHTML) ? " " . implode(" ", $EventHandlerHTML) . " " : null) . ">" . \sPHP\SetVariable($this->Property["Name"], $this->Property["DefaultValue"]) . "</textarea>";
		}

		$Result = $this->Property[__FUNCTION__];

		return $Result;
    }
    #endregion Property
}
?>