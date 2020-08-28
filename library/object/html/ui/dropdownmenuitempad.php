<?php
/*
    Name:           DropdownMenuItemPad
    Purpose:        DropdownMenuItemPad object
    Author:         Broken Arrow (SKJoy2001@GMail.Com)
    Created:  		May, 30, 2018 05:30 PM
    Date modified:  Mon, 30 Jul 2018 17:54:00 GMT+06:00
*/

namespace sPHP\HTML\UI;

class DropdownMenuItemPad{
    #region Property
    private $Property = [
        "Item"						=>	[],
        "HTML"						=>	null,
    ];
    #endregion Property

    #region Variable
    //private $Buffer = [];
    #endregion Variable

    #region Method
    public function __construct($Item = null){
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

    public function HTML(){
		if(is_null($this->Property[__FUNCTION__])){
			$HTML[] = "<div class=\"Pad\">";
			foreach($this->Property["Item"] as $Item)$HTML[] = $Item->HTML();
			$HTML[] = "</div>";
		}

        $Result = $this->Property[__FUNCTION__] = implode(null, $HTML);

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