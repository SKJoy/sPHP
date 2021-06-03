<?php
/*
    Name:           HTML UI Input
    Purpose:        User object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 12, 2018 10:51 AM
*/

namespace sPHP\HTML\UI;

class Input{
    private $Property = [
        "Name"						=>	null,
        "Width"						=>	null,
        "DefaultValue"				=>	null,
        "Required"					=>	false,
        "Type"						=>	\sPHP\INPUT_TYPE_TEXT,
        "CSSSelector"				=>	null,
        "Placeholder"				=>	null,
		"EventHandlerJavaScript"	=>	[],
        "ID"						=>	null,
		"Step"						=>	null,
		"Minimum"					=>	null,
		"Maximum"					=>	null,
		"ReadOnly"					=>	false,
        "HTML"						=>	null,
    ];

    #region Method
    public function __construct($Name = null, $Width = null, $DefaultValue = null, $Required = null, $Type = null, $CSSSelector = null, $Placeholder = null, $EventHandlerJavaScript = null, $ID = null, $Step = null, $Minimum = null, $Maximum = null, $ReadOnly = null){
		$this->Property["ID"] = "Input_" . \sPHP\GUID() . "";

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

			if(!$this->Property["ID"])$this->Property["ID"] = "{$this->Property[__FUNCTION__]}_" . \sPHP\GUID() . "";
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

    public function Type($Value = null){
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

    public function Step($Value = null){
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

    public function Minimum($Value = null){
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

    public function ReadOnly($Value = null){
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
			if(is_array($this->Property["EventHandlerJavaScript"]))foreach(array_filter($this->Property["EventHandlerJavaScript"]) as $Event=>$Code)if(trim($Code))$EventHandler[] = "{$Event}=\"{$Code}\"";

			$DefaultValueFileInformation = pathinfo($this->Property["DefaultValue"]);

			$this->Property[__FUNCTION__] = ($this->Property["Type"] == \sPHP\INPUT_TYPE_FILE && $this->Property["DefaultValue"] ? "
				<div class=\"InputExistingFile\">
					" . ($this->Property["DefaultValue"] && substr($this->Property["DefaultValue"], strlen($this->Property["DefaultValue"]) - 1, 1) != "/" ? "
						<a href=\"{$this->Property["DefaultValue"]}\" target=\"_blank\">" . (isset($DefaultValueFileInformation["extension"]) && in_array(strtolower($DefaultValueFileInformation["extension"]), array_filter(explode(",", str_replace(" ", null, "bmp, jpg, jpeg, gif, png, tif, webp, wmf")))) ? "<img src=\"{$this->Property["DefaultValue"]}\" alt=\"Document\">" : "▼ Download existing") . "</a>

						<label>
							<input type=\"checkbox\" name=\"__DeleteExistingFile_{$this->Property["Name"]}\">
							<span class=\"Caption\">Delete existing</span>
						</label>
					" : null) . "
				</div>
			" : null) . "<input" . ($this->Property["ID"] ? " id=\"{$this->Property["ID"]}\"" : null) . " type=\"" . strtolower($this->Property["Type"]) . "\" name=\"{$this->Property["Name"]}\" value=\"" . ($this->Property["Type"] == \sPHP\INPUT_TYPE_FILE ? null : \sPHP\SetVariable($this->Property["Name"], $this->Property["DefaultValue"])) . "\"" . ($this->Property["Required"] ? " required" : null) . "" . ($this->Property["Placeholder"] ? " placeholder=\"{$this->Property["Placeholder"]}\"" : null) . "" . ($this->Property["ReadOnly"] ? " readonly" : null) . "" . ($this->Property["CSSSelector"] ? " class=\"{$this->Property["CSSSelector"]}\"" : null) . " style=\"" . implode(" ", array_filter([
				$this->Property["Width"] ? "width: {$this->Property["Width"]}" . (strpos($this->Property["Width"], "%") === false ? "px" : null) . ";" : null,
			])) . "\"" . ($this->Property["Step"] ? " step=\"{$this->Property["Step"]}\"" : null) . (strlen($this->Property["Minimum"]) ? " min=\"{$this->Property["Minimum"]}\"" : null) . ($this->Property["Maximum"] ? " max=\"{$this->Property["Maximum"]}\"" : null) . (isset($EventHandler) ? " " . implode(" ", $EventHandler) . "" : null) . " />";
		}

		$Result = $this->Property[__FUNCTION__];

		return $Result;
    }
    #endregion Property
}
?>