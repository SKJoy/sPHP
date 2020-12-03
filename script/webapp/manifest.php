<?php
namespace sPHP;

$Terminal->DocumentType(DOCUMENT_TYPE_JSON);
//$Terminal->DocumentName("WebAppManifest.JSON");

header("Cache-Control: public");
header("Expires: " . date("r", time() + (24 * 60 * 60)));

$LogoURL = "{$Environment->ImageURL()}" . strtolower($_SERVER["SERVER_NAME"]) . "/logo.png";

print json_encode([
	"background_color" => $Configuration["BackgroundColor"],
	"description" => $Configuration["Description"],
	"icons" => [
		//["src" => $LogoURL, "type" => "image/png", "sizes" => "72x72", ], 
		//["src" => $LogoURL, "type" => "image/png", "sizes" => "96x96", ], 
		//["src" => $LogoURL, "type" => "image/png", "sizes" => "128x128", ], 
		//["src" => $LogoURL, "type" => "image/png", "sizes" => "256x256", ], 
		["src" => $LogoURL, "type" => "image/png", "sizes" => "512x512", ], 
	],
	"lang" => $Application->Language()->HTMLCode(), 
	"name" => $Application->Name(),
	"short_name" => $Configuration["ShortName"],
	"start_url" => $Environment->URL(),
	"theme_color" => $Terminal->ThemeColor(), 
	"display" => "standalone",
	"scope" => "/",
]);
?>