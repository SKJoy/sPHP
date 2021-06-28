<?php
/*
    Name:           HTML UI Datagrid\Action
    Purpose:        Datagrid Action object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 12, 2018 10:51 AM
*/

namespace sPHP\HTML\UI\Datagrid;

class Action{
    private $Property = [
		"Icon"						=>	null,
        "Caption"					=>	null,
        "URL"						=>	null,
        "Target"					=>	null,
        "JavaScript"				=>	null,
        "Name"						=>	null,
        "Tooltip"					=>	null,
        "SelfTarget"				=>	true,
        "ParameterKey"				=>	null, // Used by Datagrid object to deterine which Field to pass in the URL, default is ID column
        "EnablerKey"				=>	null, // Used by Datagrid object to deterine which Field to cosider to show this action
		"HTML"						=>	null,
    ];

    #region Method
    public function __construct($Icon = null, $Caption = null, $URL = null, $Target = null, $JavaScript = null, $Name = null, $Tooltip = null, $SelfTarget = null, $ParameterKey = null, $EnablerKey = null){
        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

        return true;
    }
    #endregion Method

    #region Property
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

    public function Target($Value = null){
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

    public function JavaScript($Value = null){
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

    public function Tooltip($Value = null){
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

    public function SelfTarget($Value = null){
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

    public function ParameterKey($Value = null){
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

    public function EnablerKey($Value = null){
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
			$JavaScriptHTML = $this->Property["JavaScript"] ? " onclick=\"{$this->Property["JavaScript"]}\"" : null;
			$IconHTML = $this->Property["Icon"] ? "<img src=\"{$this->Property["Icon"]}\" alt=\"{$this->Property["Caption"]}\" class=\"Icon\">" : null;
			$TooltipHTML = $this->Property["Tooltip"] ? " title=\"{$this->Property["Tooltip"]}\"" : null;
			$TargetHTML = $this->Property["Target"] ? " target=\"{$this->Property["Target"]}\"" : null;

            $this->Property[__FUNCTION__] = $this->Property["Name"] ? "<button name=\"{$this->Property["Name"]}\"{$TooltipHTML}{$JavaScriptHTML}>{$IconHTML}{$this->Property["Caption"]}</button>" : "<a href=\"{$this->Property["URL"]}\"{$TargetHTML}{$TooltipHTML}{$JavaScriptHTML}>{$this->Property["Caption"]}{$IconHTML}</a>";
        }

		$Result = $this->Property[__FUNCTION__];

        return $Result;
    }
    #endregion Property
}
?>