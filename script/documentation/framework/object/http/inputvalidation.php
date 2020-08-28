<?php
	$Object = [
		"Namespace" => "HTTP",
		"Name" => "InputValidation",
		"Description" => "Define input validation rules and check for POST/GET input values accordingly.",
		"Property" =>	[
			[
				"Name" => "Name", "Read" => true, "Write" => true, "DataType" => "String", "Description" => "Name of the input (POST/GET) to be validated.",
				"Sample" => "FullName",
			],
			[
				"Name" => "Required", "Read" => true, "Write" => true, "DataType" => "Boolean", "Description" => "Is the input mandetory or not. TRUE = Mandetory; FALSE = Optional",
				"Sample" => "true",
			],
			[
				"Name" => "Type", "Read" => true, "Write" => true, "DataType" => "String", "Description" => "Type of data to be input. Use comma seperated type strings for multiple type validation.",
				"Sample" => "VALIDATION_TYPE_ALPHABETIC, VALIDATION_TYPE_EMAIL",
			],
			[
				"Name" => "Caption", "Read" => true, "Write" => true, "DataType" => "String", "Description" => "User friendly caption for the input field to be used in the message.",
				"Sample" => "Full name",
			],
			[
				"Name" => "Message", "Read" => true, "Write" => true, "DataType" => "String", "Description" => "Message to be shown when validation fails.",
				"Sample" => "Name must be alphabetic only.",
			],
		],
		"Method" => [
			[
				"Name" => "Validate",
				"Description" => "Checks if the input in the post form is valid according to Required and Type properties.",
				"Argument" => [],
				"Sample" => "",
			],
		],
		"Sample" => [
			[
				"Title" => "Validate form/URL POST/GET input/argument value;",
				"Script" => "ContactAction.php",
			],
		]
	];

	require "{$Environment->ScriptPath()}documentation/framework/template/object.php";
?>