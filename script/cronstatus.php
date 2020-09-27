<?php
namespace sPHP;
$CronHTML = [];

foreach([
	"Application",
	//"Non critical", 
] as $CronIndex => $Cron){
	$CronFileBaseName = str_replace(" ", "_", $Cron);
	$StatusJSONFile = "{$Environment->ScriptPath()}cron/process/{$CronFileBaseName}/status.json";

	if(file_exists($StatusJSONFile)){
		$Status = json_decode(file_get_contents($StatusJSONFile));
		$CronIsRunning = $Status->Running ? "Yes" : "No";

		$CronHTML[] = "
			<h2 id=\"Cron_{$CronIndex}\">
				<img src=\"{$Environment->IconURL()}process.png\" alt=\"Process\" class=\"Icon\">
				{$Cron}

				<span class=\"Action\">
					<a href=\"{$Application->URL($_POST["_Script"], "RandomNumber=" . rand() . "")}#Cron_{$CronIndex}\" class=\"Item\"><img src=\"{$Environment->IconURL()}reload.png\" alt=\"Reload\" title=\"Reload\" class=\"Icon\">Reload</a>
					<a href=\"{$Application->URL("Cron/{$CronFileBaseName}", "Command=EXECUTE")}\" target=\"_blank\" class=\"Item\"><img src=\"{$Environment->IconURL()}execute.png\" alt=\"Execute\" title=\"Execute\" class=\"Icon\">Execute</a>
					<a href=\"{$Application->URL("Cron/{$CronFileBaseName}", "Command=RESUME")}\" target=\"_blank\" class=\"Item\"><img src=\"{$Environment->IconURL()}start.png\" alt=\"Start\" title=\"Start\" class=\"Icon\">Start</a>
					<a href=\"{$Application->URL("Cron/{$CronFileBaseName}", "Command=EXIT")}\" target=\"_blank\" class=\"Item\"><img src=\"{$Environment->IconURL()}stop.png\" alt=\"Stop\" title=\"Stop\" class=\"Icon\">Stop</a>
				</span>
			</h2>

			<table class=\"CronTable\">
				<thead>
					<tr>
						<th rowspan=\"2\">Running</th>
						<th colspan=\"3\">Time</th>
						<th colspan=\"2\">Load</th>
						<th colspan=\"2\">Exit</th>
						<th colspan=\"2\">Configuration</th>
						<th colspan=\"4\">Iteration</th>
						<th rowspan=\"2\">Status</th>
					</tr>
					<tr>
						<th>Begin</th>
						<th>End</th>
						<th>Duration</th>
						<th>Memory</th>
						<th>CPU</th>
						<th>Reason</th>
						<th>Time</th>
						<th>Interval</th>
						<th>Time limit</th>
						<th>Count</th>
						<th>Begin</th>
						<th>End</th>
						<th>Duration</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class=\"HighlightPositive_{$CronIsRunning}\">{$CronIsRunning}</td>
						<td>" . date("Y-m-d H:i:s", strtotime($Status->Time->Begin)) . "</td>
						<td>" . date("Y-m-d H:i:s", strtotime($Status->Time->End)) . "</td>
						<td class=\"HighlightImportant\">{$Utility->SecondToTime($Status->Time->Duration)}</td>
						<td class=\"HighlightWarning\">" . round($Status->Load->Memory / 1024 / 1024, 2) . " MB</td>
						<td class=\"HighlightWarning\">" . number_format($Status->Load->System, 2) . "%</td>
						<td>{$Status->Exit->Reason}</td>
						<td>" . ($Status->Exit->Time ? date("Y-m-d H:i:s", strtotime($Status->Exit->Time)) : null) . "</td>
						<td>{$Utility->SecondToTime($Status->Configuration->Interval)}</td>
						<td>{$Utility->SecondToTime($Status->Configuration->MaximumExecutionTime)}</td>
						<td class=\"HighlightImportant2\">{$Status->Iteration->Count}</td>
						<td>" . date("Y-m-d H:i:s", strtotime($Status->Iteration->Time->Begin)) . "</td>
						<td>" . date("Y-m-d H:i:s", strtotime($Status->Iteration->Time->End)) . "</td>
						<td class=\"HighlightImportant\">{$Utility->SecondToTime($Status->Iteration->Time->Duration)}</td>
						<td class=\"HighlightImportant\">{$Utility->SecondToTime(time() - filemtime($StatusJSONFile))}</td>
					</tr>
				</tbody>
			</table>
		";
	//DebugDump($Status);
		$JobTableRowHTML = [];
		$JobCount = 0;

		foreach($Status->Job as $JobName => $Job){ //DebugDump($Job);
			$JobCount++;

			$JobTableRowHTML[$Job->Configuration->Order] = "
				<tr>
					<td class=\"Serial\">{$JobCount}</td>
					<td>
						<span class=\"Title\">{$JobName}</span><br>
						<span class=\"Command\">{$Job->Configuration->Command}</span>
					</td>
					<td>
						Interval: {$Utility->SecondToTime($Job->Configuration->Interval)}<br>
						Limit: {$Utility->SecondToTime($Job->Configuration->MaximumExecutionTime)}
					</td>
					<td>
						Begin: <span class=\"HighlightImportant\">" . date("Y-m-d H:i:s", strtotime($Job->Time->Begin)) . "</span><br>
						End: " . date("Y-m-d H:i:s", strtotime($Job->Time->End)) . "<br>
						Duration: {$Utility->SecondToTime($Job->Time->Duration)}
					</td>
					<td>" . implode("<br>", array_filter([$Job->Comment, $Job->Result->Error->Message ? "Error: <span class=\"HighlightError\">{$Job->Result->Error->Message}</span>" : null, ])) . "</td>
					<td>" . implode("<br>", $Job->Result->Status) . "</td>
				</tr>
			";
		}

		ksort($JobTableRowHTML);

		$CronHTML[] = "
			<table class=\"JobTable\">
				<thead>
					<tr>
						<th>#</th>
						<th>Job / Command</th>
						<th>Configuration</th>
						<th>Time</th>
						<th>Comment / Error</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>" . implode(null, $JobTableRowHTML) . "</tbody>
			</table>
		";
	}
}
?>

<style>
	h1{border-bottom: 1px Silver solid; padding-bottom: 5px;}
	h2{font-weight: bold;}
	h2 > .Icon{height: 1.61em;}
	h2 > .Action{float: right; color: Blue; font-weight: normal;}
	h2 > .Action > .Item:hover{text-decoration: none;}
	h2 > .Action > .Item > .Icon{margin-right: 5px; height: 1.61em;}
	h2:after{display: block; clear: both; content: '';}

	.CronTable{width: 100%; text-align: center;}
	.CronTable > thead > tr:first-child{background-color: Black;}
	.CronTable > tbody .HighlightImportant{background-color: Yellow; text-shadow: 1px 1px White;}
	.CronTable > tbody .HighlightImportant2{background-color: Cyan; text-shadow: 1px 1px White;}
	.CronTable > tbody .HighlightWarning{background-color: Orange; color: White;}
	.CronTable > tbody .HighlightPositive_Yes{background-color: Lime; font-weight: bold; text-transform: uppercase; text-shadow: 1px 1px White;}
	.CronTable > tbody .HighlightPositive_No{background-color: Red; color: White; font-weight: bold; text-transform: uppercase; text-shadow: 1px 1px Black;}

	.JobTable{margin-bottom: 15px; width: 100%;}
	.JobTable > tbody .Serial{text-align: right;}
	.JobTable > tbody .Serial:after{content: '.';}
	.JobTable > tbody .Title{font-weight: bold;}
	.JobTable > tbody .Command{color: Blue;}
	.JobTable > tbody .HighlightImportant{font-weight: bold;}
	.JobTable > tbody .HighlightError{color: Red;}
</style>

<h1><img src="<?=$Environment->IconURL()?>cron.png" alt="Cron" class="Icon"> Cron status</h1>
<?=implode(null, $CronHTML)?>