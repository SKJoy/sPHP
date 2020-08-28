<?php
namespace sPHP\HTML;

class Meta{
    private $Property = [
        "Name" => null, 
        "Content" => null, 
        "HTTPEquivalent" => null, 
        "Property" => null, 
        "CharacterSet" => null, 
        "HTML" => null, 
    ];
    
    #region Method
    public function __construct($Name = null, $Content = null, $HTTPEquivalent = null, $Property = null, $CharacterSet = null){
        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

        return true;
    }
    #endregion Method
    
    #region Property
    public function Name($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;
            
            $this->Property["HTML"] = null;
            
            $Result = $this->Property[__FUNCTION__];
        }
        
        return $Result;
    }
    
    public function Content($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;
            
            $this->Property["HTML"] = null;
            
            $Result = $this->Property[__FUNCTION__];
        }
        
        return $Result;
    }
    
    public function HTTPEquivalent($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;
            
            $this->Property["HTML"] = null;
            
            $Result = $this->Property[__FUNCTION__];
        }
        
        return $Result;
    }
    
    public function Property($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;
            
            $this->Property["HTML"] = null;
            
            $Result = $this->Property[__FUNCTION__];
        }
        
        return $Result;
    }
    
    public function CharacterSet($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;
            
            $this->Property["HTML"] = null;
            
            $Result = $this->Property[__FUNCTION__];
        }
        
        return $Result;
    }
    
    public function HTML(){
        if(is_null($this->Property[__FUNCTION__])){
            $Attribute = [];
            
            if($this->Property["Name"])$Attribute[] = "name=\"{$this->Property["Name"]}\"";
            if($this->Property["Property"])$Attribute[] = "property=\"{$this->Property["Property"]}\"";
            if($this->Property["HTTPEquivalent"])$Attribute[] = "http-equiv=\"{$this->Property["HTTPEquivalent"]}\"";
            if($this->Property["Content"])$Attribute[] = "content=\"{$this->Property["Content"]}\"";
            if($this->Property["CharacterSet"])$Attribute[] = "charset=\"{$this->Property["CharacterSet"]}\"";
            
            if(count($Attribute))$this->Property[__FUNCTION__] = "<meta " . implode(" ", $Attribute) . ">";
        }
        
        return $this->Property[__FUNCTION__];
    }
    #endregion Property
}
?>