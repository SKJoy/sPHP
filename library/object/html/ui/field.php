<?php
/*
    Name:           HTML UI Field
    Purpose:        HTML UI Field object
    Author:         Broken Arrow (SKJoy2001@GMail.Com)
    Created:  		May, 30, 2018 05:30 PM
    Date modified:  Mon, 30 Jul 2018 17:40:00 GMT+06:00
*/

namespace sPHP\HTML\UI;

class Field{
    #region Property variable
    private $Property = [
        "Content"					=>	null,
        "Caption"					=>	null,
		"NewLine"					=>	false,
		"Separate"					=>	false,
		"CaptionWidth"				=>	null,
		"ContentWidth"				=>	null,
        "CSSSelector"				=>	"Field",
		"Header"					=>	null,
		"Footer"					=>	null,
		"Prefix"					=>	null,
		"Suffix"					=>	null,
		"ContentName"				=>	null,
		"ContentPath"				=>	null,
		"ContentAnchor"				=>	false,
		"ContentLanguage"			=>	null,
        "ID"						=>	"Field",
        "HTML"						=>	null,
    ];
    #endregion Property variable

    #region Method
    public function __construct($Content = null, $Caption = null, $NewLine = null, $Separate = null, $CaptionWidth = null, $ContentWidth = null, $CSSSelector = null, $Header = null, $Footer = null, $Prefix = null, $Suffix = null, $ContentName = null, $ContentPath = null, $ContentAnchor = null, $ContentLanguage = null, $ID = null){
        $this->Property["ContentLanguage"] = new \sPHP\Language();
		$this->Property["ID"] = "Field_" . \sPHP\GUID() . "";

		// Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

        return true;
    }
    #endregion Method

    #region Property
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

    public function NewLine($Value = null){
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

    public function Separate($Value = null){
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

    public function CaptionWidth($Value = null){
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

    public function ContentWidth($Value = null){
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

    public function Header($Value = null){
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

    public function Footer($Value = null){
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

    public function ContentName($Value = null){
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

    public function ContentPath($Value = null){
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

    public function ContentAnchor($Value = null){
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

    public function ContentLanguage($Value = null){
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
			if($this->Property["ContentName"])$Content = new \sPHP\Content("[Object sPHP\HTML\UI\Field] {$this->Property["ContentName"]}", [
				"Caption"	=>	$this->Property["Caption"],
				"Footer"	=>	$this->Property["Footer"],
			], $this->Property["ContentPath"]);

			$this->Property[__FUNCTION__] = "" . ($this->Property["NewLine"] ? "<div class=\"FieldNewLine\"></div>" : ($this->Property["Separate"] && !$this->Property["NewLine"] ? "<div class=\"FieldSeparator\"></div>" : null)) . "<div id=\"{$this->Property["ID"]}\" class=\"{$this->Property["CSSSelector"]}\">
				" . ($this->Property["Header"] ? "<div class=\"Header\">{$this->Property["Header"]}</div>" : null) . "

				<div class=\"Body\">
					" . ($this->Property["Prefix"] ? "<div class=\"Prefix\">{$this->Property["Prefix"]}</div>" : null) . "
					" . ($this->Property["Caption"] ? "<div class=\"Caption\" style=\"" . implode(" ", array_filter([
						$this->Property["CaptionWidth"] ? "width: {$this->Property["CaptionWidth"]}" . (strpos($this->Property["CaptionWidth"], "%") === false ? "px" : null) . ";" : null,
					])) . "\">" . ($this->Property["ContentName"] ? $Content->Value()["Caption"] : $this->Property["Caption"]) . "</div>" : null) . "
					<div class=\"Content\" style=\"" . implode(" ", array_filter([
						$this->Property["ContentWidth"] ? "width: {$this->Property["ContentWidth"]}" . (strpos($this->Property["ContentWidth"], "%") === false ? "px" : null) . ";" : null,
					])) . "\">{$this->Property["Content"]}</div>
					" . ($this->Property["Suffix"] ? "<div class=\"Suffix\">{$this->Property["Suffix"]}</div>" : null) . "
				</div>

				" . (!is_null($this->Property["Footer"]) ? "<div id=\"{$this->Property["ID"]}_Footer\" class=\"Footer\">" . ($this->Property["ContentName"] ? $Content->Value()["Footer"] : $this->Property["Footer"]) . "</div>" : null) . "
				" . ($this->Property["ContentName"] && $this->Property["ContentAnchor"] ? $Content->EditAnchor() : null) . "
			</div>";
		}

		$Result = $this->Property[__FUNCTION__];

		return $Result;
    }
    #endregion Property
}
?>