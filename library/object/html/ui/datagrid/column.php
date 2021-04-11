<?php
/*
    Name:           HTML UI Datagrid\Column
    Purpose:        Datagrid Column object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 12, 2018 10:51 AM
*/

namespace sPHP\HTML\UI\Datagrid;

class Column{
    private $Property = [
        "Name"						=>	null,
        "Caption"					=>	null,
        "Type"						=>	\sPHP\FIELD_TYPE_TEXT,
		"Align"						=>	null,
        "DataType"					=>	null,
        "DateFormat"				=>	"M j, Y",
        "TimeFormat"				=>	"H:i:s",
		"Sortable"					=>	true,
		"IconPrefix"				=>	null,
		"Icon"						=>	null,
        "Target"					=>	null,
        "Prefix"                    =>  null, // Content to show before/left value
        "Suffix"                    =>  null,  // Content to show after/right value
        "Template"                  =>  null, // String template to be used replacing placeholders with record values
        "TemplateHideEmpty"         =>  true, // Ignore empty value and hide template
    ];

    #region Method
    public function __construct($Name = null, $Caption = null, $Type = null, $Align = null, $DataType = null, $DateFormat = null, $TimeFormat = null, $Sortable = null, $IconPrefix = null, $Icon = null, $Target = null, $Prefix = null, $Suffix = null, $Template = null, $TemplateHideEmpty = null){
        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

        return true;
    }

	public function Data($Value = null){
        // Transform Value to Type from Data type
        if($this->Property["Type"] == \sPHP\FIELD_TYPE_DATETIME){ // To date time
			$DateTimeFormat = "{$this->Property["DateFormat"]} {$this->Property["TimeFormat"]}";

			if($this->Property["DataType"] == \sPHP\DATA_TYPE_PHP_DATETIME){ // From PHP time
				$Result = date($DateTimeFormat, $Value);
			}
			else{ // From string
				$Result = $Value ? date($DateTimeFormat, strtotime($Value)) : null;
			}

			//if(!$this->Property["Align"])$this->Property["Align"] = \sPHP\ALIGN_CENTER;
		}
		elseif($this->Property["Type"] == \sPHP\FIELD_TYPE_SHORTDATE){ // To date time
			$DateTimeFormat = "{$this->Property["DateFormat"]}";

			if($this->Property["DataType"] == \sPHP\DATA_TYPE_PHP_DATETIME){ // From PHP time
				$Result = date($DateTimeFormat, $Value);
			}
			else{ // From string
				$Result = $Value ? date($DateTimeFormat, strtotime($Value)) : null;
			}

			//if(!$this->Property["Align"])$this->Property["Align"] = \sPHP\ALIGN_CENTER;
		}
        elseif($this->Property["Type"] == \sPHP\FIELD_TYPE_COLOR){
            $Result = "<span class=\"Color\" style=\"background-color: {$Value};\"></span>";
            if(!$this->Property["Align"])$this->Property["Align"] = \sPHP\ALIGN_CENTER;
        }
		else{ // No transform
			$Result = $Value; // As it is
		}

		// Impose CSS selector(s) if any
		if(isset($CSSSelector))$Result = "<span class=\"" . implode(" ", $CSSSelector) . "\">{$Result}</span>";

		return $Result;
	}
    #endregion Method

    #region Property
    public function Name($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

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

			$Result = true;
        }

        return $Result;
    }

    public function Align($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$Result = true;
        }

        return $Result;
    }

    public function DataType($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$Result = true;
        }

        return $Result;
    }

    public function DateFormat($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$Result = true;
        }

        return $Result;
    }

    public function TimeFormat($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$Result = true;
        }

        return $Result;
    }

    public function Sortable($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$Result = true;
        }

        return $Result;
    }

    public function IconPrefix($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

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

			$Result = true;
        }

        return $Result;
    }

    public function Template($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$Result = true;
        }

        return $Result;
    }

    public function TemplateHideEmpty($Value = null){
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
}
?>