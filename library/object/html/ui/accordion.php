<?php
/*
    Name:           HTML UI Accordion
    Purpose:        User object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 12, 2018 10:51 AM
*/

namespace sPHP\HTML\UI;

class Accordion{
    #region Property variable
    private $Property = [
		"Name"						=>	null,
		"Pad"						=>	[],
		"IconBaseURL"				=>	null,
		"CSSSelector"				=>	null,
		"SinglePad"					=>	false,
        "HTML"						=>	null,
    ];
    #endregion Property variable

    #region Method
    public function __construct($Name = null, $Pad = null, $IconBaseURL = null, $CSSSelector = null, $SinglePad = null){
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

    public function Pad($Value = null){
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

    public function HTML(){
		if(!$this->Property[__FUNCTION__]){
			foreach(array_filter($this->Property["Pad"]) as $PadKey => $Pad){
				$Pad->AccordionName($this->Property["Name"]);
				$Pad->SinglePad($this->Property["SinglePad"]);

				if($Pad->Icon() && strpos($Pad->Icon(), "/") === false)$Pad->Icon("{$this->Property["IconBaseURL"]}" . strtolower(str_replace(" ", "_", $Pad->Icon())) . ".png");
				if(!$Pad->IconBaseURL())$Pad->IconBaseURL($this->Property["IconBaseURL"]);
				if(!$Pad->Key())$Pad->Key($PadKey);

				$HTML[] = $Pad->HTML();
			}

			$this->Property[__FUNCTION__] = "<nav class=\"Accordion" . ($this->Property["CSSSelector"] ? " {$this->Property["CSSSelector"]}" : null) . "\">" . implode("", $HTML) . "</nav>";
		}

		$Result = $this->Property[__FUNCTION__];

		return $Result;
    }
    #endregion Property
}
?>