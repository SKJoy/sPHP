<?php
/*
    Name:           HTML UI Accordion Item
    Purpose:        User object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 12, 2018 10:51 AM
*/

namespace sPHP\HTML\UI\Accordion;

class Item{
    #region Property variable
    private $Property = [
		"Caption"					=>	null,
		"Content"					=>	null,
        "URL"						=>	null,
        "Target"					=>	null,
		"OnClick"					=>	null,
		"Icon"						=>	null,
        "Tooltip"					=>	null,
		"Default"					=>	false,
		"Key"						=>	null,
		"PadKey"					=>	null,
		"AccordionName"				=>	null,
        "HTML"						=>	null,
    ];
    #endregion Property variable

    #region Method
    public function __construct($Caption = null, $Content = null, $URL = null, $Target = null, $OnClick = null, $Icon = null, $Tooltip = null, $Default = null, $Key = null, $PadKey = null, $AccordionName = null){
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

			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function Content($Value = null){
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

    public function OnClick($Value = null){
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

    public function Default($Value = null){
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

    public function Key($Value = null){
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

    public function PadKey($Value = null){
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

    public function AccordionName($Value = null){
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
			$Tag = $this->Property["Caption"] == "" || $this->Property["Content"] ? "div" : "a";
			$CaptionTag = $Tag == "a" ? "span" : "div";
			$IsActive = (!isset($_POST["_{$this->Property["AccordionName"]}_PadKey"]) && !isset($_POST["_{$this->Property["AccordionName"]}_ItemKey"]) && $this->Property["Default"]) || (isset($_POST["_{$this->Property["AccordionName"]}_PadKey"]) && $_POST["_{$this->Property["AccordionName"]}_PadKey"] == $this->Property["PadKey"] && isset($_POST["_{$this->Property["AccordionName"]}_ItemKey"]) && $_POST["_{$this->Property["AccordionName"]}_ItemKey"] == $this->Property["Key"]) ? true : false;

			if($Tag == "a" && $this->Property["URL"]){
				$URLSuffix["Pad"] = "_{$this->Property["AccordionName"]}_PadKey={$this->Property["PadKey"]}";
				$URLSuffix["Item"] = "_{$this->Property["AccordionName"]}_ItemKey={$this->Property["Key"]}";

				$URL = explode("#", $this->Property["URL"]);
				$URL = "{$URL[0]}" . (strpos($URL[0], "?") === false ? "?" : "&") . implode("&", $URLSuffix) . (isset($URL[1]) ? "#{$URL[1]}" : null) . "";
			}

			$this->Property[__FUNCTION__] = "<{$Tag}" . ($Tag == "a" && $this->Property["URL"] ? " href=\"{$URL}\"" : null) . "" . ($Tag == "a" && $this->Property["Target"] ? " target=\"{$this->Property["Target"]}\"" : null) . "" . ($this->Property["Tooltip"] && $this->Property["Tooltip"] != $this->Property["Caption"] ? " title=\"{$this->Property["Tooltip"]}\"" : null) . " class=\"" . ($this->Property["Caption"] == "" ? "Devider" : "Item" . ($IsActive ? " Active" : null) . "") . "\"" . ($this->Property["OnClick"] ? " onclick=\"{$this->Property["OnClick"]}\"" : null) . ">" . ($this->Property["Icon"] ? "<img src=\"" . strtolower($this->Property["Icon"]) . "\" alt=\"{$this->Property["Caption"]}\" class=\"Icon\" xonerror=\"this.src = './image/icon/not_available.png';\">" : null) . "" . ($this->Property["Caption"] ? "<{$CaptionTag} class=\"Caption\">" . ($this->Property["Content"] ? $this->Property["Content"] : $this->Property["Caption"]) . "</{$CaptionTag}>" : null) . "</{$Tag}>";
		}

		$Result = $this->Property[__FUNCTION__];

		return $Result;
    }
    #endregion Property
}
?>