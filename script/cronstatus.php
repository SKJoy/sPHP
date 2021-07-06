<?php
namespace sPHP;

$CronHTML = [];
$DateTimeFormat = "{$Configuration["ShortDateFormat"]} {$Configuration["TimeFormat"]}"; //DebugDump($DateTimeFormat);

foreach([
	"Application",
	//"Non critical", 
	//"Test", 
] as $CronIndex => $Cron){
	$CronFileBaseName[$CronIndex] = str_replace(" ", "_", $Cron);
	$StatusJSONFile = "{$Environment->ScriptPath()}cron/process/{$CronFileBaseName[$CronIndex]}/status.json";

	if(file_exists($StatusJSONFile)){
		$Status = json_decode(file_get_contents($StatusJSONFile));

		if(isset($Status->Running)){
			$CronIsRunning = $Status->Running ? "Yes" : "No";

			$CronHTML[] = "
				<h2>
					<img src=\"{$Environment->IconURL()}process.png\" alt=\"Process\" class=\"Icon\">
					{$Cron}

					<span class=\"Action\">
						<a href=\"{$Application->URL($_POST["_Script"], "RandomNumber=" . rand() . "")}#Cron_{$CronIndex}\" class=\"Item\"><img src=\"{$Environment->IconURL()}reload.png\" alt=\"Reload\" title=\"Reload\" class=\"Icon\">Reload</a>
						<a href=\"{$Application->URL("Cron/{$CronFileBaseName[$CronIndex]}", "Command=EXECUTE")}\" target=\"_blank\" class=\"Item\"><img src=\"{$Environment->IconURL()}execute.png\" alt=\"Execute\" title=\"Execute\" class=\"Icon\">Execute</a>
						<a href=\"{$Application->URL("Cron/{$CronFileBaseName[$CronIndex]}", "Command=RESUME")}\" target=\"_blank\" class=\"Item\"><img src=\"{$Environment->IconURL()}start.png\" alt=\"Start\" title=\"Start\" class=\"Icon\">Start</a>
						<a href=\"{$Application->URL("Cron/{$CronFileBaseName[$CronIndex]}", "Command=EXIT")}\" target=\"_blank\" class=\"Item\"><img src=\"{$Environment->IconURL()}stop.png\" alt=\"Stop\" title=\"Stop\" class=\"Icon\">Stop</a>
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
						<tr class=\"CronRunning_{$CronIsRunning}\">
							<td id=\"Cron-{$CronFileBaseName[$CronIndex]}-Status\">{$CronIsRunning}</td>
							<td id=\"Cron-{$CronFileBaseName[$CronIndex]}-Time-Begin\">" . date($DateTimeFormat, strtotime($Status->Time->Begin)) . "</td>
							<td id=\"Cron-{$CronFileBaseName[$CronIndex]}-Time-End\">" . date($DateTimeFormat, strtotime($Status->Time->End)) . "</td>
							<td id=\"Cron-{$CronFileBaseName[$CronIndex]}-Time-Duration\" class=\"Highlight\">{$Utility->SecondToTime($Status->Time->Duration)}</td>
							<td id=\"Cron-{$CronFileBaseName[$CronIndex]}-Load-Memory\" class=\"Warning\">" . round($Status->Load->Memory / 1024 / 1024, 2) . " MB</td>
							<td id=\"Cron-{$CronFileBaseName[$CronIndex]}-Load-System\" class=\"Warning\">" . number_format($Status->Load->System, 2) . "%</td>
							<td id=\"Cron-{$CronFileBaseName[$CronIndex]}-Exit-Reason\">{$Status->Exit->Reason}</td>
							<td id=\"Cron-{$CronFileBaseName[$CronIndex]}-Exit-Time\">" . ($Status->Exit->Time ? date($DateTimeFormat, strtotime($Status->Exit->Time)) : null) . "</td>
							<td id=\"Cron-{$CronFileBaseName[$CronIndex]}-Configuration-Interval\">{$Utility->SecondToTime($Status->Configuration->Interval)}</td>
							<td id=\"Cron-{$CronFileBaseName[$CronIndex]}-Configuration-MaximumExecutionTime\">{$Utility->SecondToTime($Status->Configuration->MaximumExecutionTime)}</td>
							<td id=\"Cron-{$CronFileBaseName[$CronIndex]}-Iteration-Count\" class=\"IterationCount\">{$Status->Iteration->Count}</td>
							<td id=\"Cron-{$CronFileBaseName[$CronIndex]}-Iteration-Time-Begin\">" . date($DateTimeFormat, strtotime($Status->Iteration->Time->Begin)) . "</td>
							<td id=\"Cron-{$CronFileBaseName[$CronIndex]}-Iteration-Time-End\">" . date($DateTimeFormat, strtotime($Status->Iteration->Time->End)) . "</td>
							<td id=\"Cron-{$CronFileBaseName[$CronIndex]}-Iteration-Time-Duration\" class=\"Highlight\">{$Utility->SecondToTime($Status->Iteration->Time->Duration)}</td>
							<td id=\"Cron-{$CronFileBaseName[$CronIndex]}-Status-Age\" class=\"Highlight\">{$Utility->SecondToTime(time() - filemtime($StatusJSONFile))}</td>
						</tr>
					</tbody>
				</table>
			"; //DebugDump($Status);

			$JobTableRowHTML = [];
			$JobCount = 0;

			foreach($Status->Job as $JobName => $Job){ //DebugDump($Job);
				$JobCount++;
				$JobIsRunning = isset($Job->Running) && $Job->Running ? "Yes" : "No";

				$JobTableRowHTML[$Job->Configuration->Order] = "
					<tr id=\"CronJob-{$CronFileBaseName[$CronIndex]}-{$JobName}\" class=\"JobRunning_{$JobIsRunning}\">
						<td id=\"CronJob-{$CronFileBaseName[$CronIndex]}-{$JobName}-Serial\" class=\"Serial\">{$JobCount}</td>
						<td>
							<span class=\"Important\">{$JobName}</span><br>
							<span class=\"Command\">{$Job->Configuration->Command}</span>
						</td>
						<td>
							Interval: {$Utility->SecondToTime($Job->Configuration->Interval)}<br>
							Limit: {$Utility->SecondToTime($Job->Configuration->MaximumExecutionTime)}
						</td>
						<td class=\"Time\">
							" . (isset($Job->Time->Begin) ? "Begin: <span id=\"CronJob-{$CronFileBaseName[$CronIndex]}-{$JobName}-Time-Begin\" class=\"Important\">" . date($DateTimeFormat, strtotime($Job->Time->Begin)) . "</span><br>" : null) . "
							" . (isset($Job->Time->End) ? "End: <span id=\"CronJob-{$CronFileBaseName[$CronIndex]}-{$JobName}-Time-End\">" . date($DateTimeFormat, strtotime($Job->Time->End)) . "</span><br>" : null) . "
							" . (isset($Job->Time->Duration) ? "Duration: <span id=\"CronJob-{$CronFileBaseName[$CronIndex]}-{$JobName}-Time-Duration\">{$Utility->SecondToTime($Job->Time->Duration)}</span>" : null) . "
						</td>
						<td id=\"CronJob-{$CronFileBaseName[$CronIndex]}-{$JobName}-Comment\">" . implode("<br>", array_filter([isset($Job->Comment) ? $Job->Comment : null, isset($Job->Result->Error->Message) && $Job->Result->Error->Message ? "Error: <span class=\"Error\">{$Job->Result->Error->Message}</span>" : null, ])) . "</td>
						<td id=\"CronJob-{$CronFileBaseName[$CronIndex]}-{$JobName}-Status\">" . implode("<br>", isset($Job->Result->Status) ? $Job->Result->Status : []) . "</td>
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

	table > tbody > tr .Important{font-weight: bold;}
	table > tbody > tr .Highlight{background-color: Yellow; text-shadow: 1px 1px White;}
	table > tbody > tr .Warning{background-color: Orange; color: White; text-shadow: -1px -1px Black;}
	table > tbody > tr .Error{color: Red;}

	.CronTable{width: 100%; text-align: center;}
	.CronTable > thead > tr:first-child{background-color: Black;}
	.CronTable > tbody > tr > td:first-child{font-weight: bold; text-transform: uppercase;}
	.CronTable > tbody > .CronRunning_Yes > td:first-child{background-color: Lime; text-shadow: 1px 1px White;}
	.CronTable > tbody > .CronRunning_No > td:first-child{background-color: Red; color: White; text-shadow: 1px 1px Black;}
	.CronTable > tbody > tr > .IterationCount{background-color: Cyan; font-weight: bold; text-shadow: 1px 1px White;}

	.JobTable{margin-bottom: 15px; width: 100%;}
	.JobTable > tbody > tr > td > .Title{font-weight: bold;}
	.JobTable > tbody > tr > td > .Command{color: Blue;}
	.JobTable > tbody > tr > .Serial{text-align: right; transition: all 1s;}
	.JobTable > tbody > tr > .Serial:after{content: '.';}

	.JobTable > tbody > .JobRunning_Yes > .Serial{background-color: Lime; font-weight: bold; text-shadow: 1px 1px White; transition: all 1s;}
	.JobTable > tbody > .JobRunning_No > .Serial{}
</style>

<h1><img src="<?=$Environment->IconURL()?>cron.png" alt="Cron" class="Icon"> Cron status</h1>
<?=implode(null, $CronHTML)?>

<script>
	var DateTimeFormat = '<?=$DateTimeFormat?>'; //console.log('DateTimeFormat = ' + DateTimeFormat + ';');
	var CronStatusPath = '<?="{$Environment->ScriptURL()}cron/process/"?>'; //console.log('CronStatusPath = ' + CronStatusPath + ';');
	var Cron = <?=json_encode($CronFileBaseName)?>; //console.log('Cron', Cron);

	Cron.forEach(function(Cron, CronIndex){ //console.log('Cron = ' + Cron + '; CronIndex = ' + CronIndex + ';');
		var StatusFile = CronStatusPath + Cron + '/status.json'; //console.log('StatusFile = ' + StatusFile + ';');

		sJS.HTTP.GetAtInterval(
			3000, // Milisecond in positive integer
			StatusFile, // URL: String
			function(Response){ // OnSuccess: Function(Response)
				CronStatus = Response.Running ? 'Yes' : 'No';

				elmCronStatus = document.getElementById('Cron-' + Cron + '-Status');
				elmCronTimeBegin = document.getElementById('Cron-' + Cron + '-Time-Begin');
				elmCronTimeEnd = document.getElementById('Cron-' + Cron + '-Time-End');
				elmCronTimeDuration = document.getElementById('Cron-' + Cron + '-Time-Duration');
				elmCronLoadMemory = document.getElementById('Cron-' + Cron + '-Load-Memory');
				elmCronLoadSystem = document.getElementById('Cron-' + Cron + '-Load-System');
				elmCronExitReason = document.getElementById('Cron-' + Cron + '-Exit-Reason');
				elmCronExitTime = document.getElementById('Cron-' + Cron + '-Exit-Time');
				elmCronConfigurationInterval = document.getElementById('Cron-' + Cron + '-Configuration-Interval');
				elmCronConfigurationMaximumExecutionTime = document.getElementById('Cron-' + Cron + '-Configuration-MaximumExecutionTime');
				elmCronIterationCount = document.getElementById('Cron-' + Cron + '-Iteration-Count');
				elmCronIterationTimeBegin = document.getElementById('Cron-' + Cron + '-Iteration-Time-Begin');
				elmCronIterationTimeEnd = document.getElementById('Cron-' + Cron + '-Iteration-Time-End');
				elmCronIterationTimeDuration = document.getElementById('Cron-' + Cron + '-Iteration-Time-Duration');
				elmCronStatusAge = document.getElementById('Cron-' + Cron + '-Status-Age');

				elmCronStatus.className = 'CronRunning_' + CronStatus + '';
				elmCronStatus.innerHTML = CronStatus;
				elmCronTimeBegin.innerHTML = sJS.Time.Format(new Date(Response.Time.Begin), DateTimeFormat);
				elmCronTimeEnd.innerHTML = sJS.Time.Format(new Date(Response.Time.End), DateTimeFormat);
				elmCronTimeDuration.innerHTML = sJS.Time.SecondToString(Response.Time.Duration);
				elmCronLoadMemory.innerHTML = Math.round(Response.Load.Memory / 1024 / 1024, 2) + ' MB';
				elmCronLoadSystem.innerHTML = Response.Load.System.toFixed(2) + '%';
				elmCronExitReason.innerHTML = Response.Exit.Reason;
				elmCronExitTime.innerHTML = Response.Exit.Time ? sJS.Time.Format(new Date(Response.Exit.Time), DateTimeFormat) : '';
				elmCronConfigurationInterval.innerHTML = sJS.Time.SecondToString(Response.Configuration.Interval);
				elmCronConfigurationMaximumExecutionTime.innerHTML = sJS.Time.SecondToString(Response.Configuration.MaximumExecutionTime);
				elmCronIterationCount.innerHTML = Response.Iteration.Count;
				elmCronIterationTimeBegin.innerHTML = sJS.Time.Format(new Date(Response.Iteration.Time.Begin), DateTimeFormat);
				elmCronIterationTimeEnd.innerHTML = sJS.Time.Format(new Date(Response.Iteration.Time.End), DateTimeFormat);
				elmCronIterationTimeDuration.innerHTML = sJS.Time.SecondToString(Response.Iteration.Time.Duration);
				//elmCronStatusAge.innerHTML = Response.Iteration.Time.Duration;

				for(JobName in Response.Job){
					Job = Response.Job[JobName]; //console.log(JobName, Job);

					elmCronJob = document.getElementById('CronJob-' + Cron + '-' + JobName + '');
					elmCronJobSerial = document.getElementById('CronJob-' + Cron + '-' + JobName + '-Serial');
					elmCronJobTimeBegin = document.getElementById('CronJob-' + Cron + '-' + JobName + '-Time-Begin');
					elmCronJobTimeEnd = document.getElementById('CronJob-' + Cron + '-' + JobName + '-Time-End');
					elmCronJobTimeDuration = document.getElementById('CronJob-' + Cron + '-' + JobName + '-Time-Duration');
					elmCronJobComment = document.getElementById('CronJob-' + Cron + '-' + JobName + '-Comment');
					elmCronJobStatus = document.getElementById('CronJob-' + Cron + '-' + JobName + '-Status');

					elmCronJob.className = 'JobRunning_' + (Job.Running ? 'Yes' : 'No') + '';
					elmCronJobTimeBegin.innerHTML = sJS.Time.Format(new Date(Job.Time.Begin), DateTimeFormat);
					elmCronJobTimeEnd.innerHTML = sJS.Time.Format(new Date(Job.Time.End), DateTimeFormat);
					elmCronJobTimeDuration.innerHTML = sJS.Time.SecondToString(Job.Time.Duration);
					elmCronJobComment.innerHTML = (Job.Comment ? Job.Comment : '') + (Job.Result.Error.Message ? '<br>Error: <span class=\"Error\">' + Job.Result.Error.Message + '</span>' : '');
					elmCronJobStatus.innerHTML = Job.Result.Status.join('<br>');
				}
			},
			function(Code, Description){ // OnFail: Function(Code, Description)
				if(Code != 404)console.log('Error for ' + Cron + ' #' + Code + ': ' + Description);
			},
			1000, // TimeOut: Milisecond in positive integer
			true, // ProcessJSON: Boolean, 
			function(){ // OnStart: Callback function on HTTP invoke process start			
				//console.log('Fetching status for ' + Cron);
			},
			Data = [], // POST field // Needs work
			File = [] // FILE field // Needs work
		);
	});
</script>