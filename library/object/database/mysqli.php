<?php
namespace sPHP\Database;

class MySQLi{
    private $Property = [
        "Host" => null, 
        "User" => null, 
        "Password" => null, 
        "Database" => null, 
        "Error" => null, 
        "Connection" => false, 
    ];
    
    #region Method    
    public function __construct($Host = null, $User = null, $Password = null, $Database = null){
        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){
        $this->Disconnect();

        return true;
    }
    
    public function Connect(){
        $this->Property["Connection"] = new \mysqli($this->Property["Host"], $this->Property["User"], $this->Property["Password"], $this->Property["Database"]);
        
        if($this->Property["Connection"]->connect_errno){
            $this->Property["Error"] = $this->Property["Connection"]->connect_error;
            $this->Property["Connection"] = false;
            $Result = false;
        }
        else{
            $this->Property["Error"] = null;
            $Result = true;
        }
        

        return $Result;
    }
    
    public function Disconnect(){
        $this->Property["Error"] = null;
        $this->Property["Connection"]->close();
        $this->Property["Connection"] = false;

        return true;
    }

    public function Query($SQL){
        if($this->Property["Connection"] === false)$this->Connect();
        
        if($this->Property["Connection"] && $this->Property["Connection"]->multi_query($SQL)){
            $this->Property["Error"] = null;
            $Result = [];
            $RecordsetCount = -1;

            do{
                if($MySQLiRecordset = $this->Property["Connection"]->store_result()){
                    $RecordsetCount++;
                
                    while($MySQLiRow = $MySQLiRecordset->fetch_assoc()){
                        $Result[$RecordsetCount][] = $MySQLiRow;
                    }
                }
            }while($this->Property["Connection"]->more_results() && $this->Property["Connection"]->next_result());
            
            $Result = count($Result) ? $Result : true;
        }
        else{
            $this->Property["Error"] = "Unkown error";
            $Result = false;
        }

        return $Result;
    }
    #endregion Method
    
    #region Property
    public function Host($Value){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;
            
            $Result = true;
        }
        
        return $Result;
    }
    
    public function User($Value){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;
            
            $Result = true;
        }
        
        return $Result;
    }
    
    public function Password($Value){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;
            
            $Result = true;
        }
        
        return $Result;
    }
    
    public function Database($Value){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;
            
            $Result = true;
        }
        
        return $Result;
    }
    
    public function Error(){
        $Result = $this->Property[__FUNCTION__];
        
        return $Result;
    }
    
    public function Connection(){
        $Result = $this->Property[__FUNCTION__];
        
        return $Result;
    }
    #endregion Property
}
?>