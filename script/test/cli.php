<?php
namespace sPHP;

print "Testing CLI detection on " . date("r") . "\n";
print "\$_SERVER[\"SERVER_NAME\"] = {$_SERVER["SERVER_NAME"]}\n";
print "\$ENV->CLI() = {$ENV->CLI()}\n";
print "\$ENV->DomainPath() = {$ENV->DomainPath()}\n";
print "\$TRM->DocumentType() = {$TRM->DocumentType()}\n";
print "\$APP->DocumentType() = {$APP->DocumentType()}\n";
print "\$SSN->IsGuest() = {$SSN->IsGuest()}\n";
print "\$SSN->User()->Email() = {$SSN->User()->Email()}\n";
print "\$SSN->User()->Name() = {$SSN->User()->Name()}\n";
?>