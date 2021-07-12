<?php
namespace sPHP;

print "
	<h1>Traffic statistics</h1>
	" . HTML\UI\Form($Application->URL($_POST["_Script"]), implode(null, [
		HTML\UI\Field(HTML\UI\Input("ApplicationTrafficTime" . ($Caption = "From") . "", null, date("Y-m-01"), true, INPUT_TYPE_DATE), "{$Caption}", null, null),
		HTML\UI\Field(HTML\UI\Input("ApplicationTrafficTime" . ($Caption = "To") . "", null, date("Y-m-t"), true, INPUT_TYPE_DATE), "{$Caption}", null, true),
		HTML\UI\Field(HTML\UI\Button("Show", BUTTON_TYPE_SUBMIT, "btnShow"), null, null, true),
	]), "", null, null, null, null, null, null, false) . "
";

$Error = $HitByLocation = $HitByDay = $HitByMonth = $HitByYear = $HitByScript = $HitByUser = [];
$TimeSelectSQLWHEREClause = "ATr.ApplicationTrafficTime BETWEEN '{$_POST["ApplicationTrafficTimeFrom"]} 00:00:00' AND '{$_POST["ApplicationTrafficTimeTo"]} 23:59:59'";

$Recordset = $Database->Query($SQL = "
	# Parameter
		SET @ColorValueMinimum := 127;
		SET @ColorValueMultiplier := @ColorValueMinimum + 1;

	# Top location
		SELECT			@Country := COALESCE(ATr.ApplicationTrafficCountry, 'Other') AS Country,
						@City := NULL, #COALESCE(ATr.ApplicationTrafficCity, 'Other') AS City,
						CONCAT_WS(', ', @City, @Country) AS Label,
						COUNT(0) AS Hit,
						CONCAT('#', LPAD(HEX(@ColorValueMinimum + ROUND(RAND() * @ColorValueMultiplier)), 2, '0'), LPAD(HEX(@ColorValueMinimum + ROUND(RAND() * @ColorValueMultiplier)), 2, '0'), LPAD(HEX(@ColorValueMinimum + ROUND(RAND() * @ColorValueMultiplier)), 2, '0')) AS Color
		FROM			sphp_applicationtraffic AS ATr
		WHERE			{$TimeSelectSQLWHEREClause}
		GROUP BY		Label
		ORDER BY		Hit DESC;

	# Generate hourly hit
		DROP TEMPORARY TABLE IF EXISTS tmp_applicationtraffichitbyday;

		CREATE TEMPORARY TABLE tmp_applicationtraffichitbyday AS
		SELECT			ATr.ApplicationTrafficTime,
						COUNT(0) AS Hit
		FROM			sphp_applicationtraffic AS ATr
		WHERE			{$TimeSelectSQLWHEREClause}
			AND			ATr.ApplicationTrafficTime > DATE_ADD(NOW(), INTERVAL -1 MONTH)
		GROUP BY		DATE_FORMAT(ATr.ApplicationTrafficTime, '%Y%m%d%H')
		ORDER BY		ATr.ApplicationTrafficTime ASC;

	# By hour
		SELECT			DATE_FORMAT(ApplicationTrafficTime, '%H:00') AS Label,
						Hit,
						CONCAT('#', LPAD(HEX(@ColorValueMinimum + ROUND(RAND() * @ColorValueMultiplier)), 2, '0'), LPAD(HEX(@ColorValueMinimum + ROUND(RAND() * @ColorValueMultiplier)), 2, '0'), LPAD(HEX(@ColorValueMinimum + ROUND(RAND() * @ColorValueMultiplier)), 2, '0')) AS Color
		FROM			tmp_applicationtraffichitbyday
		GROUP BY		Label
		ORDER BY		ApplicationTrafficTime ASC;

	# By date
		SELECT			DATE(ApplicationTrafficTime) AS Label,
						SUM(Hit) AS Hit,
						CONCAT('#', LPAD(HEX(@ColorValueMinimum + ROUND(RAND() * @ColorValueMultiplier)), 2, '0'), LPAD(HEX(@ColorValueMinimum + ROUND(RAND() * @ColorValueMultiplier)), 2, '0'), LPAD(HEX(@ColorValueMinimum + ROUND(RAND() * @ColorValueMultiplier)), 2, '0')) AS Color
		FROM			tmp_applicationtraffichitbyday
		GROUP BY		Label
		ORDER BY		ApplicationTrafficTime ASC;

	# By month
		SELECT			DATE_FORMAT(ApplicationTrafficTime, '%M, %Y') AS Label,
						SUM(Hit) AS Hit,
						CONCAT('#', LPAD(HEX(@ColorValueMinimum + ROUND(RAND() * @ColorValueMultiplier)), 2, '0'), LPAD(HEX(@ColorValueMinimum + ROUND(RAND() * @ColorValueMultiplier)), 2, '0'), LPAD(HEX(@ColorValueMinimum + ROUND(RAND() * @ColorValueMultiplier)), 2, '0')) AS Color
		FROM			tmp_applicationtraffichitbyday
		GROUP BY		Label
		ORDER BY		ApplicationTrafficTime ASC;

	# By year
		SELECT			DATE_FORMAT(ApplicationTrafficTime, '%Y') AS Label,
						SUM(Hit) AS Hit,
						CONCAT('#', LPAD(HEX(@ColorValueMinimum + ROUND(RAND() * @ColorValueMultiplier)), 2, '0'), LPAD(HEX(@ColorValueMinimum + ROUND(RAND() * @ColorValueMultiplier)), 2, '0'), LPAD(HEX(@ColorValueMinimum + ROUND(RAND() * @ColorValueMultiplier)), 2, '0')) AS Color
		FROM			tmp_applicationtraffichitbyday
		GROUP BY		Label
		ORDER BY		ApplicationTrafficTime ASC;

	# Top script
		SELECT			ATr.ApplicationTrafficScript AS Label,
						COUNT(0) AS Hit,
						CONCAT('#', LPAD(HEX(@ColorValueMinimum + ROUND(RAND() * @ColorValueMultiplier)), 2, '0'), LPAD(HEX(@ColorValueMinimum + ROUND(RAND() * @ColorValueMultiplier)), 2, '0'), LPAD(HEX(@ColorValueMinimum + ROUND(RAND() * @ColorValueMultiplier)), 2, '0')) AS Color
		FROM			sphp_applicationtraffic AS ATr
		WHERE			{$TimeSelectSQLWHEREClause}
			AND			ATr.ApplicationTrafficScript NOT IN ('webapp/manifest')
		GROUP BY		Label
		ORDER BY		Hit DESC
		LIMIT			10;

	# Top user
		SELECT			CONCAT_WS(' ', U.UserNameFirst, U.UserNameMiddle, U.UserNameLast) AS Label,
						COUNT(0) AS Hit,
						CONCAT('#', LPAD(HEX(@ColorValueMinimum + ROUND(RAND() * @ColorValueMultiplier)), 2, '0'), LPAD(HEX(@ColorValueMinimum + ROUND(RAND() * @ColorValueMultiplier)), 2, '0'), LPAD(HEX(@ColorValueMinimum + ROUND(RAND() * @ColorValueMultiplier)), 2, '0')) AS Color
		FROM			sphp_applicationtraffic AS ATr
			LEFT JOIN	sphp_user AS U ON U.UserID = ATr.UserID
		WHERE			{$TimeSelectSQLWHEREClause}
			AND			U.UserID IS NOT NULL
			AND			U.UserEmail NOT IN ('Shahriar@SingularityBD.Com')
		GROUP BY		Label
		ORDER BY		Hit DESC
		LIMIT			10;

	# Status
		SELECT 'Complete' AS ProcessStatus;
"); //DebugDump("<pre>{$SQL}</pre>");

if(is_array($Recordset) && count($Recordset) >= 7){ //DebugDump($Recordset);
	$HitByLocation = $Recordset[0];
	$HitByHour = $Recordset[1];
	$HitByDay = $Recordset[2];
	$HitByMonth = $Recordset[3];
	$HitByYear = $Recordset[4];
	$HitByScript = $Recordset[5];
	$HitByUser = $Recordset[6];
}
else{
	$Error[] = "Database query failed!";
}

//DebugDump($Error);
?>

<style>
	.ChartRow > .ChartArea{float: left; border: 1px Silver solid; padding: 5px;}
	.ChartRow > .ChartArea > .Title{background-color: Black; padding: 5px; color: White; font-weight: bold; text-align: center; text-transform: uppercase;}
	.ChartRow:after{display: block; clear: both; content: '';}
	.ChartRow > .ChartArea > .Chart{height: 300px;}

	.ChartWidth_1_4{width: 25%;}
	.ChartWidth_2_4{width: 50%;}
	.ChartWidth_3_4{width: 75%;}
	.ChartWidth_4_4{width: 100%;}
</style>

<div class="ChartRow">
	<div class="ChartArea ChartWidth_1_4">
		<div class="Title">By location</div>
		<div class="Chart"><canvas id="Chart_HitByLocation"></canvas></div>
	</div>
	<div class="ChartArea ChartWidth_3_4">
		<div class="Title">Daily</div>
		<div class="Chart"><canvas id="Chart_HitByDay"></canvas></div>
	</div>
</div>

<div class="ChartRow">
	<div class="ChartArea ChartWidth_3_4">
		<div class="Title">Monthly</div>
		<div class="Chart"><canvas id="Chart_HitByMonth"></canvas></div>
	</div>
	<div class="ChartArea ChartWidth_1_4">
		<div class="Title">Yearly</div>
		<div class="Chart"><canvas id="Chart_HitByYear"></canvas></div>
	</div>
</div>

<div class="ChartRow">
	<div class="ChartArea ChartWidth_2_4">
		<div class="Title">Top script</div>
		<div class="Chart"><canvas id="Chart_HitByScript"></canvas></div>
	</div>
	<div class="ChartArea ChartWidth_2_4">
		<div class="Title">Top user</div>
		<div class="Chart"><canvas id="Chart_HitByUser"></canvas></div>
	</div>
</div>

<script>
	var Chart_HitByLocation = new Chart(document.getElementById('Chart_HitByLocation').getContext('2d'), {
		type: 'pie',
		data: {
			labels: ['<?=implode("', '", array_column($HitByLocation, "Label"))?>'],
			datasets: [
				{
					data: [<?=implode(", ", array_column($HitByLocation, "Hit"))?>],
					backgroundColor: ['<?=implode("', '", array_column($HitByLocation, "Color"))?>'],
				},
			]
		},
		options: {
			maintainAspectRatio: false,
			title: {display: false, text: 'Hit by location', },
			plugins: {
				datalabels: {
					//color: 'White', anchor: 'center', align: 'center',
					font: {weight: 'bold', },
				},
			},
		},
	});

	var Chart_HitByDay = new Chart(document.getElementById('Chart_HitByDay').getContext('2d'), {
		type: 'line',
		data: {
			labels: ['<?=implode("', '", array_column($HitByDay, "Label"))?>'],
			datasets: [
				{
					data: [<?=implode(", ", array_column($HitByDay, "Hit"))?>],
					label: 'Day', backgroundColor: 'rgba(127, 127, 127, 0.25)', borderColor: 'Black', borderWidth: 2,
				},
			]
		},
		options: {
			maintainAspectRatio: false,
			title: {display: false, text: 'Hit by day', },
			plugins: {
				datalabels: {
					color: 'Blue', anchor: 'end', align: 'top', offset: -6,
					font: {weight: 'bold', },
				},
			},
		},
	});

	var Chart_HitByMonth = new Chart(document.getElementById('Chart_HitByMonth').getContext('2d'), {
		type: 'bar',
		data: {
			labels: ['<?=implode("', '", array_column($HitByMonth, "Label"))?>'],
			datasets: [
				{
					data: [<?=implode(", ", array_column($HitByMonth, "Hit"))?>],
					label: "Month",
					backgroundColor: 'rgba(0, 0, 128, 0.25)',
					borderColor: 'Navy', borderWidth: 2,
				},
			]
		},
		options: {
			maintainAspectRatio: false,
			title: {display: false, text: 'Hit by month', },
			plugins: {
				datalabels: {
					color: 'Blue', anchor: 'end', align: 'top', offset: -6,
					font: {weight: 'bold', },
				},
			},
		},
	});

	var Chart_HitByYear = new Chart(document.getElementById('Chart_HitByYear').getContext('2d'), {
		type: 'bar',
		data: {
			labels: ['<?=implode("', '", array_column($HitByYear, "Label"))?>'],
			datasets: [
				{
					data: [<?=implode(", ", array_column($HitByYear, "Hit"))?>],
					label: "Year",
					backgroundColor: 'rgba(128, 0, 0, 0.25)',
					borderColor: 'Maroon', borderWidth: 2,
				},
			]
		},
		options: {
			maintainAspectRatio: false,
			title: {display: false, text: 'Hit by year', },
			plugins: {
				datalabels: {
					color: 'Blue', anchor: 'end', align: 'top', offset: -6,
					font: {weight: 'bold', },
				},
			},
		},
	});

	var Chart_HitByScript = new Chart(document.getElementById('Chart_HitByScript').getContext('2d'), {
		type: 'horizontalBar',
		data: {
			labels: ['<?=implode("', '", array_column($HitByScript, "Label"))?>'],
			datasets: [
				{
					data: [<?=implode(", ", array_column($HitByScript, "Hit"))?>],
					label: "Script",
					backgroundColor: 'rgba(255, 165, 0, 0.25)',
					borderColor: 'Orange', borderWidth: 2,
				},
			]
		},
		options: {
			maintainAspectRatio: false,
			title: {display: false, text: 'Hit by year', },
			plugins: {
				datalabels: {
					color: 'Blue', anchor: 'end', align: 'top', offset: -6,
					font: {weight: 'bold', },
				},
			},
		},
	});

	var Chart_HitByUser = new Chart(document.getElementById('Chart_HitByUser').getContext('2d'), {
		type: 'horizontalBar',
		data: {
			labels: ['<?=implode("', '", array_column($HitByUser, "Label"))?>'],
			datasets: [
				{
					data: [<?=implode(", ", array_column($HitByUser, "Hit"))?>],
					label: "User",
					backgroundColor: 'rgba(255, 0, 0, 0.25)',
					borderColor: 'Red', borderWidth: 2,
				},
			]
		},
		options: {
			maintainAspectRatio: false,
			title: {display: false, text: 'Hit by year', },
			plugins: {
				datalabels: {
					color: 'Blue', anchor: 'end', align: 'top', offset: -6,
					font: {weight: 'bold', },
				},
			},
		},
	});
</script>