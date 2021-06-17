<?php
namespace sPHP;

if(isset($sPHPCronJob)){ // In case if this script runs through sPHP Cron object
	$Table = $Resource["Table"];
	$Utility = $Resource["Utility"];
	$Configuration = $Resource["Configuration"];

	$Database = $Table[array_keys($Table)[0]]->Database();
}

$Entity = "Notification";
$CheckpointTime = microtime(true);
$NotificationMobileSMSCount = 0;
$NotificationEmailCount = 0;
$NotificationUPDATESQL = [];

$cURL = curl_init(); // Initialize cURL for repeatative use by single connection
curl_setopt($cURL, CURLOPT_RETURNTRANSFER, TRUE); // Return the response as a string from curl_exec(), don't output it

foreach($Table["{$Entity}"]->Get("
	" . ($Configuration["SendNotification"] ? "TRUE" : "FALSE") . " # Configuration: SendNotification
	AND	N.{$Entity}SentTime IS NULL
	AND	N.TimeInserted > DATE_ADD(NOW(), INTERVAL -1 DAY)
	AND	NT.{$Entity}TypeIdentifier IN ('" . NOTIFICATION_TYPE_MOBILE_SMS . "', '" . NOTIFICATION_TYPE_EMAIL . "')
	AND	N.{$Entity}Attempt < 3
	AND	N.{$Entity}IsActive = 1
", "N.TimeInserted DESC", 1, 120) as $Notification){
	$NotificationAttemptTime = microtime(true);
	$NotificationUPDATESQL[] = "UPDATE sphp_notification SET {$Entity}AttemptTime = '" . date("Y-m-d H:i:s", $NotificationAttemptTime) . "', {$Entity}Attempt = " . ($Notification["{$Entity}Attempt"] + 1) . " WHERE {$Entity}ID = {$Notification["{$Entity}ID"]};";
	$NotificationRecepient = trim($Notification["{$Entity}To"]);
	$NotificationSendOutResult = false; // Assume notification send out failed

	#region Forward message to SMS server
	if($Notification["{$Entity}TypeIdentifier"] == NOTIFICATION_TYPE_MOBILE_SMS && $NotificationRecepient){
		curl_setopt($cURL, CURLOPT_URL, $URL = $Utility->ReplaceByKey($Configuration["SMSHTTPAPIURL"], [
			"PhoneNumber" => urlencode($NotificationRecepient),
			"Message" => urlencode($Notification["{$Entity}Message"]),
			"Source" => urlencode($Configuration["ShortName"]),
			"Purpose" => urlencode("Alert"),
		]));

		$cURLResponse = curl_exec($cURL);
		$cURLHTTPStatusCode = curl_getinfo($cURL, CURLINFO_HTTP_CODE);

		if($cURLHTTPStatusCode == 200){ // SMS send out succeded
			$NotificationSendOutResult = true;
			$NotificationMobileSMSCount++;
		}
	}
	#endregion Forward message to SMS server

	#region Send out email
	if($Notification["{$Entity}TypeIdentifier"] == NOTIFICATION_TYPE_EMAIL && $NotificationRecepient){
		$NotificationSendOutResult = Comm\Mail(
			new Comm\MailContact($NotificationRecepient),
			$Notification["{$Entity}Subject"],
			"\n\n<style>{$Configuration["EmailCSS"]}</style>\n\n{$Configuration["EmailHeader"]}{$Notification["{$Entity}Message"]}{$Configuration["EmailFooter"]}",
			$Notification["{$Entity}From"] ? new Comm\MailContact($Notification["{$Entity}From"], null) : new Comm\MailContact($Configuration["NoReplyEmail"], $Configuration["CompanyName"]),
			$Configuration["SMTPBodyStyle"], 
			null, // LogPath
			null, // Cc
			null, // Bcc
			null, // Attachment
			null, //new Comm\MailContact("NoReply@BondStein.Com", "Bondstein"), // Reply to
			null, // HTML
			null, // Header
			$Configuration["SMTPHost"], 
			$Configuration["SMTPPort"], 
			$Configuration["SMTPUser"], 
			$Configuration["SMTPPassword"] 
		);

		if($NotificationSendOutResult)$NotificationEmailCount++;
	}
	#endregion Send out email

	$NotificationUPDATESQL[] = "UPDATE sphp_notification SET {$Entity}SentTime = '" . date("Y-m-d H:i:s", $NotificationAttemptTime) . "' WHERE {$Entity}ID = {$Notification["{$Entity}ID"]};";
}

curl_close($cURL); // Close cURL connection
if(count($NotificationUPDATESQL))$Database->Query(implode("\n", $NotificationUPDATESQL)); // Update notification status

if(isset($sPHPCronJob)){ // Resturn result if ran through sPHP Cron object
	$CronJobResult["Error"] = ["Code" => 0, "Message" => null, ];

	$CronJobResult["Status"] = [
		"SMS: {$NotificationMobileSMSCount}",
		"Email: {$NotificationEmailCount}",
	];
}
else{ // Output only if not an sPHP Cron Job (service, background process)
	print "
		<h2>Notification: Send</h2>

		<ul>
			<li>Send notification: " . ($Configuration["SendNotification"] ? "Yes" : "No") . "</li>
			<li>SMS: {$NotificationMobileSMSCount}</li>
			<li>Email: {$NotificationEmailCount}</li>
			<li>Begin: " . date("H:i:s", $CheckpointTime) . "</li>
			<li>End: " . date("H:i:s") . " (" . number_format(microtime(true) - $CheckpointTime, 3) . ")</li>
		</ul>
	";
}
?>