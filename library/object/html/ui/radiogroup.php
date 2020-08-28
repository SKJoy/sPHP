<?php
/*
    Name:           HTML\UI\RadioGroup
    Purpose:        Object to create group of radio buttons
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 12, 2018 10:51 AM
*/

namespace sPHP\HTML\UI;

class RadioGroup{
    #region Property variable
    private $Property = [
        "Name"						=>	null,
        "Option"					=>	[],
		"DefaultValue"				=>	null,
        "CSSSelector"				=>	null,
        "HTML"						=>	null,
    ];
    #endregion Property variable

    #region Method
    public function __construct($Name = null, $Option = null, $DefaultValue = null, $CSSSelector = null){
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

			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function Option($Value = null){
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

    public function HTML(){
		if(!$this->Property[__FUNCTION__]){
			\sPHP\SetVariable($this->Property["Name"], is_null($this->Property["DefaultValue"]) ? $this->Property["Option"][0]->Value() : $this->Property["DefaultValue"]);

			foreach($this->Property["Option"] as $Option){
				$Option->Name($this->Property["Name"]);
				$HTML[] = $Option->HTML();
			}

			$this->Property[__FUNCTION__] = "<div class=\"RadioGroup" . ($this->Property["CSSSelector"] ? " {$this->Property["CSSSelector"]}" : null) . "\">" . implode(null, $HTML) . "</div>";
		}

		$Result = $this->Property[__FUNCTION__];

		return $Result;
    }
    #endregion Property
}
?>