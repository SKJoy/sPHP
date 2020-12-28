<?php
namespace sPHP;

$_COOKIE["StyleTheme"] = $_POST["Theme"]; DebugDump($_COOKIE);
$Message = "Theme changed, please <a href=\"{$Environment->URL()}\">reload page</a> to take effect.";

print "
	" . HTML\UI\MessageBox("{$Message}") . "
	<script>
		//alert('{$Message}');
	</script>
";

//$Terminal->Redirect($_SERVER["HTTP_REFERER"]);
?>