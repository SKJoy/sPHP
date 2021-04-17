<?php
/*
    Name:           HTML\UI\Select
    Purpose:        HTML Select entity object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 12, 2018 10:51 AM
*/

namespace sPHP\HTML\UI;

class Select{
    private $Property = [
        "Name"						=>	null,
        "Option"					=>	[],
        "PrependOption"				=>	[],
		"CaptionField"				=>	null,
        "AppendOption"				=>	[],
		"ValueField"				=>	null,
        "DefaultValue"				=>	null,
        "CSSSelector"				=>	null,
		"EventHandlerJavaScript"	=>	[],
        "ID"						=>	null,
        "HTML"						=>	null,
    ];

    #region Method
    public function __construct($Name = null, $Option = null, $PrependOption = null, $CaptionField = null, $AppendOption = null, $ValueField = null, $DefaultValue = null, $CSSSelector = null, $EventHandlerJavaScript = null, $ID = null){
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

			if(!$this->Property["ID"])$this->Property["ID"] = "{$this->Property[__FUNCTION__]}_" . \sPHP\GUID() . "";
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

    public function PrependOption($Value = null){
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

    public function CaptionField($Value = null){
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

    public function AppendOption($Value = null){
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

    public function ValueField($Value = null){
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

    public function EventHandlerJavaScript($Value = null){
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

    public function ID($Value = null){
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
			//if(is_array($this->Property["EventHandlerJavaScript"]))foreach(array_filter($this->Property["EventHandlerJavaScript"]) as $Handler)if(is_array($Handler) && isset($Handler[1]))$EventHandler[] = "{$Handler[0]}=\"{$Handler[1]}\"";
			if(is_array($this->Property["EventHandlerJavaScript"]))foreach(array_filter($this->Property["EventHandlerJavaScript"]) as $HandlerKey=>$Handler)if($Handler)$EventHandler[] = "{$HandlerKey}=\"{$Handler}\"";
			if(!is_array($this->Property["Option"]))$this->Property["Option"] = [$this->Property["Option"]];
            //var_dump($this->Property["Option"]); //exit;
			if(isset($this->Property["Option"][0]) && is_array($this->Property["Option"][0]) && count($this->Property["Option"][0])){
				$ValueField = $this->Property["ValueField"] ? $this->Property["ValueField"] : array_keys($this->Property["Option"][0])[0];
				$CaptionField = $this->Property["CaptionField"] ? $this->Property["CaptionField"] : $ValueField;
				foreach($this->Property["Option"] as $Data)$Option[] = new \sPHP\Option($Data[$ValueField], $Data[$CaptionField]);
				$this->Property["Option"] = $Option;
                //var_dump($Option); //exit;
			}

			$OptionSet = array_merge(is_array($this->Property["PrependOption"]) ? $this->Property["PrependOption"] : [$this->Property["PrependOption"]], $this->Property["Option"], is_array($this->Property["AppendOption"]) ? $this->Property["AppendOption"] : [$this->Property["AppendOption"]]);
			if(!count($OptionSet))$OptionHTML[] = "<option>No option!</option>";
			$CurrentValue = \sPHP\SetVariable($this->Property["Name"], $this->Property["DefaultValue"]); //\sPHP\DebugDump(["Name" => $this->Property["Name"], "DefaultValue" => $this->Property["DefaultValue"], "CurrentValue" => $CurrentValue, ]);
            //var_dump($OptionSet); //exit;

            #region DEBUG
            if(is_array($CurrentValue)){
                file_put_contents(__DIR__ . "/debug_select_CurrentValueIsNotString.json", json_encode([
                    "CurrentValue" => $CurrentValue, 
                    "BackTrace" => debug_backtrace(), 
                ]));
            }
            #endregion DEBUG
				
			foreach($OptionSet as $Option)$OptionHTML[] = "<option value=\"{$Option->Value()}\"" . (strlen($CurrentValue) == strlen($Option->Value()) && $CurrentValue == $Option->Value() ? " selected" : null) . ">" . ($Option->Caption() ? $Option->Caption() : $Option->Value()) . "</option>";
			$this->Property[__FUNCTION__] = "<select id=\"{$this->Property["ID"]}\" name=\"{$this->Property["Name"]}\" class=\"{$this->Property["CSSSelector"]}\"" . (isset($EventHandler) ? " " . implode(" ", $EventHandler) . "" : null) . ">" . implode(null, $OptionHTML) . "</select>";
		}

		$Result = $this->Property[__FUNCTION__];

		return $Result;
    }
    #endregion Property
}
?>