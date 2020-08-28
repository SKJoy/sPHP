<?php
	namespace sPHP;

	require __DIR__ . "/../../common/languagebyhtmlcode.php";

	$Content = new Content($_POST["Name"], null, $Environment->ContentPath(), null, $Language[$_POST["LanguageHTMLCode"]]);

	if(is_array($Content->Value())){ // Array of content items
		$Input = explode(",", $_POST["Input"]);
		$ItemCounter = -1;
		$Field = explode("\n", $_POST["Field"]);

		foreach($Field as $Key){
			$ItemCounter++;
			$InputName = "Value[{$Key}]";
			if(!isset($Input[$ItemCounter]))$Input[$ItemCounter] = null;

			if($Input[$ItemCounter] == INPUT_TYPE_TEXT){
				$InputHTML = "" . HTML\UI\Input("{$InputName}", $Configuration["InputFullWidth"], $Content->Value()[$Key]) . "";
			}
			elseif($Input[$ItemCounter] == INPUT_TYPE_FILE){
				$InputHTML = "" . HTML\UI\Input("{$InputName}", $Configuration["InputFullWidth"], "{$Environment->ContentUploadURL()}{$Content->Value()[$Key]}", null, INPUT_TYPE_FILE) . "";
			}
			elseif($Input[$ItemCounter] == INPUT_TYPE_RICHTEXTAREA){
				$Textarea = new HTML\UI\Textarea("{$InputName}", $Configuration["InputFullWidth"], $Configuration["TextareaHeight"], $Content->Value()[$Key]);

				$InputHTML = "
					{$Textarea->HTML()}
					<script>sJS.MakeTinyMCETextarea('{$Textarea->ID()}');</script>
				";
			}
			else{
				$InputHTML = "" . HTML\UI\Textarea("{$InputName}", $Configuration["InputFullWidth"], $Configuration["TextareaHeight"], $Content->Value()[$Key]) . "";
			}

			$ValueInput[] = "" . HTML\UI\Field($InputHTML, "{$Key}", true, null, $Configuration["FieldCaptionWidth"], $Configuration["FieldContentFullWidth"]) . "";
		}
	}
	elseif(!is_object($Content->Value()) && !is_resource($Content->Value())){ // Single content
		if($_POST["Input"] == INPUT_TYPE_TEXT){
			$InputHTML = "" . HTML\UI\Input("Value", $Configuration["InputFullWidth"], $Content->Value()) . "";
		}
		elseif($_POST["Input"] == INPUT_TYPE_FILE){
			$InputHTML = "" . HTML\UI\Input("Value", $Configuration["InputFullWidth"], "{$Environment->ContentUploadURL()}{$Content->Value()}", null, INPUT_TYPE_FILE) . "";
		}
		elseif($_POST["Input"] == INPUT_TYPE_RICHTEXTAREA){
			$Textarea = new HTML\UI\Textarea("Value", $Configuration["InputFullWidth"], $Configuration["TextareaHeight"], $Content->Value());

			$InputHTML = "
				{$Textarea->HTML()}
				<script>sJS.MakeTinyMCETextarea('{$Textarea->ID()}');</script>
			";
		}
		else{
			$InputHTML = "" . HTML\UI\Textarea("Value", $Configuration["InputFullWidth"], $Configuration["TextareaHeight"], $Content->Value()) . "";
		}

		$ValueInput[] = "" . HTML\UI\Field($InputHTML, "Value", true, null, $Configuration["FieldCaptionWidth"], $Configuration["FieldContentFullWidth"]) . "";
	}
	else{
		$ValueInput[] = "This type of content is not supported for editing!";
	}

	print "" . HTML\UI\Form(
		$Application->URL("Utility/Content/Action"), // Action URL
		// Content
		"
			" . HTML\UI\Field(HTML\UI\Input("" . ($Caption = "Name") . "", $Configuration["InputFullWidth"], $Content->Name(), true), "{$Caption}*", null, null, $Configuration["FieldCaptionWidth"], $Configuration["FieldContentFullWidth"]) . "
			" . HTML\UI\Field(HTML\UI\Input("" . ($Caption = "Language") . "HTMLCode", $Configuration["InputFullWidth"], $Content->Language()->HTMLCode(), true), "{$Caption}*", true, null, $Configuration["FieldCaptionWidth"], $Configuration["FieldContentFullWidth"]) . "
			" . implode(null, $ValueInput) . "
			" . HTML\UI\Input("Input", null, null, null, INPUT_TYPE_HIDDEN) . "
			" . HTML\UI\Input("Field", null, null, null, INPUT_TYPE_HIDDEN) . "
			" . HTML\UI\Input("AnchorID", null, null, null, INPUT_TYPE_HIDDEN) . "
		",
		"Set", // Submit button caption
		$Application->EncryptionKey(), // Signature modifier
		"Edit content", // Title
		"Use the form below to edit the content to desired values.", // Header
		"Each language will have seperate impact on the values you save.", // Footer
		"*Required field", // Status
		"frmContact" // ID
	) . "";
?>