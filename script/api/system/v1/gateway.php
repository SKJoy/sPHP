<?php
namespace sPHP;

require __DIR__ . "/pre.php";
require __DIR__ . "/module/" . strtolower(SetVariable("_Module", "ping")) . ".php";
require __DIR__ . "/post.php";
?>