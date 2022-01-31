<?php
/*
    Name:           Class
    Purpose:        Load framework core object definition class library
    Author:         Broken Arrow (SKJoy2001@GMail.Com)
    Created:  		May, 30, 2018 05:30 PM
    Modified:  		Oct, 24 Jul 2021 23:54:00 GMT+06:00
*/

namespace sPHP;

$LibraryPath = __DIR__ . "/library/private/object/";
foreach(array_filter(explode(",", str_replace(" ", "", "Environment, Terminal, Session, Application, Template, Utility, Graphic, Debug"))) as $Object)require $LibraryPath . strtolower($Object) . ".php";
?>