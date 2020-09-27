<script>
	var UserDeviceIdentifier = '<?=isset($_COOKIE["UserDeviceIdentifier"]) ? $_COOKIE["UserDeviceIdentifier"] : null?>';
	var FetchUserUserDeviceNotification = <?=intval($User->ID()) ? "true" : "false"?>; // Disable notification fetch for Guest user
</script>

<script src="<?=$Environment->URL()?>javascript/useruserdevicenotification_fetch.js"></script>