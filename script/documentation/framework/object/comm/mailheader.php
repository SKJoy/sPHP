<?php
	$Object = [
		"Namespace" => "Comm",
		"Name" => "MailHeader",
		"Description" => "Custom header for an email through Mail object.",
		"Property" =>	[
			[
				"Name" => "Name", "Required" => true, "Read" => true, "Write" => true, "DataType" => "String", "Description" => "Header name.",
				"Sample" => "X-Mailer",
			],
			[
				"Name" => "Data", "Required" => false, "Read" => true, "Write" => true, "DataType" => "String", "Description" => "Header value.",
				"Sample" => "My Application/1.0",
			],
		],
		"Method" => [],
		"Sample" => [
				[
					"Title" => "Send email with custom mailer signature",
					"Script" => "SendEmail.php",
				],
			]
	];

	require "{$Environment->ScriptPath()}documentation/framework/template/object.php";
?>