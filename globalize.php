<?php
/*
	Make sPHP resources/objects/environment available within any scope
	through globalized $sPHP variable, global function or auto global 
	static property of sPHP class.
*/

$sPHP = []; // Global scope variable for globalization

function sPHP(){ // Global function as a replacement for $sPHP global variable
    global $sPHP;

    return $sPHP;
}

class sPHP{ // Class abstraction for auto global static properties
	// Values are update in /engine.php
	public static $Application; 
	public static $Configuration; 
	public static $Environment; 
	public static $Terminal; 
	public static $Session; 
	public static $User; 
	public static $Utility; 
	public static $Debug; 
	public static $Template; 
	public static $Database; 
	public static $Table; 
	public static $Variable; 
	public static $Log; 

	public static $APP; 
	public static $CFG; 
	public static $ENV; 
	public static $TRM; 
	public static $SSN; 
	public static $USR; 
	public static $UTL; 
	public static $DBG; 
	public static $TPL; 
	public static $DTB; 
	public static $TBL; 
	public static $VAR; 
	public static $LOG; 
}
?>