<?php
namespace sPHP\HTML\UI\Menubar;

class Pad{
	private $Property = [
		"ID"			=> null, 
		"Item"			=> null, 
		"Children"		=> null, // Array of Pad objects
		"IconBaseURL"	=> null, 
		"ParentID"		=> null, 
		"Selected"		=> null, 
		"HTML"			=> null, 
	];

	public function __construct(?string $ID = null, ?object $Item = null, ?array $Children = null, ?string $IconBaseURL = null){
		foreach(explode(",", str_replace(" ", "", "ID, Item, Children, IconBaseURL")) as $Argument)if(!is_null($$Argument))$this->$Argument($$Argument);
	}

	public function ID(?string $Value = null){
		if(is_null($Value)){
			return $this->Property[__FUNCTION__];
		}
		else{
			$this->Property[__FUNCTION__] = $Value;
			$this->Property["HTML"] = null;
		}
	}

	public function Item(?object $Value = null){
		if(is_null($Value)){
			return $this->Property[__FUNCTION__];
		}
		else{
			$this->Property[__FUNCTION__] = $Value;
			$this->Property["HTML"] = null;
		}
	}

	public function Children(?array $Value = null){
		if(is_null($Value)){
			return $this->Property[__FUNCTION__];
		}
		else{
			$this->Property[__FUNCTION__] = $Value;
			$this->Property["HTML"] = null;
		}
	}

	public function IconBaseURL(?string $Value = null){
		if(is_null($Value)){
			return $this->Property[__FUNCTION__];
		}
		else{
			$this->Property[__FUNCTION__] = $Value;
			$this->Property["HTML"] = null;
		}
	}

	public function ParentID(?string $Value = null){
		if(is_null($Value)){
			return $this->Property[__FUNCTION__];
		}
		else{
			$this->Property[__FUNCTION__] = $Value;
			$this->Property["HTML"] = null;
		}
	}

	public function Selected(){
		if(is_null($this->Property[__FUNCTION__])){ //var_dump("'Menubar-{$this->Property["ParentID"]}-{$this->Property["ID"]}'");
			$this->Property[__FUNCTION__] = isset($_POST["Menubar-{$this->Property["ParentID"]}-{$this->Property["ID"]}"]) || isset($_GET["Menubar-{$this->Property["ParentID"]}-{$this->Property["ID"]}"]);			
			//if($this->Property[__FUNCTION__])var_dump("'Menubar-{$this->Property["ParentID"]}-{$this->Property["ID"]}' is selected");
		}
		
		return $this->Property[__FUNCTION__];
	}

	public function HTML(){
		if(is_null($this->Property[__FUNCTION__])){
			$this->Selected(); // Determine if pad is selected
			
			if(!$this->Property["Item"]->Caption())$this->Property["Item"]->Caption($this->Property["ID"]); //? Set ID as item caption if not provided with
			if($this->Property["Item"]->IconURL())$this->Property["Item"]->IconURL("{$this->Property["IconBaseURL"]}{$this->Property["Item"]->IconURL()}");
			$this->Property["Item"]->LabelFor("Menubar-{$this->Property["ParentID"]}-{$this->Property["ID"]}");

			$ChildrenHTML = [];

			if(is_array($this->Property["Children"])){
				$this->Property["Children"] = array_filter($this->Property["Children"]); //? Remove NULL items

				if(count($this->Property["Children"])){
					foreach($this->Property["Children"] as $Child){				
						if($Child == \sPHP\HTML\UI\Menubar::CHILD_TYPE_DEVIDER){
							$ChildrenHTML[] = "<div class=\"Devider\"></div>";
						}
						else{
							if(!$Child->IconBaseURL())$Child->IconBaseURL($this->Property["IconBaseURL"]);
							$Child->ParentID("{$this->Property["ParentID"]}-{$this->Property["ID"]}");
	
							$ChildrenHTML[] = $Child->HTML();
							$this->Property["Selected"] = $this->Property["Selected"] || $Child->Selected();
						}
					}
	
					//$this->Property["Item"]->URL(""); //? Forcefully disable URL hyperlink for item with children
				}
			}

			$this->Property[__FUNCTION__] = "
				<input type=\"radio\" name=\"Menubar-{$this->Property["ParentID"]}\" id=\"Menubar-{$this->Property["ParentID"]}-{$this->Property["ID"]}\"" . ($this->Property["Selected"] ? " checked" : null) . ">
				<div class=\"Pad\">
					{$this->Property["Item"]->HTML()}
					" . (count($ChildrenHTML) ? "<div class=\"ChildrenIndicator\"></div>" : null) . "
					<div class=\"Children\">
						" . implode("", $ChildrenHTML) . "
					</div>
				</div>
			";
		}

		return $this->Property[__FUNCTION__];
	}
}
?>