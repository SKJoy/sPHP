<?php
if(!$Session->IsGuest() && intval($User->ID())){ // Authenticated User
	print "
		<script>
			var UserDeviceIdentifier = '" . (isset($_COOKIE["UserDeviceIdentifier"]) ? $_COOKIE["UserDeviceIdentifier"] : null) . "';
		</script>

		<script src='{$Environment->URL()}javascript/useruserdevicenotification_fetch.js'></script>
	";
}
else{ // Guest User
	// We decide not to fetch UserDevice notification for Guest
}
?>