<?php
	namespace sPHP;

	$DocumentationHTML[] = HTML\UI\DropdownMenu([
		new HTML\UI\DropdownMenuItem("Object", null, new HTML\UI\DropdownMenuItemPad([
			new HTML\UI\DropdownMenuItem("Convention", "#"),
			new HTML\UI\DropdownMenuItem(),
			new HTML\UI\DropdownMenuItem("HTML", null, new HTML\UI\DropdownMenuItemPad([
				new HTML\UI\DropdownMenuItem("UI", null, new HTML\UI\DropdownMenuItemPad([
					new HTML\UI\DropdownMenuItem($ObjectName = "Input", $Application->URL($_POST["_Script"], "Topic=Object/HTML/UI/{$ObjectName}")),
					new HTML\UI\DropdownMenuItem($ObjectName = "Textarea", $Application->URL($_POST["_Script"], "Topic=Object/HTML/UI/{$ObjectName}")),
					new HTML\UI\DropdownMenuItem($ObjectName = "Select", $Application->URL($_POST["_Script"], "Topic=Object/HTML/UI/{$ObjectName}")),
					new HTML\UI\DropdownMenuItem($ObjectName = "Radio", $Application->URL($_POST["_Script"], "Topic=Object/HTML/UI/{$ObjectName}")),
					new HTML\UI\DropdownMenuItem(),
					new HTML\UI\DropdownMenuItem($ObjectName = "Field", $Application->URL($_POST["_Script"], "Topic=Object/HTML/UI/{$ObjectName}")),
					new HTML\UI\DropdownMenuItem($ObjectName = "Form", $Application->URL($_POST["_Script"], "Topic=Object/HTML/UI/{$ObjectName}")),
				])),
			])),
			new HTML\UI\DropdownMenuItem("HTTP", null, new HTML\UI\DropdownMenuItemPad([
				new HTML\UI\DropdownMenuItem($ObjectName = "InputValidation", $Application->URL($_POST["_Script"], "Topic=Object/HTTP/{$ObjectName}")),
			])),
			new HTML\UI\DropdownMenuItem("COMM", null, new HTML\UI\DropdownMenuItemPad([
				new HTML\UI\DropdownMenuItem($ObjectName = "MailContact", $Application->URL($_POST["_Script"], "Topic=Object/Comm/{$ObjectName}")),
				new HTML\UI\DropdownMenuItem($ObjectName = "MailAttachment", $Application->URL($_POST["_Script"], "Topic=Object/Comm/{$ObjectName}")),
				new HTML\UI\DropdownMenuItem($ObjectName = "MailHeader", $Application->URL($_POST["_Script"], "Topic=Object/Comm/{$ObjectName}")),
				new HTML\UI\DropdownMenuItem($ObjectName = "Mail", $Application->URL($_POST["_Script"], "Topic=Object/Comm/{$ObjectName}")),
				new HTML\UI\DropdownMenuItem(),
				new HTML\UI\DropdownMenuItem($ObjectName = "SMS", $Application->URL($_POST["_Script"], "Topic=Object/Comm/{$ObjectName}")),
			])),
		])),
		new HTML\UI\DropdownMenuItem(),
		new HTML\UI\DropdownMenuItem("Function"),
	], "DropdownMenuHorizontal");

	//require __DIR__ . "/" . strtolower(SetVariable("Topic", "Object/HTML/UI/Form")) . ".php";
	require __DIR__ . "/" . strtolower(SetVariable("Topic", "Overview")) . ".php";
?><style>
	html > body > main{max-width: none; background-image: none;}

	.TableTitle{text-align: left;}

	.Documentation{background-color: White; font-size: 18px; font-family: Consolas; line-height: 1.62;}
	.Documentation .Section{margin-top: 17px; padding-top: 17px;}
	.Documentation .Section > .Title{position: absolute; top: -17px; box-shadow: 0 0 5px 0 Black; border-radius: 5px; border: 2px White solid; background-color: Maroon; padding: 5px; padding-left: 15px; padding-right: 15px; color: White; font-weight: bold; font-style: italic; line-height: 1;}
	.Documentation .SectionShortcut{float: right; z-index: 1; line-height: 1;}
	.Documentation .SectionShortcut > a{border: 1px Blue solid; padding: 5px; color: Blue;}
	.Documentation .SectionShortcut > a:hover{border-color: Black; background-color: Black; color: White; text-decoration: none;}
	.Documentation .SectionShortcut > a > .Icon{margin-right: 5px; height: 15px; border-radius: 50%; border: 1px Black solid; padding: 1px; vertical-align: middle;}
	.Documentation table > thead{line-height: 1;}
	.Documentation table > thead > tr > th > .Icon{margin-right: 5px; height: 15px; border-radius: 50%; border: 1px White solid; background-color: White; vertical-align: middle;}
	.Documentation table > thead > tr > th > .SectionShortcut > a{border-width: 0; padding: 0; color: White;}
	.Documentation table > thead > tr > th > .SectionShortcut > a:hover{background-color: Transparent; color: Cyan;}
	.Documentation table > tbody > tr > td{vertical-align: top;}
	.Documentation table > tbody > tr > .Code{border-color: Silver; background-color: Black; color: White; line-height: 1.62;}
	.Documentation table > tbody > tr > .Code > .Default{border-bottom: 1px Yellow solid; color: Yellow;}
	.Documentation > .Object{margin-top: 15px;}
	.Documentation > .Object > .Name{display: inline-block; box-shadow: 0 0 5px 0 Grey; border-radius: 5px; background-color: White; padding: 15px; padding-left: 30px; padding-right: 30px; line-height: 1;}
	.Documentation > .Object > .Name > .Title{float: left; margin-right: 5px; font-weight: bold;}
	.Documentation > .Object > .Name > .Title > .Icon{margin-right: 5px; height: 22px; border-radius: 50%; border: 1px Black solid; padding: 1px; vertical-align: middle;}
	.Documentation > .Object > .Name > .Namespace{float: left; margin-right: 5px; font-style: italic;}
	.Documentation > .Object > .Name > .Caption{float: left; color: Blue; font-size: 125%; font-weight: bold;}
	.Documentation > .Object > .Description{margin-top: 15px; margin-left: 30px; border-left: 1px Silver solid; padding-left: 15px;}
	.Documentation > .Object > .Property{margin-top: 15px;}
	.Documentation > .Object > .Property > thead > tr:first-child > th{background-color: LightSeaGreen;}
	.Documentation > .Object > .Property > tbody > tr > td > .Icon{height: 16px; vertical-align: middle;}
	.Documentation > .Object > .Method{margin-top: 15px;}
	.Documentation > .Object > .Method > thead > tr:first-child > th{background-color: Orange;}
	.Documentation > .Object > .Method > tbody > tr > td > .Argument{margin: -5px; border-width: 0;}
	.Documentation > .Object > .Method > tbody > tr > td > .Argument > thead > tr:first-child > th{background-color: BlueViolet;}
	.Documentation > .Object > .Sample{margin-top: 15px; border: 1px Black solid;}
	.Documentation > .Object > .Sample > .Title{background-color: Silver; padding: 5px; line-height: 1;}
	.Documentation > .Object > .Sample > .Title > .Icon{margin-right: 5px; height: 15px; border-radius: 50%; border: 1px Black solid; padding: 1px; vertical-align: middle;}
	.Documentation > .Object > .Sample > .Title > .Script{margin-right: 15px; color: Blue; font-style: italic;}
	.Documentation > .Object > .Sample > .Title > .SectionShortcut{font-weight: bold; text-shadow: 1px 1px Black;}
	.Documentation > .Object > .Sample > .Title > .SectionShortcut > a{border-width: 0; padding: 0; color: White;}
	.Documentation > .Object > .Sample > .Title > .SectionShortcut > a:hover{background-color: Transparent; color: Cyan;}
	.Documentation > .Object > .Sample > .Code{background-color: Black; padding: 5px; color: White; line-height: 1.62;}
</style>

<div class="Documentation"><?=implode(null, $DocumentationHTML)?></div>