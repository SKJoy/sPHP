<?php
/*
    Name:           CheckEnvironment
    Purpose:        Check for required environment compatibility
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Created:  		May 30, 2018 05:30 PM
    Modified:  		June 7, 2018 02:55 AM
*/

//$Message = ["Unknown!"]; // Dummy error

// Check for minimum PHP version
$RequiredPHPVersionMajor = 5;
$RequiredPHPVersionMinor = 3;
$PHPVersion = explode(".", phpversion());
if($PHPVersion[0] < $RequiredPHPVersionMajor || ($PHPVersion[0] == $RequiredPHPVersionMajor && $PHPVersion[1] < $RequiredPHPVersionMinor))$Message[] = "Minimum of PHP {$RequiredPHPVersionMajor}.{$RequiredPHPVersionMinor} is required. Your version is PHP {$PHPVersion[0]}.{$PHPVersion[1]}!";
//var_dump(get_loaded_extensions()); exit;
// Check for required extension: PDO_ODBC cURL
foreach(array_filter(explode(",", str_replace(" ", "", "GD, OpenSSL"))) as $Extension)if(!extension_loaded($Extension))$Message[] = "'{$Extension}' PHP extension is required.";

// Show requirement error message
if(isset($Message))die("<html><body><p style=\"color: Red;\">Please check for the following error!</p><ul><li>" . implode("</li><li>", $Message) . "</li></ul></body></html>");
?>