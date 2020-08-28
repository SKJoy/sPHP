<?php
/*
    Name:           Version
    Purpose:        Version object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 15, 2018 11:17 PM
*/

namespace sPHP;

class Version{
    #region Property
    private $Property = [
        "Major"				=>	1,
        "Minor"				=>	0,
        "Revision"			=>	0,
        "MaximumMinor"		=>	999,
        "MaximumRevision"	=>	99999,
		"File"				=>	null,
		"Full"				=>	null,
    ];
    #endregion Property

    #region Variable
    //private $Buffer = [];
    #endregion Variable

    #region Method
    public function __construct($Major = null, $Minor = null, $Revision = null, $MaximumMinor = null, $MaximumRevision = null, $File = null){
        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){
		if($this->Property["File"])file_put_contents($this->Property["File"], json_encode(([
			"Major"			=>	$this->Property["Major"],
			"Minor"			=>	$this->Property["Minor"],
			"Revision"		=>	$this->Property["Revision"],
		])));

        return true;
    }
    #endregion Method

    #region Property
    public function Major($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $this->Property["Full"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function Minor($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            if($this->Property[__FUNCTION__] > $this->Property["MaximumMinor"]){
                $this->Property["Major"]++;
                $this->Property[__FUNCTION__] = 0;
            }

            $this->Property["Full"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function Revision($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            if($this->Property[__FUNCTION__] > $this->Property["MaximumRevision"]){
                $this->Minor($this->Minor() + 1);
                $this->Property[__FUNCTION__] = 0;
            }

            $this->Property["Full"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function MaximumMinor($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function MaximumRevision($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function File($Value = null, $Increment = true){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			if(file_exists($this->Property[__FUNCTION__])){
				$Data = json_decode(file_get_contents($this->Property[__FUNCTION__]));

				if($Data){
					$this->Property["Major"] = $Data->Major;
					$this->Property["Minor"] = $Data->Minor;
					$this->Revision($Data->Revision + ($Increment ? 1 : 0));
				}
			}

            $Result = true;
        }

        return $Result;
    }

    public function Full(){
        if(is_null($this->Property[__FUNCTION__]))$this->Property[__FUNCTION__] = implode(".", [$this->Property["Major"], $this->Property["Minor"], $this->Property["Revision"]]);

        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }
    #endregion Property
}
?>