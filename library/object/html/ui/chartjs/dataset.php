<?php
/*
    Name:           HTML UI Chart JS Dataset
    Purpose:        User object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 12, 2018 10:51 AM
*/

namespace sPHP\HTML\UI\ChartJS;

class Dataset{
    #region Property variable
    private $Property = [
		"Name"						=>	[],
		"BackgroundColor"			=>	"Blue",
		"BorderColor"				=>	"Cyan",
		"Fill"						=>	false,
		"Label"						=>	null,
        "JavaScript"				=>	null,
		"Data"						=>	[], // To be dynamically passed by the ChartJS object
    ];
    #endregion Property variable

    #region Method
    public function __construct($Name = null, $BackgroundColor = null, $BorderColor = null, $Fill = null, $Label = null){
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

			if(is_null($this->Property["Label"]))$this->Property["Label"] = $this->Property[__FUNCTION__];
			$this->Property["JavaScript"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function BackgroundColor($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["JavaScript"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function BorderColor($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["JavaScript"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function Fill($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["JavaScript"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function Label($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["JavaScript"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function JavaScript(){
		if(is_null($this->Property[__FUNCTION__]))$this->Property[__FUNCTION__] = "{
			label: '{$this->Property["Label"]}',
			backgroundColor: '{$this->Property["BackgroundColor"]}',
			borderColor: '{$this->Property["BorderColor"]}',
			data: [" . implode(", ", $this->Property["Data"]) . "],
			fill: " . ($this->Property["Fill"] ? "true" : "false") . ",
		}";

		$Result = $this->Property[__FUNCTION__];

		return $Result;
    }

    public function Data($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["JavaScript"] = null;

            $Result = true;
        }

        return $Result;
    }
    #endregion Property
}
?>