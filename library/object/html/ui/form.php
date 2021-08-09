<?php
/*
    Name:           Form
    Purpose:        Form object
    Author:         Broken Arrow (SKJoy2001@GMail.Com)
    Created:  		May, 30, 2018 05:30 PM
    Date modified:  Mon, 30 Jul 2018 17:54:00 GMT+06:00
*/

namespace sPHP\HTML\UI;

class Form{
    private $Property = [
        "Action"					=>	null,
        "Content"					=>	null,
		"SubmitCaption"				=>	"Post",
        "SignatureModifier"			=>	null,
        "Title"						=>	null,
        "Header"					=>	null,
        "Footer"					=>	null,
        "Status"					=>	null,
        "ID"						=>	null,
		"Reset"						=>	true,
        "ButtonContent"				=>	null,
		"EventHandlerJavaScript"	=>	[],
		"InputValidation"			=>	[],
		"CSSSelector"				=>	null,
		"ErrorMessage"				=>	null,
        "BeginHTML"					=>	null,
        "EndHTML"					=>	null,
        "HTML"						=>	null,
    ];

    #region Variable
    //private $Buffer = [];
    #endregion Variable

    #region Method
    public function __construct($Action = null, $Content = null, $SubmitCaption = null, $SignatureModifier = null, $Title = null, $Header = null, $Footer = null, $Status = null, $ID = null, $Reset = null, $ButtonContent = null, $EventHandlerJavaScript = null, $InputValidation = null, $CSSSelector = null){
		$this->Property["ID"] = "Form_" . \sPHP\GUID();

        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){
		return true;
    }

	public function Authenticate($SignatureModifier = null){
		#region Make sure we have the authentication variables
		\sPHP\SetVariable("_Signature");
		\sPHP\SetVariable("_Time");
		\sPHP\SetVariable("_FormScript");
		\sPHP\SetVariable("_GUID");
		#endregion Make sure we have the authentication variables
		
		$Result = $_POST["_Signature"] == md5("{$_POST["_Time"]}_{$_POST["_FormScript"]}_{$_POST["_GUID"]}_" . ($SignatureModifier ? $SignatureModifier : $this->Property["SignatureModifier"]) . "");
		if(!$Result)$this->ErrorMessage("Form verification failed!");

		return $Result;
	}

	public function ValidateInput(){
		$Result = true; //var_dump($this->Property["InputValidation"]);

		foreach(array_filter($this->Property["InputValidation"]) as $Validation){
			if(!$Validation->Validate()){
				$Result = false;
				$this->ErrorMessage($Validation->Message());
				break;
			}
		}

		return $Result;
	}

    public function Verify($SignatureModifier = null){
		$Result = $this->Authenticate($SignatureModifier);
		if($Result)$Result = $this->ValidateInput();

		return $Result;
    }
    #endregion Method

    #region Property
    public function Action($Value = null){
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

    public function Content($Value = null){
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

    public function SubmitCaption($Value = null){
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

    public function SignatureModifier($Value = null){
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

    public function Title($Value = null){
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

    public function Header($Value = null){
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

    public function Footer($Value = null){
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

    public function Status($Value = null){
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

			$this->ClearHTML();

            $Result = true;
        }

        return $Result;
    }

    public function Reset($Value = null){
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

    public function ButtonContent($Value = null){
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

    public function EventHandlerJavaScript($Value = null){
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

    public function InputValidation($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

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

    public function ErrorMessage($Value = null){
		if(is_null($Value)){
            $Result = isset($_POST["FormError_{$this->Property["ID"]}"]) ? $_POST["FormError_{$this->Property["ID"]}"] : null;
        }
        else{
            $_POST["FormError_{$this->Property["ID"]}"] = $Value;

            $Result = true;
        }

        return $Result;
    }

	public function BeginHTML(){
		if(is_null($this->Property[__FUNCTION__])){
			foreach(array_filter(is_array($this->Property["EventHandlerJavaScript"]) ? $this->Property["EventHandlerJavaScript"] : ["OnSubmit" => $this->Property["EventHandlerJavaScript"], ]) as $Event => $Handler)$EventHandler[] = "{$Event}=\"{$Handler}\"";

			$this->Property[__FUNCTION__] = "
				<form id=\"{$this->Property["ID"]}\" name=\"{$this->Property["ID"]}\" action=\"{$this->Property["Action"]}\" method=\"POST\" enctype=\"multipart/form-data\"" . ($this->Property["CSSSelector"] ? " class=\"{$this->Property["CSSSelector"]}\"" : null) . (isset($EventHandler) ? " " . implode(" ", $EventHandler) . " " : null) . ">
					" . ($this->Property["Title"] ? "<div class=\"Title\">{$this->Property["Title"]}</div>" : null) . "
					" . (\sPHP\SetVariable("FormError_{$this->Property["ID"]}") ? "<div class=\"Error\">{$_POST["FormError_{$this->Property["ID"]}"]}</div>" : null) . "
					" . ($this->Property["Header"] ? "<div class=\"Header\">{$this->Property["Header"]}</div>" : null) . "
					<div class=\"Content\">
			";
		}

        $Result = $this->Property[__FUNCTION__];

        return $Result;
	}

	public function EndHTML(){
		if(is_null($this->Property[__FUNCTION__])){
			if(is_array($this->Property["EventHandlerJavaScript"]))foreach(array_filter($this->Property["EventHandlerJavaScript"]) as $Handler)if(is_array($Handler) && isset($Handler[1]))$EventHandler[] = "{$Handler[0]}=\"{$Handler[1]}\"";

			if($this->Property["ButtonContent"] || $this->Property["SubmitCaption"] || $this->Property["Reset"]){
				$ButtonHTML[] = "<div class=\"ButtonArea\">";
				if($this->Property["Reset"])$ButtonHTML[] = Button("Reset", \sPHP\BUTTON_TYPE_RESET) . "\n";
				if($this->Property["SubmitCaption"])$ButtonHTML[] = Button($this->Property["SubmitCaption"], \sPHP\BUTTON_TYPE_SUBMIT);
				if($this->Property["ButtonContent"])$ButtonHTML[] = $this->Property["ButtonContent"];
				$ButtonHTML[] = "</div>";
			}

			$this->Property[__FUNCTION__] = "
					</div>
					" . ($this->Property["Footer"] ? "<div class=\"Footer\">{$this->Property["Footer"]}</div>" : null) . "
					" . (isset($ButtonHTML) ? implode(null, $ButtonHTML) : null) . "
					" . ($this->Property["Status"] ? "<div class=\"Status\">{$this->Property["Status"]}</div>" : null) . "
					<input type=\"hidden\" name=\"_ID\" value=\"{$this->Property["ID"]}\">
					<input type=\"hidden\" name=\"_Referer\" value=\"" . \sPHP\SetVariable("_Referer", $_SERVER["HTTP_REFERER"]) . "\">
					<input type=\"hidden\" name=\"_Time\" value=\"" . ($FormTime = date("r")) . "\">
					<input type=\"hidden\" name=\"_FormScript\" value=\"" . \sPHP\SetVariable("_FormScript", $_POST["_Script"]) . "\">
					<input type=\"hidden\" name=\"_GUID\" value=\"" . \sPHP\SetVariable("_GUID", \sPHP\GUID()) . "\">
					<input type=\"hidden\" name=\"_Signature\" value=\"" . md5("{$FormTime}_{$_POST["_FormScript"]}_{$_POST["_GUID"]}_{$this->Property["SignatureModifier"]}") . "\">
				</form>
			";
		}

        $Result = $this->Property[__FUNCTION__];

        return $Result;
	}

    public function HTML(){
		if(is_null($this->Property[__FUNCTION__]))$this->Property[__FUNCTION__] = $this->BeginHTML() . (is_array($this->Property["Content"]) ? implode(null, $this->Property["Content"]) : $this->Property["Content"]) . $this->EndHTML();

        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }
    #endregion Property

	#region Function
	private function ClearHTML(){
		$this->Property["BeginHTML"] = $this->Property["EndHTML"] = $this->Property["HTML"] = null;

		return true;
	}
	#endregion Function
}
?>