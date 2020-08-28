<?php
	$Object = [
		"Namespace" => "HTML\\UI",
		"Name" => "Form",
		"Description" => "Object to generate HTML form element and validate input(s) submitted.",
		"Property" =>	[
			[
				"Name" => "Action", "Read" => true, "Write" => true, "DataType" => "String", "Description" => "URL where the form should be submitted.",
				"Sample" => "http://MyDomain/?_Script=ActionScript",
			],
			[
				"Name" => "Content", "Read" => true, "Write" => true, "DataType" => "String", "Description" => "Content of the form that will appear/reside inside the HTML form tags.",
				"Sample" => "Name <input type=\"text\" name=\"FullName\" />
<button type=\"submit\">Post</button>",
			],
			[
				"Name" => "SubmitCaption", "Read" => true, "Write" => true, "DataType" => "String", "Description" => "Caption for the submit button.",
				"Sample" => "Post data",
			],
			[
				"Name" => "SignatureModifier", "Read" => true, "Write" => true, "DataType" => "String", "Description" => "Custom private signature string to more secure the form's authenticity signature for origin and data verification.",
				"Sample" => "My encryption key",
			],
			[
				"Name" => "Title", "Read" => true, "Write" => true, "DataType" => "String", "Description" => "Title of the form as appears on top.",
				"Sample" => "Contact us",
			],
			[
				"Name" => "Header", "Read" => true, "Write" => true, "DataType" => "String", "Description" => "Text as the form header as appears on top of the form contents.",
				"Sample" => "Fill in the field(s) below.",
			],
			[
				"Name" => "Footer", "Read" => true, "Write" => true, "DataType" => "String", "Description" => "Text as the form footer as appears beneath the form contents.",
				"Sample" => "We will reply you shortly.",
			],
			[
				"Name" => "Status", "Read" => true, "Write" => true, "DataType" => "String", "Description" => "Text to be used as the status line for the form as appears at the very bottom.",
				"Sample" => "*Required field(s)",
			],
			[
				"Name" => "ID", "Read" => true, "Write" => true, "DataType" => "String", "Description" => "A name for the form to uniquely identify or reference it.",
				"Sample" => "frmContact",
			],
			[
				"Name" => "Reset", "Read" => true, "Write" => true, "DataType" => "Boolean", "Description" => "Whether or not to show the reset button.",
				"Sample" => "true",
			],
			[
				"Name" => "ButtonContent", "Read" => true, "Write" => true, "DataType" => "String", "Description" => "HTML code for the form action buttons and will appear beneath the form contents.",
				"Sample" => "<button type=\"reset\">Reset</button>
<button type=\"submit\">Post</button>",
			],
			[
				"Name" => "EventHandlerJavaScript", "Read" => true, "Write" => true, "DataType" => "Array", "Description" => "Array with each element being handler for each event. For each event, another nested array with Key being the HTML event name and Value being the JavaScript event handler routine.",
				"Sample" => "[
	[\"OnClick\" => \"alert('Submit button is clicked!');\"],
	[\"OnSubmit\" => \"alert('Form is being post.');\"],
]",
			],
			[
				"Name" => "InputValidation", "Read" => true, "Write" => true, "DataType" => "Array of InputValidation object", "Description" => "Inputs will be validated according to the InputValidation object definitions used.",
				"Sample" => "[
	new InputValidation(\"Name\", true, VALIDATION_TYPE_ALPHABETIC),
	new InputValidation(\"Email\", true, VALIDATION_TYPE_EMAIL),
	new InputValidation(\"Comment\", true),
]",
			],
			[
				"Name" => "CSSSelector", "Read" => true, "Write" => true, "DataType" => "String", "Description" => "CSS selector (class) for the HTML form tag.",
				"Sample" => "MyForm",
			],
			[
				"Name" => "ErrorMessage", "Read" => true, "Write" => true, "DataType" => "String", "Description" => "Message with error style to display with the form. This message is automatically generated upon each call to form's Authenticate & Verify methods.",
				"Sample" => "Sorry, the email address is not valid!",
			],
			[
				"Name" => "BeginHTML", "Read" => true, "Write" => false, "DataType" => "String", "Description" => "Upper HTML section for the form's content. Use this to encapsulate freely coded HTML content within the form.",
				"Sample" => "",
			],
			[
				"Name" => "EndHTML", "Read" => true, "Write" => false, "DataType" => "String", "Description" => "Lower HTML section for the form's content. Use this to encapsulate freely coded HTML content within the form.",
				"Sample" => "",
			],
			[
				"Name" => "HTML", "Read" => true, "Write" => false, "DataType" => "String", "Description" => "Entire HTML for the form. Use this to show the form on the user interface.",
				"Sample" => "",
			],
		],
		"Method" => [
			[
				"Name" => "Authenticate",
				"Description" => "Checks if the form submitted is originated from or crafted by the legitimate source, in other words, checks for submission forgery.",
				"Argument" => [
					[
						"Name" => "SignatureModifier",
						"Description" => "Generates more strong signature with your custom signature.",
						"DataType" => "String",
						"Sample" => "My encryption key",
					],
				],
				"Sample" => "\$MyForm->Authenticate(\"My encryption key\")",
			],
			[
				"Name" => "ValidateInput",
				"Description" => "Checks and validates form inputs and generates automatic or custom error messages.",
				"Argument" => [],
				"Sample" => "",
			],
			[
				"Name" => "Verify",
				"Description" => "Combinedly performs the actions for Authenticate and ValidateInput methods.",
				"Argument" => [
					[
						"Name" => "SignatureModifier",
						"Description" => "Generates more strong signature with your custom signature.",
						"DataType" => "String",
						"Sample" => "My encryption key",
					],
				],
				"Sample" => "\$MyForm->Verify(\"My encryption key\")",
			],
		],
		"Sample" => [
				[
					"Title" => "Build a contact form and show for user input;",
					"Script" => "Contact.php",
				],
				[
					"Title" => "Verify form's source and validate input(s) on the action script where the form is post;",
					"Script" => "ContactAction.php",
				],
				[
					"Title" => "Separately check form's source and validate input(s) on the action script where the form is post;",
					"Script" => "ContactAction.php",
				],
			]
	];

	require "{$Environment->SystemScriptPath()}documentation/framework/template/object.php";
?>