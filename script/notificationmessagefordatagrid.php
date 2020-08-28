<?php
namespace sPHP;
$Notification = $Table["Notification"]->Get("N.NotificationID = {$_POST["NotificationID"]}")[0];
$NotificationMessage = $Notification["NotificationTypeIdentifier"] == "MOBILE_SMS" ? str_replace("\n", "<br>", $Notification["NotificationMessage"]) : $Notification["NotificationMessage"];
?>

<style>
	.Nofication{margin: 15px;}
	.Notification > .Message{}
</style>

<div class="Nofication">
	<div class="Message"><?=$NotificationMessage?></div>
</div>