<?php
/*
    Name:           HTML\UI\Radio
    Purpose:        HTML Select entity object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 12, 2018 10:51 AM
*/

namespace sPHP\HTML\UI;

class Radio{
    #region Property variable
    private $Property = [
        "Value"						=>	null,
        "Caption"					=>	null,
        "Name"						=>	null,
        "CSSSelector"				=>	"Radio",
		"EventHandlerJavaScript"	=>	[],
        "ID"						=>	null,
        "HTML"						=>	null,
    ];
    #endregion Property variable

    #region Method
    public function __construct($Value = null, $Caption = null, $Name = null, $CSSSelector = null, $EventHandlerJavaScript = null, $ID = null){
        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

        return true;
    }
    #endregion Method

    #region Property
    public function Value($Value = null){
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

    public function Caption($Value = null){
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

    public function Name($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			if(!$this->Property["ID"])$this->Property["ID"] = "{$this->Property[__FUNCTION__]}_" . \sPHP\GUID() . "";
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
		if(!$this->Property[__FUNCTION__]){
			if(is_array($this->Property["EventHandlerJavaScript"]))foreach(array_filter($this->Property["EventHandlerJavaScript"]) as $Handler)if(is_array($Handler) && isset($Handler[1]))$EventHandler[] = "{$Handler[0]}=\"{$Handler[1]}\"";

			$HTML[] = "<input type=\"radio\" id=\"{$this->Property["ID"]}\" name=\"{$this->Property["Name"]}\" value=\"{$this->Property["Value"]}\"" . (isset($_POST[$this->Property["Name"]]) && $_POST[$this->Property["Name"]] == $this->Property["Value"] ? " checked" : null) . "" . (isset($EventHandler) ? " " . implode(" ", $EventHandler) . " " : null) . ">";
			$HTML[] = "<label for=\"{$this->Property["ID"]}\"><span class=\"Caption\">{$this->Property["Caption"]}</label></label>";
			if($this->Property["CSSSelector"])$HTML = ["<div class=\"{$this->Property["CSSSelector"]}\">" . implode(null, $HTML) . "</div>"];

			$this->Property[__FUNCTION__] = implode(null, $HTML);
		}

		$Result = $this->Property[__FUNCTION__];

		return $Result;
    }
    #endregion Property
}
?>