<?php
/*
    Name:           HTML UI Progressbar
    Purpose:        User object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  April 11, 2021 04:46 PM
*/

namespace sPHP\HTML\UI;

class Progressbar{
    private $Property = [
		"Maximum"					=>	100,
		"Value"						=>	0,
		"URL"						=>	null,
		"Target"					=>	null,
        "CSSSelector"				=>	"Progressbar",
        "Tooltip"					=>	"Progress",
        "Width"						=>	"100%",
        "Prefix"					=>	null,
		"Suffix"					=>	"%",
		"EventHandlerJavaScript"	=>	[],
        "ID"						=>	null,
        "HTML"						=>	null,
    ];

    #region Method
    public function __construct($Maximum = null, $Value = null, $URL = null, $Target = null, $CSSSelector = null, $Tooltip = null, $Width = null, $Prefix = null, $Suffix = null, $EventHandlerJavaScript = null, $ID = null){
		#region Set default value
		$this->Property["ID"] = "Progressbar_" . \sPHP\GUID(false, false) . "";
		#endregion Set default value

        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

        return true;
    }
    #endregion Method

    #region Property
    public function Maximum($Value = null){
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

    public function URL($Value = null){
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

    public function Target($Value = null){
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

    public function Prefix($Value = null){
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

    public function Suffix($Value = null){
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
			if(!is_array($this->Property["EventHandlerJavaScript"]) && trim($this->Property["EventHandlerJavaScript"]))$this->Property["EventHandlerJavaScript"] = ["OnClick" => $this->Property["EventHandlerJavaScript"]];
			foreach(array_filter($this->Property["EventHandlerJavaScript"]) as $Event=>$Code)if(trim($Code))$EventHandler[] = "{$Event}=\"{$Code}\"";

			$Style = implode(" ", array_filter([
				$this->Property["Width"] ? "width: {$this->Property["Width"]}" . (strpos($this->Property["Width"], "%") === false ? "px" : null) . ";" : null,
			]));

			$this->Property[__FUNCTION__] = "<a id=\"{$this->Property["ID"]}\"" . ($this->Property["URL"] ? " href=\"{$this->Property["URL"]}\"" : null) . " target=\"{$this->Property["Target"]}\" title=\"{$this->Property["Tooltip"]}\" class=\"{$this->Property["CSSSelector"]}\" style=\"{$Style}\"" . (isset($EventHandler) ? " " . implode(" ", $EventHandler) . " " : null) . ">
				<span id=\"{$this->Property["ID"]}Bar\" class=\"Bar\" style=\"width: " . ($this->Property["Value"] / $this->Property["Maximum"] * 
				100) . "%\">
					<span id=\"{$this->Property["ID"]}BarData\" class=\"Data\">" . implode(null, [
						"<span id=\"{$this->Property["ID"]}BarDataPrefix\" class=\"Prefix\">{$this->Property["Prefix"]}</span>", 
						"<span id=\"{$this->Property["ID"]}BarDataValue\" class=\"Value\">{$this->Property["Value"]}</span>", 
						"<span id=\"{$this->Property["ID"]}BarDataSuffix\" class=\"Suffix\">{$this->Property["Suffix"]}</span>", 
					]) . "</span>
				</span>
				<span id=\"{$this->Property["ID"]}Data\" class=\"Data\">" . implode(null, [
					"<span id=\"{$this->Property["ID"]}DataPrefix\" class=\"Prefix\">{$this->Property["Prefix"]}</span>", 
					"<span id=\"{$this->Property["ID"]}DataValue\" class=\"Value\">{$this->Property["Value"]}</span>", 
					"<span id=\"{$this->Property["ID"]}DataSuffix\" class=\"Suffix\">{$this->Property["Suffix"]}</span>", 
				]) . "</span>
			</a>";
		}

		$Result = $this->Property[__FUNCTION__];

		return $Result;
    }
    #endregion Property
}
?>