<?php
/*
    Name:           Language
    Purpose:        Language object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 15, 2018 10:45 PM
*/

namespace sPHP;

class Language{
    private $Property = [ // Property variable
        "Name"				=>	"English (United States)",
        "Code"				=>	"EN",
        "RegionCode"		=>	"US",
        "NativeName"		=>	"English (United States)",
        "NativelyName"		=>	"English (United States)",
        "HTMLCode"			=>	"en-US",
    ];

    #region Method
    public function __construct($Name = null, $Code = null, $RegionCode = null, $NativeName = null, $NativelyName = null){

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

            $this->NativeName($this->Property[__FUNCTION__]);

            $Result = true;
        }

        return $Result;
    }

    public function Code($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $this->Property["HTMLCode" ] = null;

            $Result = true;
        }

        return $Result;
    }

    public function RegionCode($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $this->Property["HTMLCode" ] = null;

            $Result = true;
        }

        return $Result;
    }

    public function NativeName($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $this->Property["NativelyName" ] = $this->Property[__FUNCTION__];

            $Result = true;
        }

        return $Result;
    }

    public function NativelyName($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function HTMLCode($Value = null){
        if(is_null($Value)){
            if(is_null($this->Property[__FUNCTION__]))$this->Property[__FUNCTION__] = strtolower($this->Property["Code"]) . (!is_null($this->Property["RegionCode"]) ? "-" . strtoupper($this->Property["RegionCode"]) : null);

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