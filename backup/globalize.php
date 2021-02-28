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
	public static $Terminal; 
	public static $Environment; 
	public static $Utility; 
	public static $Debug; 
	public static $Session; 
	public static $User; 
	public static $Database; 
}
?>