<?php
namespace sPHP;

?>

<style>
	h1 > .Control{display: inline-block; font-weight: normal; font-size: 14px; vertical-align: middle;}
	h1 > .Control > button{width: 2.25em; padding: 5px; font-weight: bold;}
	h1 > #Message{font-weight: normal; vertical-align: middle;}
	h1 > #Error{color: Red; font-weight: normal; vertical-align: middle;}

	#DatabaseProcessList{margin: 0; padding: 0; font-family: Consolas, Courier New, Monospace; font-size: 18px; line-height: 1;}
	#DatabaseProcessList > li{margin: 0; margin-left: 18px; padding: 0;}
	#DatabaseProcessList > li > .Serial{padding: 5px;}
	#DatabaseProcessList > li > .ID{padding: 5px; color: Blue; font-weight: bold;}
	#DatabaseProcessList > li > .ID:before{content: '# ';}
	#DatabaseProcessList > li > .User{padding: 5px; font-weight: bold;}
	#DatabaseProcessList > li > .Duration{padding: 5px; color: Red; font-weight: bold;}
	#DatabaseProcessList > li > .Duration:after{content: ' Sec';}
	#DatabaseProcessList > li > .Command{padding: 5px; color: Orange; font-weight: bold;}
	#DatabaseProcessList > li > .Progress{padding: 5px; color: Green;}
	#DatabaseProcessList > li > .Progress:after{content: '%';}
	#DatabaseProcessList > li > .State{padding: 5px;}
	#DatabaseProcessList > li > .SQL{max-height: 5.5em; border-radius: 5px; border-width: 0; background-color: Black; padding: 5px 15px 5px 15px; color: White; line-height: 1.62; white-space: pre; overflow-y: auto;}
</style>

<h1>
	Database process list

	<div class="Control">
		<select id="UpdateInterval">
			<option value="2">2 Sec</option>
			<option value="5" selected>5 Sec</option>
			<option value="10">10 Sec</option>
			<option value="20">20 Sec</option>
		</select>
	
		<button type="button" onclick="if(!PeriodicHTTPRequestTimer)PeriodicHTTPRequestTimer = Start(document.getElementById('UpdateInterval').value);">▶</button>
		<button type="button" onclick="PeriodicHTTPRequestTimer = Stop(PeriodicHTTPRequestTimer);">◾</button>
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

		return sJS.HTTP.GetAtInterval(UpdateInterval * 1000, '<?=$APP->URL("API/V1/Utility/Database/Process/List_Root", "Sleeping=FALSE")?>', function(Response){
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
						var elmProcess = document.createElement("LI");
						elmProcess.innerHTML = '<span class="Serial">' + (ProcessIndex + 1) + '</span><span class="ID">' + Process.Id + '</span><span class="User">' + Process.User + '</span><span class="Duration">' + Process.Time + '</span></span><span class="Command">' + Process.Command + '</span></span><span class="Progress">' + parseFloat(Process.Progress).toFixed(2) + '</span><span class="State">' + Process.State + '</span><div readonly class="SQL">' + Process.Info + '</div>';
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

	var PeriodicHTTPRequestTimer = null;
	PeriodicHTTPRequestTimer = Start(document.getElementById('UpdateInterval').value);
</script>