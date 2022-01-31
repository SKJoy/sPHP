<?php
/*
    Name:           DropdownMenuItem
    Purpose:        DropdownMenuItem object
    Author:         Broken Arrow (SKJoy2001@GMail.Com)
    Created:  		May, 30, 2018 05:30 PM
    Date modified:  Mon, 30 Jul 2018 17:54:00 GMT+06:00
*/

namespace sPHP\HTML\UI;

class DropdownMenuItem{
    #region Property
    private $Property = [
        "Caption"					=>	null,
        "URL"						=>	null,
		"Pad"						=>	null,
        "Icon"						=>	null,
        "HTML"						=>	null,
    ];
    #endregion Property

    #region Variable
    //private $Buffer = [];
    #endregion Variable

    #region Method
    public function __construct($Caption = null, $URL = null, $Pad = null, $Icon = null){
        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){
		$Result = true;

		return $Result;
    }

	public function _Method($Argument = null){
		$Result = true;

		return $Result;
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

    public function HTML(){
		if(is_null($this->Property[__FUNCTION__])){
			$HTML = [];
			if(!is_null($this->Property["Caption"]))$HTML[] = "<div class=\"Caption\">" . ($this->Property["Icon"] ? "<img src=\"<?=$Environment->IconURL()?>{$this->Property["Icon"]}.png\" alt=\"Item\">" : null) . ($this->Property["URL"] ? "<a href=\"{$this->Property["URL"]}\">{$this->Property["Caption"]}</a>" : $this->Property["Caption"]) . "</div>";
			if(!is_null($this->Property["Pad"]))$HTML[] = $this->Property["Pad"]->HTML();

			$this->Property[__FUNCTION__] = "<div class=\"Item\">" . implode("", $HTML) . "</div>";
		}

        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }
    #endregion Property

	#region Function
	private function _PrivateFunction(){
		$Result = true;

		return $Result;
	}
	#endregion Function
}
?>