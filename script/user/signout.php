<?php
namespace sPHP;

$LOG->Put("{$SSN->User()->Name()} ({$SSN->User()->UserGroupIdentifierHighest()})", ["User" => ["Email" => $SSN->User()->Email(), ], ], null, LOG_TYPE_SECURITY, "Sign out", "User", "Application");
$SSN->Reset();
$TRM->Redirect($APP->URL());
?>