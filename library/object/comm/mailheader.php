<?php
/*
    Name:           MailHeader
    Purpose:        MailHeader object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  Sat, 28 Jul 2018 20:12:25 GMT+06:00
*/

namespace sPHP\Comm;

class MailHeader{
    #region Property
    private $Property = [
        "Name"				=>	null,
        "Data"				=>	null,
    ];
    #endregion Property

    #region Variable
    //private $Buffer = [];
    #endregion Variable

    #region Method
    public function __construct($Name = null, $Data = null){
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

    public function Data($Value = null){
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

    public function Header(){
        if(is_null($this->Property[__FUNCTION__]))$Result = $this->Property[__FUNCTION__] = "{$this->Property["Name"]}: {$this->Property["Data"]}";

        return $Result;
    }
    #endregion Property
}
?>