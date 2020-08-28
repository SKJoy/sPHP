<?php
/*
    Name:           InputValidation
    Purpose:        InputValidation object
    Author:         Broken Arrow (SKJoy2001@GMail.Com)
    Created:  		May, 30, 2018 05:30 PM
    Date modified:  Mon, 30 Jul 2018 17:40:00 GMT+06:00
*/

namespace sPHP\HTTP;

// Object method shortcut function
class InputValidation{
    #region Property
    private $Property = [
        "Name"						=>	null,
        "Required"					=>	false,
        "Type"						=>	[],
		"Caption"					=>	null,
		"Message"					=>	null,
    ];
    #endregion Property

    #region Variable
    private $Utility = null;
    #endregion Variable

    #region Method
    public function __construct($Name = null, $Required = null, $Type = null, $Caption = null, $Message = null){
		$this->Utility = new \sPHP\Utility();

        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){
		return true;
    }

	public function Validate(){
		$Result = true;
		$Value = isset($_POST[$this->Property["Name"]]) ? $_POST[$this->Property["Name"]] : null;
//var_dump($this->Property["Name"], $Value, strlen($Value));
		if($this->Property["Required"] && !strlen($Value)){
			$Result = false;
			if(!$this->Property["Message"])$this->Property["Message"] = "{$this->Property["Caption"]} is required.";
		}

		if($Result)foreach(array_filter($this->Property["Type"]) as $Type){
			$FunctionName = "Is{$Type}";

			if(strlen($Value) && !$this->Utility->$FunctionName($Value)){
				$Result = false;
				if(!$this->Property["Message"])$this->Property["Message"] = "{$this->Property["Caption"]} must be one of types " . implode(", ", $this->Property["Type"]) . ".";
				break; // No need to check more, we already have a type error
			}
		}

		return $Result;
	}

	public function Transform(){
		$Result = false;

		foreach(array_filter($this->Property["Type"]) as $Type){
			$FunctionName = "Is{$Type}";

			if(!$this->Utility->$FunctionName($_POST[$this->Property["Name"]])){
				if($Type == \sPHP\VALIDATION_TYPE_NUMBER || $Type == \sPHP\VALIDATION_TYPE_NUMERIC){
					$_POST[$this->Property["Name"]] = floatval($_POST[$this->Property["Name"]]);
				}
				elseif($Type == \sPHP\VALIDATION_TYPE_INTEGER){
					$_POST[$this->Property["Name"]] = intval($_POST[$this->Property["Name"]]);
				}
				elseif($Type == \sPHP\VALIDATION_TYPE_FLOAT){
					$_POST[$this->Property["Name"]] = floatval($_POST[$this->Property["Name"]]);
				}
				elseif($Type == \sPHP\VALIDATION_TYPE_POSITIVE){
					$_POST[$this->Property["Name"]] = abs(floatval($_POST[$this->Property["Name"]]));
				}
				elseif($Type == \sPHP\VALIDATION_TYPE_NEGATIVE){
					$_POST[$this->Property["Name"]] = floatval($_POST[$this->Property["Name"]]) * (-1);
				}
				elseif($Type == \sPHP\VALIDATION_TYPE_NONPOSITIVE){
					$_POST[$this->Property["Name"]] = floatval($_POST[$this->Property["Name"]]);
					$_POST[$this->Property["Name"]] = $_POST[$this->Property["Name"]] == 0 ? 0 : ($_POST[$this->Property["Name"]] * (-1));
				}
				elseif($Type == \sPHP\VALIDATION_TYPE_NONNEGATIVE){
					$_POST[$this->Property["Name"]] = floatval($_POST[$this->Property["Name"]]);
					$_POST[$this->Property["Name"]] = $_POST[$this->Property["Name"]] == 0 ? 0 : abs($_POST[$this->Property["Name"]]);
				}
				else{
					$_POST[$this->Property["Name"]] = $_POST[$this->Property["Name"]];
				}
			}

			$Result = $_POST[$this->Property["Name"]];
		}

		return $Result;
	}
    #endregion Method

    #region Property
    public function Name($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["Caption"] = $this->Property[__FUNCTION__ ];

            $Result = true;
        }

        return $Result;
    }

    public function Required($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Type($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = is_array($Value) ? $Value : [$Value];

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

            $Result = true;
        }

        return $Result;
    }

    public function Message($Value = null){
		if(is_null($Value)){
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