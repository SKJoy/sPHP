<?php
/*
    Name:           HTML UI Menu
    Purpose:        HTML UI Menu object
    Author:         Broken Arrow (SKJoy2001@GMail.Com)
    Created:  		May, 30, 2018 05:30 PM
    Date modified:  Mon, 30 Jul 2018 17:40:00 GMT+06:00
*/

namespace sPHP\HTML\UI;

class Field{
    #region Property variable
    private $Property = [
		"Children"					    =>	[],
		"IconBaseURL"				=>	null,
        "CSSSelector"				=>	null,
        "PadID"						=>	null,
        "HTML"						=>	null,
    ];
    #endregion Property variable

    #region Method
    public function __construct($Children = null, $IconBaseURL = null, $CSSSelector = null, $PadID = null){
        // Set default propery values
        //$this->Property["ContentLanguage"] = new \sPHP\Language();
		$this->Property["ItemID"] = "" . \sPHP\GUID() . "";

		// Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

        return true;
    }
    #endregion Method

    #region Property
    public function Children($Value = null){
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

    public function PadID($Value = null){
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

            foreach($this->Property["Children"] as $Child){
                if(!$Child->PadID())$Child->PadID($this->Property["PadID"]);
                if(!$Child->IconBaseURL())$Child->IconBaseURL($this->Property["IconBaseURL"]);

                $ChildHTML[] = $Child->HTML();
            }

            $this->Property[__FUNCTION__] = "
                <nav class=\"sMenu" . ($this->Property["CSSSelector"] ? " {$this->Property["CSSSelector"]}" : null) . "\">
                    " . implode("", $ChildHTML) . "
                </nav>
            ";
		}

		$Result = $this->Property[__FUNCTION__];

		return $Result;
    }
    #endregion Property
}
?>