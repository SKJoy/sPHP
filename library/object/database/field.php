<?php
/*
    Name:           Table
    Purpose:        Database table object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 12, 2018 10:51 AM
*/

namespace sPHP\Database;

class Field{
    #region Property variable
    private $Property = [
		"Column"				=>	null, // Column of the target table
        "Field"					=>	null, // Field in CSV
        "Value"					=>	null, // Used when CSV doesn't contain the Field
        "RelationTable"			=>	null,
        "RelationColumn"		=>	null,
		"RelationSQL"			=>	null,
    ];
    #endregion Property variable

	#region Variable
	//private $Structure = null;
	#endregion Variable

    #region Method
    public function __construct($Column = null, $Field = null, $Value = null, $RelationTable = null, $RelationColumn = null){
        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

        return true;
    }
    #endregion Method

    #region Property
    public function Column($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Field($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$Result = true;
        }

        return $Result;
    }

    public function Value($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["RelationSQL"] = null;

			$Result = true;
        }

        return $Result;
    }

    public function RelationTable($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			if(!$this->Property["RelationColumn"])$this->Property["RelationColumn"] = $this->Property["Field"];

            $Result = true;
        }

        return $Result;
    }

    public function RelationColumn($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function RelationSQL(){
        if(is_null($this->Property[__FUNCTION__])){
			$Value = $this->Property["Value"];
			if(in_array($this->Property["RelationColumn"], $this->Property["RelationTable"]->Structure()["String"]))$Value = "'{$Value}'";
            $this->Property[__FUNCTION__] = "SELECT {$this->Property["RelationTable"]->Alias()}.{$this->Property["RelationTable"]->Structure()["Primary"][0]} FROM {$this->Property["RelationTable"]->Prefix()}{$this->Property["RelationTable"]->Name()} AS {$this->Property["RelationTable"]->Alias()} WHERE {$this->Property["RelationTable"]->Alias()}.{$this->Property["RelationColumn"]} = {$Value} LIMIT 0, 1";
        }

        return $this->Property[__FUNCTION__];
    }
    #endregion Property

	#region Private function
	private function WriteSQLFile($SQL = null){
		return file_put_contents("{$this->Property["SQLSELECTPath"]}{$this->Property["Name"]}.sql", $SQL ? $SQL : $this->Property["SELECTStatement"]);
	}
	#endregion Private function
}
?>