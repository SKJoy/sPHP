<?php
namespace sPHP\HTML\UI\Menubar\Pad;

class Item{
	private $Property = [
		"Caption"	=> null, 
		"URL"		=> null, 
		"IconURL"	=> null, 
		"OnClick"	=> null, 
		"URLTarget"	=> null, 
		"LabelFor"	=> null, 
		"HTML"		=> null, 
	];

	public function __construct(?string $Caption = null, ?string $URL = null, ?string $IconURL = null, ?string $OnClick = null, ?string $URLTarget = null){
		foreach(explode(",", str_replace(" ", "", "Caption, URL, IconURL, OnClick, URLTarget")) as $Argument)if(!is_null($$Argument))$this->$Argument($$Argument);
	}

	public function Caption(?string $Value = null){
		if(is_null($Value)){
			return $this->Property[__FUNCTION__];
		}
		else{
			$this->Property[__FUNCTION__] = $Value;
			$this->Property["HTML"] = null;
		}
	}

	public function URL(?string $Value = null){
		if(is_null($Value)){
			return $this->Property[__FUNCTION__];
		}
		else{
			$this->Property[__FUNCTION__] = $Value;
			$this->Property["HTML"] = null;
		}
	}

	public function IconURL(?string $Value = null){
		if(is_null($Value)){
			return $this->Property[__FUNCTION__];
		}
		else{
			$this->Property[__FUNCTION__] = $Value;
			$this->Property["HTML"] = null;
		}
	}

	public function OnClick(?string $Value = null){
		if(is_null($Value)){
			return $this->Property[__FUNCTION__];
		}
		else{
			$this->Property[__FUNCTION__] = $Value;
			$this->Property["HTML"] = null;
		}
	}

	public function URLTarget(?string $Value = null){
		if(is_null($Value)){
			return $this->Property[__FUNCTION__];
		}
		else{
			$this->Property[__FUNCTION__] = $Value;
			$this->Property["HTML"] = null;
		}
	}

	public function LabelFor(?string $Value = null){
		if(is_null($Value)){
			return $this->Property[__FUNCTION__];
		}
		else{
			$this->Property[__FUNCTION__] = $Value;
			$this->Property["HTML"] = null;
		}
	}

	public function HTML(){ //var_dump($this->Property["IconURL"]);
		if(is_null($this->Property[__FUNCTION__])){
			$Element = $this->Property["URL"] ? "a" : "label";

			$this->Property[__FUNCTION__] = "
				<{$Element}" . ($this->Property["URL"] ? " href=\"{$this->Property["URL"]}\"" : " for=\"{$this->Property["LabelFor"]}\"") . ($this->Property["URLTarget"] ? " target=\"{$this->Property["URLTarget"]}\"" : null) . ($this->Property["OnClick"] ? " onclick=\"{$this->Property["OnClick"]}\"" : null) . " class=\"Title\">" . ($this->Property["IconURL"] ? "<img src=\"{$this->Property["IconURL"]}\" alt=\"" . basename($this->Property["IconURL"]) . "\" class=\"Icon\">" : null) . "<span class=\"Caption\">{$this->Property["Caption"]}</span></$Element>
			";
		}

		return $this->Property[__FUNCTION__];
	}
}
?>