<?php
/*
    Name:           HTML UI Paginator
    Purpose:        User object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 12, 2018 10:51 AM
*/

namespace sPHP\HTML\UI;

class Paginator{
    #region Property variable
    private $Property = [
		"RecordCount"				=>	0,
		"RowPerPage"				=>	50,
		"PageCount"					=>	0,
		"URL"						=>	null,
        "CSSSelector"				=>	null,
        "ID"						=>	null,
		"CurrentPage"				=>	1,
        "NavigationHTML"			=>	null,
        "PageHTML"					=>	null,
        "HTML"						=>	null,
    ];
    #endregion Property variable

	#region Private variable
	private $ParameterPrefix = null;
	#endregion Private variable

    #region Method
    public function __construct($RecordCount = null, $RowPerPage = null, $PageCount = null, $URL = null, $CSSSelector = null, $ID = null){
		// Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

        return true;
    }
    #endregion Method

    #region Property
    public function RecordCount($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->SetPageCount();
			$this->ClearHTML();

            $Result = true;
        }

        return $Result;
    }

    public function RowPerPage($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->SetPageCount();
			$this->ClearHTML();

            $Result = true;
        }

        return $Result;
    }

    public function PageCount($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->SetCurrentPage();
			$this->ClearHTML();

            $Result = true;
        }

        return $Result;
    }

    public function URL($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->URL = "{$this->Property[__FUNCTION__]}" . (strpos($this->Property[__FUNCTION__], "?") === false ? "?" : "&") . "";
			$this->ClearHTML();

            $Result = true;
        }

        return $Result;
    }

    public function CSSSelector($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->ClearHTML();

            $Result = true;
        }

        return $Result;
    }

    public function ID($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->ParameterPrefix = $this->Property[__FUNCTION__] ? "Paginator_{$this->Property[__FUNCTION__]}_" : null;
			$this->SetCurrentPage();
			$this->ClearHTML();

            $Result = true;
        }

        return $Result;
    }

    public function CurrentPage(){
        $Result = $this->Property[__FUNCTION__];

		return $Result;
    }

	public function NavigationHTML(){
		if(is_null($this->Property[__FUNCTION__])){
			if($this->Property["CurrentPage"] > 1){
				$PageNavigationHTML[] = "<a href=\"{$this->URL}{$this->ParameterPrefix}Page=1\" class=\"Link\">&lt;&lt;</a>";
				$PageNavigationHTML[] = "<a href=\"{$this->URL}{$this->ParameterPrefix}Page=" . ($this->Property["CurrentPage"] - 1) . "\" class=\"Link\">&lt;</a>";
			}

			if($this->Property["CurrentPage"] < $this->Property["PageCount"]){
				$PageNavigationHTML[] = "<a href=\"{$this->URL}{$this->ParameterPrefix}Page=" . ($this->Property["CurrentPage"] + 1) . "\" class=\"Link\">&gt;</a>";
				$PageNavigationHTML[] = "<a href=\"{$this->URL}{$this->ParameterPrefix}Page={$this->Property["PageCount"]}\" class=\"Link\">&gt;&gt;</a>";
			}

			$this->Property[__FUNCTION__] = "" . implode(" ", isset($PageNavigationHTML) ? $PageNavigationHTML : []) . "";
		}

		$Result = $this->Property[__FUNCTION__];

		return $Result;
	}

	public function PageHTML(){
		if(is_null($this->Property[__FUNCTION__])){
			for($PageCounter = 1; $PageCounter <= $this->Property["PageCount"]; $PageCounter++)$PageHTML[] = "<a href=\"{$this->URL}{$this->ParameterPrefix}Page={$PageCounter}\" class=\"" . ($PageCounter == $this->Property["CurrentPage"] ? "Active" : null) . "Link\">{$PageCounter}</a>";

			$this->Property[__FUNCTION__] = "" . implode(" ", isset($PageHTML) ? $PageHTML : []) . "";
		}

		$Result = $this->Property[__FUNCTION__];

		return $Result;
	}

    public function HTML(){
		if(is_null($this->Property[__FUNCTION__])){
			$this->Property[__FUNCTION__] = "{$this->NavigationHTML()} {$this->PageHTML()}";
		}

		$Result = $this->Property[__FUNCTION__];

		return $Result;
    }
    #endregion Property

	#region Private function
	private function SetPageCount(){
		$Result = true;

		$this->PageCount(ceil($this->Property["RecordCount"] / ($this->Property["RowPerPage"] ? $this->Property["RowPerPage"] : 50)));

		return $Result;
	}

	private function SetCurrentPage(){
		$Result = true;

		$this->Property["CurrentPage"] = \sPHP\SetVariable("{$this->ParameterPrefix}Page", 1) > $this->Property["PageCount"] ? $this->Property["PageCount"] : intval($_POST["{$this->ParameterPrefix}Page"]);

		return $Result;
	}

	private function ClearHTML(){
		$Result = true;

		$this->Property["NavigationHTML"] = $this->Property["PageHTML"] = $this->Property["HTML"];

		return $Result;
	}
	#endregion Private function
}
?>