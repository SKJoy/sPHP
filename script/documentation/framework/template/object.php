<?php
namespace sPHP;

$DocumentationHTML[] = "<div class=\"Object\">";
$LocationAnchorName = str_replace("\\", "_", "sPHP\\{$Object["Namespace"]}\\{$Object["Name"]}");

$SectionShortcutHTML = "
	<div class=\"SectionShortcut\">
		<a href=\"#Object_{$LocationAnchorName}_Property\"><img src=\"{$Environment->IconURL()}objectproperty.png\" alt=\"Property\" class=\"Icon\">Property</a>
		<a href=\"#Object_{$LocationAnchorName}_Method\"><img src=\"{$Environment->IconURL()}objectmethod.png\" alt=\"Method\" class=\"Icon\">Method</a>
		<a href=\"#Object_{$LocationAnchorName}_Sample\"><img src=\"{$Environment->IconURL()}htmlcode.png\" alt=\"Code\" class=\"Icon\">Code</a>
	</div>
";

$DocumentationHTML[] = "
	<div class=\"Name\">
		<div class=\"Title\"><img src=\"{$Environment->IconURL()}object.png\" alt=\"Object\" class=\"Icon\">Name:</div>
		<div class=\"Namespace\" title=\"Namespace of the object\">sPHP\\{$Object["Namespace"]}\\</div>
		<div class=\"Caption\" title=\"Object name\">{$Object["Name"]}</div>
	</div>

	{$SectionShortcutHTML}
	<div class=\"Description\">{$Object["Description"]}</div>
";

$PropertyHTML = [];

foreach($Object["Property"] as $Property){
	$PropertyHTML[] = "
		<tr>
			<td>
				{$Property["Name"]}
				" . (isset($Property["Required"]) && $Property["Required"] ? "<img src=\"{$Environment->IconURL()}required.png\" alt=\"Required\" title=\"Required\" class=\"Icon\">" : null) . "
				" . (isset($Property["Read"]) && $Property["Read"] ? "<img src=\"{$Environment->IconURL()}read.png\" alt=\"Read\" title=\"Read\" class=\"Icon\">" : null) . "
				" . (isset($Property["Write"]) && $Property["Write"] ? "<img src=\"{$Environment->IconURL()}write.png\" alt=\"Write\" title=\"Write\" class=\"Icon\">" : null) . "
			</td>

			<td>{$Property["DataType"]}</td>
			<td class=\"Code\">
				" . (isset($Property["Default"]) && $Property["Default"] ? "<pre class=\"Default\">" . htmlspecialchars($Property["Default"]) . "</pre>" : null) . "
				<pre>" . htmlspecialchars($Property["Sample"]) . "</pre>
			</td>
			<td>{$Property["Description"]}</td>
		</tr>
	";
}

$MethodHTML = [];

foreach($Object["Method"] as $Method){
	$ArgumentHTML = [];

	foreach($Method["Argument"] as $Argument){
		$ArgumentHTML[] = "
			<tr>
				<td>{$Argument["Name"]}</td>
				<td>{$Argument["DataType"]}</td>
				<td class=\"Code\"><pre>" . str_replace(str_split("<>\""), ["&lt;", "&gt;", "&quot;"], $Argument["Sample"]) . "</pre></td>
				<td>{$Argument["Description"]}</td>
			</tr>
		";
	}

	$MethodHTML[] = "
		<tr>
			<td>{$Method["Name"]}</td>

			<td>" . (count($ArgumentHTML) ? "
				<table class=\"Argument\">
					<thead>
						<tr>
							<th>Name</th>
							<th>Type</th>
							<th>Sample</th>
							<th>Description</th>
						</tr>
					</thead>

					<tbody>" . implode(null, $ArgumentHTML) . "</tbody>
				</table>" : null) . "
			</td>

			<!--<td class=\"Code\"><pre>" . str_replace(str_split("<>\""), ["&lt;", "&gt;", "&quot;"], $Method["Sample"]) . "</pre></td>-->
			<td>{$Method["Description"]}</td>
		</tr>
	";
}

$DocumentationHTML[] = "
	<table id=\"Object_{$LocationAnchorName}_Property\" class=\"Property\">
		<thead>
			<tr class=\"TableTitle\">
				<th colspan=\"4\">
					<img src=\"{$Environment->IconURL()}objectproperty.png\" alt=\"Property\" class=\"Icon\">Property
					{$SectionShortcutHTML}
				</th>
			</tr>

			<tr>
				<th>Name</th>
				<th>Type</th>
				<th>Sample</th>
				<th>Description</th>
			</tr>
		</thead>

		<tbody>" . implode(null, $PropertyHTML) . "</tbody>
	</table>

	" . (count($MethodHTML) ? "<table id=\"Object_{$LocationAnchorName}_Method\" class=\"Method\">
		<thead>
			<tr class=\"TableTitle\">
				<th colspan=\"4\">
					<img src=\"{$Environment->IconURL()}objectmethod.png\" alt=\"Method\" class=\"Icon\">Method
					{$SectionShortcutHTML}
				</th>
			</tr>

			<tr>
				<th>Name</th>
				<th>Argument</th>
				<!--<th>Sample</th>-->
				<th>Description</th>
			</tr>
		</thead>

		<tbody>" . implode(null, $MethodHTML) . "</tbody>
	</table>" : null) . "

	<a id=\"Object_{$LocationAnchorName}_Sample\" class=\"LocationAnchor\"></a>
";

foreach($Object["Sample"] as $Key=>$Sample){
	$DocumentationHTML[] = "
		<div class=\"Sample\">
			<div class=\"Title\">
				<img src=\"{$Environment->IconURL()}htmlcode.png\" alt=\"Code\" class=\"Icon\">" . ($Sample["Script"] ? "<span class=\"Script\">{$Sample["Script"]}</span>" : null) . "{$Sample["Title"]}
				{$SectionShortcutHTML}
			</div>

			<div class=\"Code\"><pre>" . htmlspecialchars(file_get_contents("{$Environment->SystemScriptPath()}documentation/framework/code/sample/object/" . strtolower("{$Object["Namespace"]}/{$Object["Name"]}") . "/" . ($Key+1) . ".txt")) . "</pre></div>
		</div>
	";
}

$DocumentationHTML[] = "</div>";
?>