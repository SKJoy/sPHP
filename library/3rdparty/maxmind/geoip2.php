<?php
	// This script is no longer required. It is replaced by the $Utility->IP2Geo() method

	require __DIR__ . "/geoip2.phar"; // https://github.com/maxmind/GeoIP2-php/releases
	use GeoIp2\Database\Reader;
	$MaxMindGeoIP2Reader = new Reader(__DIR__ . "/GeoLite2-City.mmdb"); // http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz
//var_dump($MaxMindGeoIP2Reader);

	function MaxMindGeoIP2DataDownload($URL = null){
		if(!$URL)$URL = "http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz";

		if(copy($URL, __DIR__ . "/GeoLite2-City.mmdb.gz.tmp")){
			@unlink(__DIR__ . "/GeoLite2-City.mmdb.gz");
			rename(__DIR__ . "/GeoLite2-City.mmdb.gz.tmp", __DIR__ . "/GeoLite2-City.mmdb.gz");

			if(@rename(__DIR__ . "/GeoLite2-City.mmdb", __DIR__ . "/GeoLite2-City.mmdb.tmp")){
				if(ExtractArchive(__DIR__ . "/GeoLite2-City.mmdb.gz", null, ARCHIVE_TYPE_GZIP)){
					@unlink(__DIR__ . "/GeoLite2-City.mmdb.tmp");
					$OperationResult = true;
				}
				else{
					rename(__DIR__ . "/GeoLite2-City.mmdb.tmp", __DIR__ . "/GeoLite2-City.mmdb");
					$OperationResult = false;
					var_dump(__FILE__ . ":" . __LINE__ . "- GeoIP2Data archive extraction failed!");
				}
			}
			else{
				$OperationResult = false;
				var_dump(__FILE__ . ":" . __LINE__ . "- GeoIP2Data file rename failed!");
			}

			@unlink(__DIR__ . "/GeoLite2-City.mmdb.gz");
		}
		else{
			$OperationResult = false;
			var_dump(__FILE__ . ":" . __LINE__ . "- GeoIP2Data download failed!");
		}

		return $OperationResult;
	}

	function MaxMindGeoIP2Data($IP){
		global $MaxMindGeoIP2Reader;
//sPHP\DebugDump(__DIR__ . "/geoip2.phar");
//sPHP\DebugDump(__DIR__ . "/GeoLite2-City.mmdb");
//sPHP\DebugDump($MaxMindGeoIP2Reader);
		$Data = false;

		try{
			$Data = $MaxMindGeoIP2Reader->city($IP);
//var_dump($Data);
		}
		catch(Exception $e){

		}

		return $Data;
	}

	#region Sample usage
	/*
	if(($GeoIP2Data = GeoIP2Data("66.249.64.102", $MaxMindGeoIP2Reader)) !== false){ // $_SERVER["REMOTE_ADDR"]
		//var_dump($GeoIP2Data);

		var_dump($GeoIP2Data->traits);
		var_dump($GeoIP2Data->location);
		var_dump($GeoIP2Data->city);
		var_dump($GeoIP2Data->postal);
		var_dump($GeoIP2Data->country);
		var_dump($GeoIP2Data->registeredCountry);
		var_dump($GeoIP2Data->representedCountry);
		var_dump($GeoIP2Data->continent);
		var_dump($GeoIP2Data->locales);
		var_dump($GeoIP2Data->maxmind);
		var_dump($GeoIP2Data->raw);
	}
	*/
	#endregion Sample usage
?>