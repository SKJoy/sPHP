<?php
/*
    Name:           Engine
    Purpose:        Load framework, assemble resources and execute application
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Created:  		May 30, 2018 05:30 PM
    Modified:  		June 17, 2018 03:36 AM
*/

namespace sPHP;

require __DIR__ . "/errorhandler.php"; // Set custom error handler
require __DIR__ . "/checkenvironment.php"; // Check required environment

#region Internal resource libary
require __DIR__ . "/constant.php"; // Global constants
require __DIR__ . "/constant_mimetypeextension.php"; // MIME type to extension map constants
require __DIR__ . "/function.php"; // Framework dependent namespace independent global user function library
require __DIR__ . "/private_function.php"; // Core private function library
require __DIR__ . "/class.php"; // Core class library
#endregion Internal resource libary

#region 3rd party object
require __DIR__ . "/library/3rdparty/BrowserDetection.php"; // https://github.com/Wolfcast/BrowserDetection
//require __DIR__ . "/library/3rdparty/maxmind/geoip2.php"; // Replaced with $Utility->IP2Geo() method
#endregion 3rd party object

require __DIR__ . "/loadclass.php"; // Load classes dynamically on demand as needed

$Application = new Application( // Create application
    $Terminal = new Terminal($Environment = new Environment(new Utility(new Debug()))), // Create Terminal
    $Session = new Session($Environment) // Create session
);
?>