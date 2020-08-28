<?php
	$Object = [
		"Namespace" => "Comm",
		"Name" => "MailContact",
		"Description" => "Reference to a contact to be used with Email object as To, From, Cc, Bcc or ReplyTo.",
		"Property" =>	[
			[
				"Name" => "Address", "Required" => true, "Read" => true, "Write" => true, "DataType" => "String", "Description" => "Email address.",
				"Sample" => "JohnDoe@MyDomain.Com",
			],
			[
				"Name" => "Name", "Required" => false, "Read" => true, "Write" => true, "DataType" => "String", "Description" => "Human readable name for the email address.",
				"Sample" => "new MailContact(\"JohnDoe@MyDomain.Com\", \"John Doe\")",
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