<?php
/*
    Name:           HTML UI Chart JS Axes
    Purpose:        User object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 12, 2018 10:51 AM
*/

namespace sPHP\HTML\UI\ChartJS;

class Axes{
    #region Property variable
    private $Property = [
		"Label"						=>	null,
		"FontColor"					=>	"Black",
		"FontSize"					=>	14,
		"FontStyle"					=>	"Bold",
		"Display"					=>	true,
        "ScaleJavaScript"			=>	null,
    ];
    #endregion Property variable

    #region Method
    public function __construct($Label = null, $FontColor = null, $FontSize = null, $FontStyle = null, $Display = null){
        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

        return true;
    }
    #endregion Method

    #region Property
    public function Label($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function FontColor($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function FontSize($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function FontStyle($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Display($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function ScaleJavaScript(){
		$this->Property[__FUNCTION__] = "[{
			display: " . ($this->Property["Display"] ? "true" : "false") . ",
			" . ($this->Property["Label"] ? "scaleLabel: {
				display: true,
				labelString: '{$this->Property["Label"]}',
				fontColor: '{$this->Property["FontColor"]}',
				fontSize: '{$this->Property["FontSize"]}',
				fontStyle: '" . strtolower($this->Property["FontStyle"]) . "',
			}, " : null) . "
		}]";

		$Result = $this->Property[__FUNCTION__];

		return $Result;
    }
    #endregion Property
}
?>