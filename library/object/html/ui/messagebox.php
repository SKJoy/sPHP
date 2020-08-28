<?php
/*
    Name:           MessageBox
    Purpose:        MessageBox object
    Author:         Broken Arrow (SKJoy2001@GMail.Com)
    Created:  		May, 30, 2018 05:30 PM
    Date modified:  Mon, 30 Jul 2018 17:54:00 GMT+06:00
*/

namespace sPHP\HTML\UI;

class MessageBox{
    #region Property
    private $Property = [
        "Content"					=>	null,
        "Title"						=>	"System",
		"CSSSelector"				=>	null,
        "HTML"						=>	null,
    ];
    #endregion Property

    #region Variable
    //private $Buffer = [];
    #endregion Variable

    #region Method
    public function __construct($Content, $Title = null, $CSSSelector = null){
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

    public function Title($Value = null){
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
		if(is_null($this->Property[__FUNCTION__]))$this->Property[__FUNCTION__] = "<div class=\"MessageBox" . ($this->Property["CSSSelector"] ? " {$this->Property["CSSSelector"]}" : null) . "\">
			<div class=\"Container\">
				" . ($this->Property["Title"] ? "<div class=\"Title\">{$this->Property["Title"]}</div>" : null) . "
				<div class=\"Content\">{$this->Property["Content"]}</div>
			</div>
		</div>";

        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }
    #endregion Property

	#region Function
	#endregion Function
}
?>