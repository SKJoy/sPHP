<?php
/*
    Name:           Test
    Purpose:        Test object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 12, 2018 10:51 AM
*/

namespace sPHP;

class Test{
    #region Property variable
    private $Property = [
        "Name"						=>	null,
    ];
    #endregion Property variable

    #region Variable
	private static $AlreadyInstantiated = false;
    #endregion Variable

    #region Method
    public function __construct(){
		// Check if already instantiated and throw error if tried again
		if($this::$AlreadyInstantiated)trigger_error("Instantiation is prohibited for " . __CLASS__ .  " object.");
		$this::$AlreadyInstantiated = true;

        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

        return true;
    }
    #endregion Method

    #region Property
    #endregion Property
}
?>