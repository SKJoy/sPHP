<?php
/*
    Name:           HTML UI Button
    Purpose:        User object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 12, 2018 10:51 AM
*/

namespace sPHP\HTML\UI;

class Button{
    #region Property variable
    private $Property = [
		"Caption"					=>	null,
		"Type"						=>	\sPHP\BUTTON_TYPE_SUBMIT,
        "Name"						=>	null,
		"Value"						=>	null,
		"EventHandlerJavaScript"	=>	[],
        "Width"						=>	null,
        "CSSSelector"				=>	null,
        "ID"						=>	null,
        "Tooltip"					=>	null,
        "Icon"						=>	null,
        "HTML"						=>	null,
    ];
    #endregion Property variable

    #region Method
    public function __construct($Caption = null, $Type = null, $Name = null, $Value = null, $EventHandlerJavaScript = null, $Width = null, $CSSSelector = null, $ID = null, $Tooltip = null, $Icon = null){
        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

        return true;
    }
    #endregion Method

    #region Property
    public function Caption($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			if(is_null($this->Property["Value"]))$this->Property["Value"] = $this->Property[__FUNCTION__];
			$this->Property["HTML"] = null;

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

			if(is_null($this->Property["Name"]) && $this->Property[__FUNCTION__] == \sPHP\BUTTON_TYPE_SUBMIT)$this->Property["Name"] = "btnSubmit";
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

    public function Tooltip($Value = null){
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

    public function Icon($Value = null){
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
            foreach(is_array($this->Property["EventHandlerJavaScript"]) ? $this->Property["EventHandlerJavaScript"] : ["OnClick" => $this->Property["EventHandlerJavaScript"], ] as $Event => $Handler)$EventHandler[] = "{$Event}=\"{$Handler}\"";

            $this->Property[__FUNCTION__] = "<button" . ($this->Property["ID"] ? " id=\"{$this->Property["ID"]}\"" : null) . " type=\"" . strtolower($this->Property["Type"]) . "\" name=\"{$this->Property["Name"]}\" value=\"{$this->Property["Value"]}\"" . ($this->Property["Tooltip"] ? " title=\"{$this->Property["Tooltip"]}\"" : null) . " class=\"{$this->Property["CSSSelector"]}\" style=\"" . implode(" ", array_filter([
				$this->Property["Width"] ? "width: {$this->Property["Width"]}" . (strpos($this->Property["Width"], "%") === false ? "px" : null) . ";" : null,
			])) . "\"" . (isset($EventHandler) ? "  " : null) . ">" . implode(" ", array_filter([
                $this->Property["Icon"] ? "<img src=\"{$this->Property["Icon"]}\" alt=\"{$this->Property["Caption"]}\" class=\"Icon\">" : null, 
                $this->Property["Caption"] ? "<span class=\"Caption\">{$this->Property["Caption"]}</span>" : null, 
            ])) . "</button>";
		}

		$Result = $this->Property[__FUNCTION__];

		return $Result;
    }
    #endregion Property
}
?>