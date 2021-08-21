<?php
namespace sPHP;

#region Make sure all errors are displayed
error_reporting(E_ALL);

ini_set("display_errors", true);
ini_set("html_errors", true);
ini_set("display_startup_errors", true);
ini_set("log_errors", true); // Enable error logging to error log file
ini_set("report_memleaks", true);
ini_set("track_errors", true);
ini_set("error_prepend_string", "<div style='display: inline-block; box-shadow: 0 0 5px 0 Black; margin: 5px; border-radius: 5px; border: 1px White solid; background-color: Red; padding: 1.2em; padding-top: 0; color: White; font-family: Consolas, Verdana, Tahoma, Arial; font-size: 14px; text-shadow: 1px 1px black;'>");
ini_set("error_append_string", "</div>");
#endregion Make sure all errors are displayed

// Fix missing server variable (executed before Environment is initialized)
if(!isset($_SERVER["HTTP_USER_AGENT"]))$_SERVER["HTTP_USER_AGENT"] = null;

function ___ErrorPage($Message, $File = null, $Line = null, $Number = null, $Type = "User error"){
	//if(ob_get_contents())ob_end_clean(); // Discard all other output			NOTE: Check line below
	ob_end_clean(); // We need to discard and flush no matter content is there or not!

	// PHP doesn't seem to write error log when handled with custom handler! So we write/append the log here
	file_put_contents(ini_get("error_log"), "[" . date("d-M-Y H:i:s e") . "] {$Type}: {$Message} in {$File} on line {$Line}" . PHP_EOL, FILE_APPEND);

	$Joke = explode("\n", file_get_contents(__DIR__ . "/joke.txt")); $CurrentJoke = trim($Joke[rand(0, count($Joke) - 1)]);
	$Learn = explode("\n", file_get_contents(__DIR__ . "/learn.txt")); $CurrentLearn = explode("	", trim($Learn[rand(0, count($Learn) - 1)]));
	$Practice = explode("\n", file_get_contents(__DIR__ . "/practice.txt")); $CurrentPractice = trim($Practice[rand(0, count($Practice) - 1)]);
	$sPHP = explode("\n", file_get_contents(__DIR__ . "/sphp.txt")); $CurrentsPHP = explode("	", trim($sPHP[rand(0, count($sPHP) - 1)]));

	$Calls = debug_backtrace();
	array_shift($Calls); // Remove self
	array_pop($Calls); // sPHP\Application->__destruct()
	if(substr($Calls[$DebugCallIndex = count($Calls) - 1]["file"], strlen($Calls[$DebugCallIndex]["file"]) - strlen($Keyword = "\class.php")) == $Keyword && $Calls[$DebugCallIndex]["function"] == "sPHP\___ExecuteApplicationScript")array_pop($Calls);
	if(substr($Calls[$DebugCallIndex = count($Calls) - 1]["file"], strlen($Calls[$DebugCallIndex]["file"]) - strlen($Keyword = "\private_function.php")) == $Keyword && $Calls[$DebugCallIndex]["function"] == "require")array_pop($Calls);

	$ApplicationPath = str_replace("\\", "/", pathinfo($_SERVER["SCRIPT_FILENAME"])["dirname"]) . "/";
	$SystemPath = str_replace("\\", "/", __DIR__) . "/";
	$CallCount = count($Calls);
	$DebugMode = in_array($_SERVER["SERVER_NAME"], explode(",", str_replace(" ", null, "LocalHost, 127.0.0.1, 192.168.0.1, 192.168.1.1, 192.168.137.1, ::1"))) || in_array($_SERVER["REMOTE_ADDR"], explode(",", str_replace(" ", null, "127.0.0.1, 192.168.0.1, 192.168.1.1, 192.168.137.1, ::1")));
	$FunctionList = get_defined_functions();
	$PHPAdditionalInternalFunction = explode(",", str_replace(" ", null, "require"));
	$CommandPromptPrefix = strpos($_SERVER["HTTP_USER_AGENT"], "Windows") !== false ? "[" : null;
	$CommandPrompt = $CommandPromptPrefix ? "] X:\\&gt;" : ":~#";

	foreach(array_filter(explode("\r\n", "
		Instantiation is prohibited for
		Use of undefined constant
		Undefined variable
		Undefined offset
		Undefined index
		strcasecmp
	")) as $MessagePrefix)if(($MessagePrefix = trim($MessagePrefix))  && substr($Message, 0, strlen($MessagePrefix)) == $MessagePrefix)foreach(explode("\n", file_get_contents(__DIR__ . "/error/reason/" . str_replace(str_split(" :\\/."), "_", strtolower($MessagePrefix)) . ".txt")) as $Reason)$ResonHTML[] = "<li>{$Reason}</li>";

	foreach($Calls as $Key => $Call){
		$Serial = $Key + 1;
		$File = isset($Call["file"]) ? str_replace("\\", "/", $Call["file"]) : null;
		$IsSystemFile = $SystemPath == substr($File, 0, strlen($SystemPath)) ? true : false;

		if(
				$File // Don't log if no file is associated
			&&	$Call["function"] != "trigger_error"
		){
			if($ApplicationPath == substr($File, 0, strlen($ApplicationPath))){
				$File = "<span title=\"Application path (WebRoot)\" class=\"Path\">☸</span>" . substr($Call["file"], strlen($ApplicationPath) - 1) . "";
			}
			elseif($IsSystemFile){
				$File = "<span title=\"Framework path (sPHP)\" class=\"SystemPath\">⚙</span>" . substr($Call["file"], strlen($SystemPath) - 1) . "";
			}

			if($Call["function"] == "{closure}"){
				$Function = "<span class=\"Closure\">{$Call["function"]}</span>";
			}
			elseif($Call["function"] == "__destruct"){
				$Function = "<span title=\"PHP class magic method\" class=\"PHPMagicMethod\">{$Call["function"]}</span>";
			}
			elseif(in_array($Call["function"], array_merge($FunctionList["internal"], $PHPAdditionalInternalFunction))){
				$Function = "<a href=\"http://php.net/manual/en/function.{$Call["function"]}.php\" target=\"_blank\" title=\"PHP internal function\" class=\"PHPFunction\">{$Call["function"]}</a>";
			}
			elseif(substr($Call["function"], 0, 5) == "sPHP\\" && in_array(strtolower($Call["function"]), $FunctionList["user"])){
				$Function = "<a href=\"#\" target=\"_blank\" title=\"Framework function\" class=\"FrameworkFunction\">{$Call["function"]}</a>";
			}
			else{
				$Function = $Call["function"];
			}

			$ArgumentHTML = [];

			if(isset($Call["args"]))foreach($Call["args"] as $Argument){
				$ArgumentType = gettype($Argument);

				if($ArgumentType == "boolean"){
					$ArgumentHTML[] = "<li class=\"Boolean\">" . ($Argument ? "TRUE" : "FALSE") . "</li>";
				}
				elseif($ArgumentType == "array"){
					$ArgumentHTML[] = "<li class=\"Array\">Array(" . count($Argument) . ")</li>";
				}
				elseif($ArgumentType == "object"){
					$ArgumentHTML[] = "<li class=\"Object\">" . get_class($Argument) . " object</li>";
				}
				elseif(is_null($Argument)){
					$ArgumentHTML[] = "<li class=\"Null\">NULL</li>";
				}
				else{
					$ArgumentHTML[] = "<li>{$Argument}</li>";
				}
			}

			$CallRow[] = "
				<tr>
					<td class=\"Serial\">" . ($CallCount - $Serial + 1) . "</td>
					<td>{$File}</td>
					<td class=\"Line\">" . (isset($Call["line"]) ? $Call["line"] : null) . "</td>
					<td>" . (isset($Call["class"]) ? $Call["class"] : null) . "</td>
					<td class=\"Type\">" . (isset($Call["type"]) ? $Call["type"] : null) . "</td>
					<td>{$Function}</td>
					<td>" . (isset($Call["args"]) ? "<ul>" . implode(null, $ArgumentHTML) . "</ul>" : null) . "</td>
				</tr>
			";
		}
	}

	exit("<!DOCTYPE html>
		<html lang=\"en-US\">
			<head>
				<meta charset=\"utf-8\">
				<title>sPHP Error!</title>

				<style>
					html{background-color: Black; color: White; font-family: Monospace, Consolas, Verdana, Tahoma, Arial, Sans; font-size: 18px; line-height: 1.62;}
					body{margin: 15px;}
					div, span, img, a, table, thead, tbody, th, td{box-sizing: border-box; vertical-align: baseline;}
					span, a{display: inline-block;}

					@keyframes Blink{50% {opacity: 0;}}
					.Blink{animation: Blink 0.75s linear infinite;}

					.ErrorMessage > .Title{margin-bottom: 15px;}
					.ErrorMessage > .Title > .Icon{height: 22px; box-shadow: 0 0 5px 0 White; border-radius: 50%; border: 1px White solid; background-color: White; vertical-align: middle;}
					.ErrorMessage > .Title > .Number{color: Pink;}
					.ErrorMessage > .Title > .Message{margin-right: 60px; color: Cyan; text-decoration: none;}
					.ErrorMessage > .Title > .Message:hover{color: Lime;}
					.ErrorMessage > .Title > .Script{color: Pink;}
					.ErrorMessage > .Title > .Line{color: Pink;}
					.ErrorMessage > .Title > .Action{color: Yellow; cursor: pointer;}
					.ErrorMessage > .Title > .Action > .Icon{height: 22px; box-shadow: 0 0 5px 0 White; border-radius: 50%; border: 1px White solid; background-color: White; vertical-align: middle;}
					.ErrorMessage > .Title > .Action:hover{color: Lime;}
					.ErrorMessage > .Content > .Backtrace{box-shadow: 0 0 10px 0 white; border-collapse: collapse;}
					.ErrorMessage > .Content > .Backtrace > thead{background-color: Maroon; text-shadow: 1px 1px Black;}
					.ErrorMessage > .Content > .Backtrace > thead > .Trace{background-color: Orange; text-align: center;}
					.ErrorMessage > .Content > .Backtrace > thead > tr > th{border: 1px White solid; padding: 5px;}
					.ErrorMessage > .Content > .Backtrace > thead > .Trace > th > .Time{color: White; font-size: 75%; font-weight: normal;}
					.ErrorMessage > .Content > .Backtrace > tbody{background-color: Black;}
					.ErrorMessage > .Content > .Backtrace > tbody > tr:hover{background-color: #222222;}
					.ErrorMessage > .Content > .Backtrace > tbody > tr > td{border: 1px White solid; padding: 5px;}
					.ErrorMessage > .Content > .Backtrace > tbody > tr:first-child > td:first-child:before{margin-right: 5px; color: Red; content: '✋';} /* ✊⚠☹■☒⌧⛔➧➤➠➜⇨→ */
					.ErrorMessage > .Content > .Backtrace > tbody > tr > .Serial{text-align: right; white-space: nowrap;}
					.ErrorMessage > .Content > .Backtrace > tbody > tr > .Line{text-align: center;}
					.ErrorMessage > .Content > .Backtrace > tbody > tr > .Type{text-align: center;}
					.ErrorMessage > .Content > .Backtrace > tbody > tr > td > a{text-decoration: none;}
					.ErrorMessage > .Content > .Backtrace > tbody > tr > td > .SystemPath{color: Red; cursor: default;}
					.ErrorMessage > .Content > .Backtrace > tbody > tr > td > .Path{color: Lime; cursor: default;}
					.ErrorMessage > .Content > .Backtrace > tbody > tr > td > .Closure{color: Pink;}
					.ErrorMessage > .Content > .Backtrace > tbody > tr > td > .PHPMagicMethod{color: Yellow;}
					.ErrorMessage > .Content > .Backtrace > tbody > tr > td > .PHPFunction{color: Lime;}
					.ErrorMessage > .Content > .Backtrace > tbody > tr > td > .FrameworkFunction{color: Cyan;}
					.ErrorMessage > .Content > .Backtrace > tbody > tr > td > ul{margin: 0; padding: 0; padding-left: 34px; list-style-type: decimal;}
					.ErrorMessage > .Content > .Backtrace > tbody > tr > td > ul > .Boolean{color: Yellow;}
					.ErrorMessage > .Content > .Backtrace > tbody > tr > td > ul > .Array{color: Cyan;}
					.ErrorMessage > .Content > .Backtrace > tbody > tr > td > ul > .Object{color: Lime;}
					.ErrorMessage > .Content > .Backtrace > tbody > tr > td > ul > .Null{color: Pink;}

					.CommandPrompt{margin-top: 15px; xfont-size: 32px;}
					.CommandPrompt > .Prompt{margin-right: 15px; color: Lime; font-style: italic;}
					.CommandPrompt > .Command{color: Yellow;}
					.CommandPrompt > .Response{text-align: justify;}
					.CommandPrompt > .Response > .Subject{color: Cyan;}
					.CommandPrompt > .Response > .Description{margin-top: 15px;}
					.CommandPrompt > .Response a{color: Cyan; text-decoration: none;}

					@keyframes StaticSignal{100% {background-position: bottom;}}
					.StaticSignal{display: none; position: fixed; left: 0; top: 0; right: 0; bottom: 0; background-size: 100% 125%; background-image: url('" . file_get_contents(__DIR__ . "/resource/image/static-signal.base64") . "'); opacity: 0.90;}
				</style>
			</head>

			<body>
				<div id=\"FrameworkErrorHandler\" class=\"ErrorMessage\">
					<div class=\"Title\">
						<img src=\"" . file_get_contents(__DIR__ . "/resource/image/bug.base64") . "\" alt=\"Bug\" class=\"Icon\">
						Error <span class=\"Number\">{$Number}</span>:
						<a href=\"http://Google.Com/search?q=" . urlencode("PHP {$Message}") . "\" target=\"_blank\" class=\"Message\">" . str_replace(PHP_EOL, "<br>", $Message) . "</a>
						<!--" . (isset($Calls[1]["file"]) ? "in <span class=\"Script\">{$Calls[1]["file"]}</span>" : null) . (isset($Calls[1]["line"]) ? " at line <span class=\"Line\">{$Calls[1]["line"]}</span>" : null) . "-->
						" . ($_SERVER["REQUEST_METHOD"] == "GET" ? "<span id=\"Action\" class=\"Action\" onclick=\"ShowStaticSignal(); window.location.href = window.location.href;\"><img src=\"" . file_get_contents(__DIR__ . "/resource/image/navigate.base64") . "\" alt=\"Navigate\" class=\"Icon\"> Retry</span>" : "<span id=\"Action\" class=\"Action\" onclick=\"ShowStaticSignal(); window.location.reload();\"><img src=\"" . file_get_contents(__DIR__ . "/resource/image/reload.base64") . "\" alt=\"Reload\" class=\"Icon\"> Retry</span>") . "
					</div>

					<div class=\"Content\">
						" . (isset($CallRow) ?  "<table class=\"Backtrace\">
							<thead>
								<tr class=\"Trace\">
									<th colspan=\"99\">
										Call trace
										<span class=\"Time\">" . date("r") . "</span>
									</th>
								</tr>

								<tr>
									<th>#</th>
									<th>Script</th>
									<th>Line</th>
									<th>Object</th>
									<th>Type</th>
									<th>Function</th>
									<th>Argument</th>
								</tr>
							</thead>

							<tbody>" . implode(null, $CallRow) . "</tbody>
						</table>" : null) . "
					</div>
				</div>

				" . (isset($ResonHTML) ? "<div class=\"CommandPrompt\"><span class=\"Prompt\">{$CommandPromptPrefix}waste@Time{$CommandPrompt}</span><span class=\"Command\">reason</span><div class=\"Response\"><ul class=\"Reason\">" . implode(null, $ResonHTML) . "</ul></div></div>" : null) . "
				<div class=\"CommandPrompt\"><span class=\"Prompt\">{$CommandPromptPrefix}bored@Work{$CommandPrompt}</span><span class=\"Command\">joke</span><div class=\"Response\">{$CurrentJoke}</div></div>
				<div class=\"CommandPrompt\"><span class=\"Prompt\">{$CommandPromptPrefix}student@PHP{$CommandPrompt}</span><span class=\"Command\">learn</span><div class=\"Response\"><div class=\"Subject\">{$CurrentLearn[0]}</div><div class=\"Description\">{$CurrentLearn[1]}</div></div></div>
				<div class=\"CommandPrompt\"><span class=\"Prompt\">{$CommandPromptPrefix}developer@Home{$CommandPrompt}</span><span class=\"Command\">practice</span><div class=\"Response\">{$CurrentPractice}</div></div>
				<div class=\"CommandPrompt\"><span class=\"Prompt\">{$CommandPromptPrefix}new@sPHP{$CommandPrompt}</span><span class=\"Command\">help</span><div class=\"Response\"><div class=\"Subject\">{$CurrentsPHP[0]}</div><div class=\"Description\">{$CurrentsPHP[1]}</div></div></div>
				<div class=\"CommandPrompt\"><span class=\"Prompt\">{$CommandPromptPrefix}new@sPHP{$CommandPrompt}</span><span class=\"Command\">retry<span class=\"Blink\">_</span></span></div>
				<div id=\"StaticSignal\" class=\"StaticSignal\"></div>
			</body>

			<script>
				document.body.onkeyup = function(e){
					if(e.keyCode == 13)document.getElementById('Action').click();

					return true;
				};

				function ShowStaticSignal(){
					objStaticSignal = document.getElementById('StaticSignal');

					objStaticSignal.style.animation = 'StaticSignal 1s linear infinite';
					objStaticSignal.style.display = 'block';

					return true;
				}
			</script>
		</html>
	");

	return true; // Don't execute PHP internal error handler
}
?>