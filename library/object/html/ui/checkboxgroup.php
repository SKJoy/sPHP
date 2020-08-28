<?php
/*
    Name:           HTML\UI\CheckboxGroup
    Purpose:        Object to create group of check boxes
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 12, 2018 10:51 AM
*/

namespace sPHP\HTML\UI;

class CheckboxGroup{
    #region Property variable
    private $Property = [
        "Name"						=>	null,
        "Option"					=>	[],
        "PrependOption"				=>	[],
		"CaptionField"				=>	null,
        "AppendOption"				=>	[],
		"ValueField"				=>	null,
        "CSSSelector"				=>	"CheckboxGroup",
		"EventHandlerJavaScript"	=>	[],
        "Array"                     =>  false,
        "ID"                    	=>  null,
        "HTML"						=>	null,
    ];
    #endregion Property variable

    #region Method
    public function __construct($Name = null, $Option = null, $PrependOption = null, $CaptionField = null, $AppendOption = null, $ValueField = null, $CSSSelector = null, $EventHandlerJavaScript = null, $Array = null, $ID = null){
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

    public function Array($Value = null){
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
			if(is_array($this->Property["EventHandlerJavaScript"]))foreach(array_filter($this->Property["EventHandlerJavaScript"]) as $Handler)if(is_array($Handler) && isset($Handler[1]))$EventHandler[] = "{$Handler[0]}=\"{$Handler[1]}\"";

			if(isset($this->Property["Option"][0]) && is_array($this->Property["Option"][0])){
				$ValueField = $this->Property["ValueField"] ? $this->Property["ValueField"] : array_keys($this->Property["Option"][0])[0];
				foreach($this->Property["Option"] as $Data)$Option[] = new \sPHP\Option($Data[$ValueField], $Data[$this->Property["CaptionField"] ? $this->Property["CaptionField"] : null]);
	            $this->Property["Option"] = $Option;
			}

			if(!is_array($this->Property["Option"]) || !count($this->Property["Option"])){
				$Checkbox = new Checkbox(null, "No option!");
				$OptionHTML[] = $Checkbox->HTML();
			}

			$CurrentValue = \sPHP\SetVariable($this->Property["Name"]);

			foreach(array_merge(is_array($this->Property["PrependOption"]) ? $this->Property["PrependOption"] : [$this->Property["PrependOption"]], is_array($this->Property["Option"]) ? $this->Property["Option"] : [], is_array($this->Property["AppendOption"]) ? $this->Property["AppendOption"] : [$this->Property["AppendOption"]]) as $Option){
				$Checkbox = new Checkbox(
                    $Option->Value(),
                    $Option->Caption() ? $Option->Caption() : $Option->Value(),
                    "{$this->Property["Name"]}" . ($this->Property["Array"] ? "[{$Option->Value()}]" : "_{$Option->Value()}") . "",
                    null,
                    isset($EventHandler) ? $EventHandler : null,
					null // ID
                );

				$OptionHTML[] = $Checkbox->HTML();
			}

			$this->Property[__FUNCTION__] = "<div id=\"{$this->Property["ID"]}\" class=\"{$this->Property["CSSSelector"]}\">" . implode(null, $OptionHTML) . "</div>";

			/*
			foreach($this->Property["Option"] as $Option){
				if(is_null($Option->Name()))$Option->Name("{$this->Property["Name"]}_{$Option->Value()}");
				$HTML[] = $Option->HTML();
			}

			$this->Property[__FUNCTION__] = "<div class=\"CheckboxGroup" . ($this->Property["CSSSelector"] ? " {$this->Property["CSSSelector"]}" : null) . "\">" . implode(null, $HTML) . "</div>";
			*/
		}

		$Result = $this->Property[__FUNCTION__];

		return $Result;
    }
    #endregion Property
}
?>