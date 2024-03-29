<?php
	namespace sPHP;

	require __DIR__ . "/../../common/languagebyhtmlcode.php";
	$Content = new Content($_POST["Name"], null, $Environment->ContentPath(), null, $Language[$_POST["LanguageHTMLCode"]]);
	//var_dump($_FILES, $Content->Value()); exit;
	Upload($Environment->ContentUploadPath());

	foreach($_FILES as $Field=>$FieldValue)if($Field == "Value"){
		if(is_array($FieldValue)){
			foreach(array_keys($FieldValue["name"]) as $Item){
				$ExistingFile = isset($Content->Value()[$Item]) ? $Content->Value()[$Item] : null;

				if(isset($_POST["__DeleteExistingFile_{$Field}"][$Item]) || $_POST[$Field][$Item]){
					if($ExistingFile && file_exists($ExistingFile = "{$Environment->ContentUploadPath()}{$ExistingFile}"))unlink($ExistingFile);
				}
				else{
					$_POST[$Field][$Item] = $ExistingFile;
				}
			}
		}
		else{
			$ExistingFile = $Content->Value() ? $Content->Value() : null;

			if(isset($_POST["__DeleteExistingFile_{$Field}"]) || $_POST[$Field]){
				if($ExistingFile && file_exists($ExistingFile = "{$Environment->ContentUploadPath()}{$ExistingFile}"))unlink($ExistingFile);
			}
			else{
				$_POST[$Field] = $ExistingFile;
			}
		}
	}

	if($Content->Value($_POST["Value"])){
		print HTML\UI\MessageBox("Content updated successfully.", "Content management");

		if(isset($_POST["NewWindow"])){
			print "
				<script>
					alert('Content updated successfully.');
					window.close();
				</script>
			";
		}
		else{
			$Terminal->Redirect("{$_POST["_Referer"]}#{$_POST["AnchorID"]}");
		}
			
		//require "{$Environment->ScriptPath()}{$_POST["_FormScript"]}.php";
	}
	else{
		print HTML\UI\MessageBox("Content couldn't be updated!", "Content management", "MessageBox_Error");
	}
?>