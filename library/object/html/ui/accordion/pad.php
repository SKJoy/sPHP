<?php
/*
    Name:           HTML UI Accordion Pad
    Purpose:        User object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 12, 2018 10:51 AM
*/

namespace sPHP\HTML\UI\Accordion;

class Pad{
    #region Property variable
    private $Property = [
        "Item"						=>	[],
		"Caption"					=>	null,
		"Icon"						=>	null,
        "Tooltip"					=>	null,
		"IconBaseURL"			=>	null,
		"Key"						=>	null,
		"AccordionName"				=>	null,
		"SinglePad"					=>	false,
		"Default"					=>	false,
        "HTML"						=>	null,
    ];
    #endregion Property variable

    #region Method
    public function __construct($Item = null, $Caption = null, $Icon = null, $Tooltip = null, $IconBaseURL = null, $Key = null, $AccordionName = null, $SinglePad = null, $Default = null){
        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

        return true;
    }
    #endregion Method

    #region Property
    public function Item($Value = null){
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

    public function IconBaseURL($Value = null){
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

    public function SinglePad($Value = null){
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

    public function HTML(){
		if(!$this->Property[__FUNCTION__]){
			foreach(array_filter($this->Property["Item"]) as $ItemKey => $Item){
				$Item->AccordionName($this->Property["AccordionName"]);
				$Item->PadKey($this->Property["Key"]);

				if($Item->Icon() && strpos($Item->Icon(), "/") === false)$Item->Icon("{$this->Property["IconBaseURL"]}" . strtolower(str_replace(" ", "_", $Item->Icon())) . ".png");
				if(!$Item->Key())$Item->Key($ItemKey);

				$HTML[] = $Item->HTML();
			}

			$IsActive = (!isset($_POST["_{$this->Property["AccordionName"]}_PadKey"]) && $this->Property["Default"]) || (isset($_POST["_{$this->Property["AccordionName"]}_PadKey"]) && $_POST["_{$this->Property["AccordionName"]}_PadKey"] == $this->Property["Key"]) ? true: false;

			$this->Property[__FUNCTION__] = "
				<div class=\"Pad" . ($IsActive ? " Active" : null) . "\">
					<input type=\"" . ($this->Property["SinglePad"] ? "radio" : "checkbox") . "\" name=\"_{$this->Property["AccordionName"]}_Pad\" id=\"_{$this->Property["AccordionName"]}_PadKey[{$this->Property["Key"]}]\"" . ($IsActive ? " checked" : null) . " class=\"Switch\">

					<label for=\"_{$this->Property["AccordionName"]}_PadKey[{$this->Property["Key"]}]\"" . ($this->Property["Tooltip"] && $this->Property["Tooltip"] != $this->Property["Caption"] ? " title=\"{$this->Property["Tooltip"]}\"" : null) . " class=\"Title" . ($this->Property["Icon"] || $this->Property["Caption"] ? null : " Visible") . "\">
						" . ($this->Property["Icon"] ? "<img src=\"{$this->Property["Icon"]}\" alt=\"{$this->Property["Caption"]}\" class=\"Icon\">" : null) . "" . ($this->Property["Caption"] ? "<span class=\"Caption\">{$this->Property["Caption"]}</span>" : null) . "
					</label>

					<div class=\"Option\">" . implode(null, $HTML) . "</div>
				</div>
			";
		}

		$Result = $this->Property[__FUNCTION__];

		return $Result;
    }
    #endregion Property
}
?>