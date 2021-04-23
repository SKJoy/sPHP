<?php
/*
    Name:           HTML\UI\Option
    Purpose:        Option object to be use with HTML Select entity
    Author:         Broken Arrow (SKJoy2001@GMail.Com)
    Created:  		May, 30, 2018 05:30 PM
    Date modified:  Mon, 30 Jul 2018 17:40:00 GMT+06:00
*/

namespace sPHP;

class Option{
    private $Property = [
        "Value"						=>	null,
        "Caption"					=>	null,
        "CSSSelector"				=>	"Field",
        "ID"						=>	"Field",
        "HTML"						=>	null,
    ];

    #region Method
    public function __construct($Value = null, $Caption = null, $CSSSelector = null, $ID = null){
        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

		$this->Property["ID"] = "Field_" . \sPHP\GUID() . "";

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

    public function Deprecated_HTML(){
		if(is_null($this->Property[__FUNCTION__]))$this->Property[__FUNCTION__] = "<option value=\"{$this->Property["Value"]}\" class=\"{$this->Property["CSSSelector"]}\">" . ($this->Property["Caption"] ? $this->Property["Caption"] : $this->Property["Value"]) . "</option>";

		$Result = $this->Property[__FUNCTION__];

		return $Result;
    }
    #endregion Property

	#region Private function
	#endregion Private function
}
?>