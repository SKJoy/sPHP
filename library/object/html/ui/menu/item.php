<?php
/*
    Name:           HTML UI Menu Item
    Purpose:        HTML UI Menu Item object
    Author:         Broken Arrow (SKJoy2001@GMail.Com)
    Created:  		May, 30, 2018 05:30 PM
    Date modified:  Mon, 30 Jul 2018 17:40:00 GMT+06:00
*/

namespace sPHP\HTML\UI;

class Field{
    #region Property variable
    private $Property = [
        "Caption"					=>	null,
        "URL"					    =>	null,
		"Icon"					    =>	null,
		"Children"					=>	[],
		"JavaScript"				=>	null,
		"IconBaseURL"				=>	null,
        "ItemID"					=>	null,
        "PadID"						=>	null,
        "HTML"						=>	null,
    ];
    #endregion Property variable

    #region Method
    public function __construct($Caption = null, $URL = null, $Icon = null, $Children = null, $JavaScript = null, $IconBaseURL = null, $ItemID = null, $PadID = null){
        // Set default propery values
        //$this->Property["ContentLanguage"] = new \sPHP\Language();
		$this->Property["ItemID"] = "" . \sPHP\GUID() . "";

		// Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

        return true;
    }
    #endregion Method

    #region Property
    public function Caption($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["HTML"] = null;

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

			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function Icon($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function Children($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function JavaScript($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function IconBaseURL($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function ItemID($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function PadID($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function HTML(){
		if(is_null($this->Property[__FUNCTION__])){
            $ChildrenPadID = \sPHP\GUID();

            foreach($this->Property["Children"] as $Child){
                if(!$Child->PadID())$Child->PadID($ChildrenPadID);
                if(!$Child->IconBaseURL())$Child->IconBaseURL($this->Property["IconBaseURL"]);

                $ChildHTML[] = $Child->HTML();
            }

            $this->Property[__FUNCTION__] = "
                <input type=\"radio\" name=\"HTML_UI_Menu_Switch_{$this->Property["PadID"]}\" id=\"HTML_UI_Menu_Switch_{$this->Property["ItemID"]}\">
                <div class=\"Item\">
                    <label for=\"HTML_UI_Menu_Switch_{$this->Property["ItemID"]}\" class=\"Caption\">
                        <a href=\"{$this->Property["URL"]}\" class=\"Container\" onclick=\"{$this->Property["JavaScript"]}\">
                            <img src=\"" . ($this->Property["IconBaseURL"] && strtoupper(substr($this->Property["Icon"], 0, 7)) != "HTTP://" && strtoupper(substr($this->Property["Icon"], 0, 8)) != "HTTPS://" ? $this->Property["IconBaseURL"] : null) . "{$this->Property["Icon"]}\" alt=\"Icon\" class=\"Icon\">
                            <span class=\"Conent\">{$this->Property["Caption"]}</span>
                        </a>
                    </label>

                    <div class=\"Pad\">" . implode("", $ChildHTML) . "</div>
                    <div class=\"Notch\"></div>
                </div>
            ";
		}

		$Result = $this->Property[__FUNCTION__];

		return $Result;
    }
    #endregion Property
}
?>