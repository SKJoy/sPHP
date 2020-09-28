<?php
namespace sPHP;
/*
*/
if(
		!is_null($Database->Connection()) // We have a connected database
	&&	!$Session->IsGuest() // Ignore for Guest user
	&&	time() - $Session->UserSetTime() < 2 // Avoid blind repeatative trials
){
	$UserID = intval($User->ID()); // For some reason, $User->ID() returns NULL even with an authenticated user!

	if(!isset($_COOKIE["UserDeviceIdentifier"]) || !$_COOKIE["UserDeviceIdentifier"]){ // No UserDeviceIdentifier
		// Keep trying generating an unique UserDeviceIdentifier
		while($Database->Query("# Keep trying generating an unique UserDeviceIdentifier
			SELECT			COUNT(0) AS UserDeviceCount 
			FROM			sphp_userdevice AS UD 
			WHERE			UD.UserDeviceIdentifier = '" . ($_COOKIE["UserDeviceIdentifier"] = implode("-", [
				md5(date("r")), 
				md5($_SERVER["HTTP_USER_AGENT"]), 
				session_id(), 
				$Utility->RandomString(32, true, true, false, false), 
			])) . "'
		")[0][0]["UserDeviceCount"] > 0){ // Same UserDeviceIdentifier already exisys
			// Do nothing but loopping
			// Or we may decide to do something with too much looping!
		}

		$UserDeviceGeoLocation = $Utility->IP2Geo($_SERVER["REMOTE_ADDR"]);

		// Create new UserDevice and tag with User
		$Database->Query("
			# Create new UserDevice
			INSERT INTO sphp_userdevice (
				UserDeviceIdentifier, 
				UserDeviceUserAgent, 
				UserDeviceIP, 
				UserDeviceCountry, 
				UserDeviceCity, 
				UserDeviceIsActive, 
				UserIDInserted, 
				TimeInserted
			) VALUES (
				'{$_COOKIE["UserDeviceIdentifier"]}', 
				'{$Database->Escape($_SERVER["HTTP_USER_AGENT"])}', 
				'{$_SERVER["REMOTE_ADDR"]}', 
				'" . (isset($UserDeviceGeoLocation->country->names["en"]) ? $UserDeviceGeoLocation->country->names["en"] : null) . "', 
				'" . (isset($UserDeviceGeoLocation->city->names["en"]) ? $UserDeviceGeoLocation->city->names["en"] : null) . "', 
				1, 
				{$UserID}, 
				NOW()
			);
		");

		// Update client (user's device)
		setcookie("UserDeviceIdentifier", $_COOKIE["UserDeviceIdentifier"], time() + (30 * 24 * 60 * 60)); 
	}
	else{ // Suppose to be a known UserDevice
		$Database->Query("
			# Tag UserDevice to the User (SHOULD BE OPTIMIZED RATHER THAN TRY)
			INSERT IGNORE INTO sphp_useruserdevice (
				UserID, 
				UserDeviceID, 
				UserUserDeviceTimeActiveLast, 
				UserUserDeviceIsActive, 
				UserIDInserted, 
				TimeInserted
			) VALUES (
				{$UserID}, # UserID
				(
					SELECT			UD.UserDeviceID 
					FROM			sphp_userdevice AS UD 
					WHERE			UD.UserDeviceIdentifier = '{$_COOKIE["UserDeviceIdentifier"]}'
				), # UserDeviceID
				NOW(), # UserUserDeviceTimeActiveLast
				1, # UserUserDeviceIsActive
				{$UserID}, # UserIDInserted
				NOW() # TimeInserted
			);
			
			# Update UserDevice last activity
			UPDATE			sphp_useruserdevice AS UUD 
				SET			UUD.UserUserDeviceTimeActiveLast = NOW()
			WHERE			UUD.UserID = {$UserID} 
				AND			UUD.UserDeviceID = (
								SELECT			UD.UserDeviceID 
								FROM			sphp_userdevice AS UD 
								WHERE			UD.UserDeviceIdentifier = '{$_COOKIE["UserDeviceIdentifier"]}'
							)
		");
	}
}
else{ // Do we really need to do anything for Guest user?
	// Anything at all?
}
?>