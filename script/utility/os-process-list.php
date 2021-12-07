<?php
namespace sPHP;
?>

<h1>Operating system process list</h1>

<?php
print HTML\UI\Form($APP->URL($_POST["_Script"]), [
	HTML\UI\Input("CommandFilter", null, null, null, null, null, "Command filter"), 
	HTML\UI\Input("CommandFilterLeft", null, null, null, null, null, "Command filter from left"), 
	HTML\UI\Button("Filter", BUTTON_TYPE_SUBMIT)
], "", null, null, null, null, null, null, false);

$HTML = [];
$ProcessCounter = 0;

foreach($ENV->OSProcesses("Command", $_POST["CommandFilter"], $_POST["CommandFilterLeft"]) as $ThisProcess){	
	$ProcessCounter++;

	$HTML[] = "
		<tr>
			<td class=\"Serial\">{$ProcessCounter}.</td>
			<td class=\"AlignCenter\">{$ThisProcess["User"]}</td>
			<td class=\"AlignRight\">{$ThisProcess["ID"]}</td>
			<td class=\"AlignRight\">{$ThisProcess["CPU"]}%</td>
			<td class=\"AlignRight\">{$ThisProcess["Memory"]}%</td>
			<td class=\"AlignRight\">{$ThisProcess["VSZ"]}</td>
			<td class=\"AlignRight\">{$ThisProcess["RSS"]}</td>
			<td class=\"AlignCenter\">{$ThisProcess["TTY"]}</td>
			<td class=\"AlignCenter\">{$ThisProcess["Stat"]}</td>
			<td class=\"AlignRight\">{$ThisProcess["Start"]}</td>
			<td class=\"AlignRight\">{$ThisProcess["Time"]}</td>
			<td>{$ThisProcess["Command"]}</td>
		</tr>
	";
}
?>

<br><br>

<table class="ProcessList">
	<thead>
		<tr>
			<th class="Serial">#</th>
			<th>User</th>
			<th>ID</th>
			<th>CPU</th>
			<th>Memory</th>
			<th>VSZ</th>
			<th>RSS</th>
			<th>TTY</th>
			<th>Stat</th>
			<th>Start</th>
			<th>Time</th>
			<th>Command</th>
		</tr>
	</thead>
	<tbody><?=implode(null, $HTML)?></tbody>
</table>