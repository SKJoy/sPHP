<?php
	$Object = [
		"Namespace" => "Comm",
		"Name" => "Mail",
		"Description" => "Send email with custom SMTP support.",
		"Property" =>	[
			[
				"Name" => "To", "Required" => true, "Read" => true, "Write" => true, "DataType" => "MailContact object [array]", "Description" => "An array of MailContact objects each representing a recipient of the email.",
				"Sample" => "new MailContact(\"JohnDoe@MyDomain.Com\", \"John Doe\")",
			],
			[
				"Name" => "Subject", "Required" => true, "Read" => true, "Write" => true, "DataType" => "String", "Description" => "Subject line of the email.",
				"Sample" => "Some email subject",
			],
			[
				"Name" => "Message", "Required" => false, "Read" => true, "Write" => true, "DataType" => "String", "Description" => "Email body. You can use HTML formatted content for HTML email.",
				"Sample" => "<p>Some email message</p>
<p>With another paragraph.</p>",
			],
			[
				"Name" => "From", "Required" => false, "Read" => true, "Write" => true, "DataType" => "MailContact object", "Description" => "A MailContact object rferring to the sender information of the email. Some mail servers require you to explicitely set this for sending out emails.",
				"Sample" => "new MailContact(\"Info@MyDomain.Com\", \"Information Desk\")",
			],
			[
				"Name" => "BodyStyle", "Required" => false, "Read" => true, "Write" => true, "DataType" => "String", "Description" => "CSS style for the body tag for an HTML email.",
				"Sample" => "border: 1px Blue solid; padding: 15px;",
			],
			[
				"Name" => "LogPath", "Required" => false, "Read" => true, "Write" => true, "DataType" => "String", "Description" => "Path on the server to store a copy of the email sent in EML format.",
				"Sample" => "./custom/log/path/",
				"Default" => "./log/mail/sent/",
			],
			[
				"Name" => "Cc", "Required" => false, "Read" => true, "Write" => true, "DataType" => "MailContact object [array]", "Description" => "An array of MailContact objects each representing a carbon copy (CC) recipient of the email.",
				"Sample" => "new MailContact(\"SomeOne@MyDomain.Com\", \"Some One\")",
			],
			[
				"Name" => "Bcc", "Required" => false, "Read" => true, "Write" => true, "DataType" => "MailContact object [array]", "Description" => "An array of MailContact objects each representing a blind carbon copy (BCC) recipient of the email.",
				"Sample" => "new MailContact(\"SomeBody@MyDomain.Com\", \"Some Body\")",
			],
			[
				"Name" => "Attachment", "Required" => false, "Read" => true, "Write" => true, "DataType" => "MailAttachment object [array]", "Description" => "An array of MailAttachment objects each specifying an attached document for the email.",
				"Sample" => "new MailAttachement(\"./files/myfile.doc\", \"MyFile.Doc\")",
			],
			[
				"Name" => "ReplyTo", "Required" => false, "Read" => true, "Write" => true, "DataType" => "MailContact object", "Description" => "A MailContact object referring to the detail of the recipient of the reply email.",
				"Sample" => "new MailContact(\"JaneDoe@MyDomain.Com\", \"Jane Doe\")",
			],
			[
				"Name" => "HTML", "Required" => false, "Read" => true, "Write" => true, "DataType" => "Boolean", "Description" => "Whether to send the email in HTML format or plain text; TRUE = HTML or FALSE = plain text.",
				"Sample" => "false",
				"Default" => "true",
			],
			[
				"Name" => "Header", "Required" => false, "Read" => true, "Write" => true, "DataType" => "MailHeader object [array]", "Description" => "An array of MailHeader objects each referring to a custom email header.",
				"Sample" => "new MailHeader(\"Header-name\", \"Header value\")",
			],
			[
				"Name" => "Host", "Required" => false, "Read" => true, "Write" => true, "DataType" => "Integer", "Description" => "Custom SMTP host.",
				"Sample" => "MyCustomMail.Host",
			],
			[
				"Name" => "Port", "Required" => false, "Read" => true, "Write" => true, "DataType" => "String", "Description" => "Custom SMTP port.",
				"Sample" => "26",
				"Default" => "25",
			],
			[
				"Name" => "User", "Required" => false, "Read" => true, "Write" => true, "DataType" => "String", "Description" => "Authentication username for custom SMTP host.",
				"Sample" => "info@mycustommail.host",
			],
			[
				"Name" => "Password", "Required" => false, "Read" => true, "Write" => true, "DataType" => "String", "Description" => "Authentication password for custom SMTP host.",
				"Sample" => "MyCustomMailHostPassword",
			],
		],
		"Method" => [
			[
				"Name" => "Send",
				"Description" => "Send out the email.",
				"Argument" => [],
				"Sample" => "",
			],
		],
		"Sample" => [
				[
					"Title" => "Send an HTML to multiple recipients;",
					"Script" => "SendEmail.php",
				],
			]
	];

	require "{$Environment->ScriptPath()}documentation/framework/template/object.php";
?>