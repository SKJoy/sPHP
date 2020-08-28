<?php
	$Object = [
		"Namespace" => "Comm",
		"Name" => "MailAttachment",
		"Description" => "Reference to an attachment file to be sent via Email object",
		"Property" =>	[
			[
				"Name" => "File", "Required" => true, "Read" => true, "Write" => true, "DataType" => "String", "Description" => "File name with full path on the server.",
				"Sample" => "./file/MyFile.txt",
			],
			[
				"Name" => "Name", "Required" => false, "Read" => true, "Write" => true, "DataType" => "String", "Description" => "File name to suggest when client downloads it.",
				"Sample" => "YourDoc.log",
			],
			[
				"Name" => "MIMEType", "Required" => false, "Read" => true, "Write" => true, "DataType" => "String", "Description" => "Custom MIME type of the file when being downloaded by the client.",
				"Sample" => "CSV",
			],
		],
		"Method" => [],
		"Sample" => [
				[
					"Title" => "Send an HTML to multiple recipients;",
					"Script" => "SendEmail.php",
				],
				[
					"Title" => "Example with namespace;",
					"Script" => "SendEmail.php",
				],
			]
	];

	require "{$Environment->ScriptPath()}documentation/framework/template/object.php";
?>