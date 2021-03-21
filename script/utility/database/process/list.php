<?php
namespace sPHP;

?>

<h1>
	Database process list

	<div class="Control">
		<input type="text" id="inpUser" placeholder="User">
		<input type="password" id="inpPassword" placeholder="Password">
		<label class="LinkButton" onclick="objRequestTimer = Restart(objRequestTimer);"><input type="checkbox" id="inpSleeping"> Sleeping</label>

		<select id="inpInterval" onchange="objRequestTimer = Restart(objRequestTimer);">
			<option value="2">2 Sec</option>
			<option value="5" selected>5 Sec</option>
			<option value="10">10 Sec</option>
			<option value="20">20 Sec</option>
		</select>

		<button type="button" id="btnToggle" onclick="objRequestTimer = Toggle(objRequestTimer);">Stop</button>
	</div>

	<span id="Message"></span>
	<span id="Error"></span>
</h1>

<ul id="DatabaseProcessList"></ul>

<script>
	function Start(UpdateInterval){ //console.log(UpdateInterval);
		var elmMessage = document.getElementById('Message');
		var elmError = document.getElementById('Error');
		var elmProcessList = document.getElementById('DatabaseProcessList');
		var APIURL = '<?=$APP->URL("API/V1/Utility/Database/Process/List")?>&User=' + document.getElementById('inpUser').value + '&Password=' + document.getElementById('inpPassword').value + '&Sleeping=' + (document.getElementById('inpSleeping').checked ? 'TRUE' : 'FALSE') + '';

		return sJS.HTTP.GetAtInterval(UpdateInterval * 1000, APIURL, function(Response){
			if(Response.Error.Code != 0){
				elmError.innerHTML = '' + Response.Error.Code + ': ' + Response.Error.Description;
			}
			else{
				elmProcessList.innerHTML = '';
				elmError.innerHTML = '';
	
				if(Response.Response.Process.length){ //console.log(Response.Response.Process);
					elmMessage.innerHTML = '' + sJS.Time.Format(new Date(), 'g:i:s A') + '';
					Response.Response.Process.sort(function(Previous, Next){return Previous.Time - Next.Time;});
					Response.Response.Process.reverse();
					
					Response.Response.Process.forEach(function(Process, ProcessIndex){ //console.log(Process.Time);
						var ProcessProgressPercentile = parseInt(Process.Progress);
						var elmProcess = document.createElement("LI");
						elmProcess.innerHTML = '<progress value="' + ProcessProgressPercentile + '" max="100" class="ProgressBar">' + ProcessProgressPercentile + '%</progress><span class="Serial">' + (ProcessIndex + 1) + '</span><span class="ID">' + Process.Id + '</span><span class="User">' + Process.User + '</span><span class="Duration">' + Process.Time + '</span></span><span class="Command">' + Process.Command + '</span></span><span class="Progress">' + parseFloat(Process.Progress).toFixed(2) + '</span><span class="State">' + Process.State + '</span><div readonly class="SQL">' + (Process.Info ? Process.Info : '') + '</div>';
						elmProcessList.appendChild(elmProcess);
					});
				}
			}
		}, function(Code, Description){ //console.log(Code, Description);
			elmError.innerHTML = '' + Code + ': ' + Description + '';
		}, UpdateInterval * 500, true); //console.log(PeriodicHTTPRequestTimer);
	}

	function Stop(TimerObject){
		clearInterval(TimerObject);
		return null;
	}

	function Toggle(objTimer = null){
		elmToggle = document.getElementById('btnToggle');

		if(objTimer){
			objTimer = Stop(objTimer);
			elmToggle.innerHTML = 'Start';
			elmToggle.className = 'ButtonStart';
		}
		else{
			objTimer = Start(document.getElementById('inpInterval').value);
			elmToggle.innerHTML = 'Stop';
			elmToggle.className = 'ButtonStop';
		}

		return objTimer;
	}

	function Restart(objTimer = null){
		objTimer = Toggle(objTimer);
		objTimer = Toggle(objTimer);

		return objTimer;
	}

	var objRequestTimer = Toggle();
</script>