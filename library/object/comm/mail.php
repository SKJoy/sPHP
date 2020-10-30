<?php
/*
    Name:           Mail
    Purpose:        Mail object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  Sat, 28 Jul 2018 20:12:25 GMT+06:00
*/

namespace sPHP\Comm;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/../../3rdparty/phpmailer/Exception.php";
require __DIR__ . "/../../3rdparty/phpmailer/PHPMailer.php";
require __DIR__ . "/../../3rdparty/phpmailer/SMTP.php";

class Mail{
    #region Property
    private $Property = [
        "To"				=>	[], // Array of MailContact object
        "Subject"			=>	null,
        "Message"			=>	null,
        "From"				=>	[], // MailContact object
        "BodyStyle"			=>	null,
        "LogPath"			=>	"./NO_LOG_PATH_log/mail/",
        "Cc"				=>	[], // Array of MailContact object
        "Bcc"				=>	[], // Array of MailContact object
        "Attachment"		=>	[],
        "ReplyTo"			=>	[], // MailContact object
		"HTML"				=>	true,
        "Header"			=>	[],
        "Host"				=>	null,
        "Port"				=>	25,
        "User"				=>	null,
        "Password"			=>	null,
    ];
    #endregion Property

    #region Variable
    //private $Utility = null;
    #endregion Variable

    #region Method
    public function __construct($To = null, $Subject = null, $Message = null, $From = null, $BodyStyle = null, $LogPath = null, $Cc = null, $Bcc = null, $Attachment = null, $ReplyTo = null, $HTML = null, $Header = null, $Host = null, $Port = null, $User = null, $Password = null){
		//$this->Utility = new Utility;

        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){
		return true;
    }

    public function Send($To = null, $Subject = null, $Message = null, $From = null, $BodyStyle = null, $LogPath = null, $Cc = null, $Bcc = null, $Attachment = null, $ReplyTo = null, $HTML = null, $Header = null, $Host = null, $Port = null, $User = null, $Password = null){
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

		$PHPMailer = new PHPMailer(true);

		foreach(is_array($this->Property["To"]) ? $this->Property["To"] : [$this->Property["To"]] as $To){
			try{
				$PHPMailer->addAddress($To->Address(), $To->Name());
			}
			catch(Exception $Exception){
				// Do nothing
			}
		}

		if($this->Property["From"]){
			try{
				$PHPMailer->setFrom($this->Property["From"]->Address(), $this->Property["From"]->Name());
			}
			catch(Exception $Exception){
				// Do nothing
			}
		}

		$PHPMailer->Subject = $this->Property["Subject"];
		$PHPMailer->Body = $Message = $this->Property["HTML"] ? "<html><body" . ($this->Property["BodyStyle"] ? " style=\"{$this->Property["BodyStyle"]}\"" : "") . ">{$this->Property["Message"]}</body></html>" : $this->Property["Message"];
		if($this->Property["ReplyTo"])$PHPMailer->AddReplyTo($this->Property["ReplyTo"]->Address(), $this->Property["ReplyTo"]->Name());
		if($this->Property["HTML"])$PHPMailer->IsHTML(true);
		$PHPMailer->CharSet = 'UTF-8';

		if($this->Property["Host"]){
			$PHPMailer->IsSMTP(true);
			$PHPMailer->Host = $this->Property["Host"];
			if($this->Property["Port"])$PHPMailer->Port = $this->Property["Port"];
		}

		if($this->Property["User"]){ // Require SMTP authentication
			$PHPMailer->SMTPAuth = true;
			$PHPMailer->Username = $this->Property["User"];
			if($this->Property["Password"])$PHPMailer->Password = $this->Property["Password"];
		}
        //var_dump($PHPMailer, realpath($this->Property["LogPath"]));
		try{
			if(($Result = $PHPMailer->send()) && realpath($this->Property["LogPath"]) !== false)file_put_contents("{$this->Property["LogPath"]}" . str_replace(str_split(" :"), "_", date("r")) . "_" . \sPHP\GUID() . ".eml", "Subject: {$this->Property["Subject"]}
To: White Services <info@white.services>
Date: " . date("r") . "
From: {$this->Property["From"]->Name()} <{$this->Property["From"]->Address()}>
MIME-Version: 1.0
Content-Type: text/html;charset=utf-8
xContent-Type: text/html; charset=iso-8859-1

{$Message}");
		}
		catch(Exception $Exception){
            //\sPHP\DebugDump($Exception);
			print \sPHP\HTML\UI\MessageBox("Failed sending email! Please contact support.", "Error", "MessageBox_Error");
			$Result = false;
		}

		return $Result;
    }
    #endregion Method

    #region Property
    public function To($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Subject($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Message($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function From($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function BodyStyle($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function LogPath($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Cc($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Bcc($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Attachment($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function ReplyTo($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function HTML($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

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

            $Result = true;
        }

        return $Result;
    }

    public function Host($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Port($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function User($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Password($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }
    #endregion Property
}
?>