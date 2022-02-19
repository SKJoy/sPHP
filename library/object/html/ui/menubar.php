<?php
namespace sPHP\HTML\UI;

class Menubar{
	public const CHILD_TYPE_DEVIDER = "DEVIDER";
	public const STYLE_HORIZONTAL = "HORIZONTAL";
	public const STYLE_VERTICAL = "VERTICAL";
	public const STYLE_ACCORDION = "ACCORDION";

	private $Property = [
		"ID"			=> null, 
		"Pad"			=> null, // Array of MenubarPad objects
		"IconBaseURL"	=> null, 
		"Style"			=> self::STYLE_HORIZONTAL, // Vertical | Accordion
		"CSSSelector"	=> null, // Custom CSS selector; Must prefix with 'Menubar-'
		"HTML"			=> null, 
	];

	public function __construct(?string $ID = null, ?array $Pad = null, ?string $IconBaseURL = null, ?string $Style = null, ?string $CSSSelector = null){
		foreach(explode(",", str_replace(" ", "", "ID, Pad, IconBaseURL, Style, CSSSelector")) as $Argument)if(!is_null($$Argument))$this->$Argument($$Argument);
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
	
	public function Pad(?array $Value = null){
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

	public function Style(?string $Value = null){
		if(is_null($Value)){
			return $this->Property[__FUNCTION__];
		}
		else{
			$this->Property[__FUNCTION__] = $Value;
			$this->Property["HTML"] = null;
		}
	}

	public function CSSSelector(?string $Value = null){
		if(is_null($Value)){
			return $this->Property[__FUNCTION__];
		}
		else{
			$this->Property[__FUNCTION__] = $Value;
			$this->Property["HTML"] = null;
		}
	}

	public function HTML(){
		if(is_null($this->Property[__FUNCTION__])){
			$PadHTML = [];

			if(is_array($this->Property["Pad"]))foreach(array_filter($this->Property["Pad"]) as $Pad){ // Remove NULL items before looping to process
				if($Pad == Menubar::CHILD_TYPE_DEVIDER){
					$PadHTML[] = "<div class=\"Devider\"></div>";
				}
				else{
					if(!$Pad->IconBaseURL())$Pad->IconBaseURL($this->Property["IconBaseURL"]);
					$Pad->ParentID($this->Property["ID"]);

					$PadHTML[] = $Pad->HTML();
				}
			}

			$this->Property[__FUNCTION__] = "
				<div id=\"Menubar-{$this->Property["ID"]}\" class=\"Menubar" . ($this->Property["Style"] == self::STYLE_VERTICAL ? " Menubar-Vertical" : null) . "" . ($this->Property["Style"] == self::STYLE_ACCORDION ? " Menubar-Accordion" : null) . "" . ($this->Property["CSSSelector"] ? " {$this->Property["CSSSelector"]}" : null) . "\">
					" . implode("", $PadHTML) . "
				</div>
			";
		}

		return $this->Property[__FUNCTION__];
	}
}
?>