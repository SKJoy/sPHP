<?php
namespace sPHP;

class OpenGraph{
    private $Property = [
        "URL" => null, 
        "Title" => null, 
        "Description" => null, 
        "Image" => null, 
        "ImageTitle" => null, 
        "UpdateTime" => null, 
        "Type" => "website", 
        "Locale" => "en_US", 
        "Meta" => null, 
        "MetaHTML" => null, 
    ];
    
    #region Method
    public function __construct($URL = null, $Title = null, $Description = null, $Image = null, $ImageTitle = null, $UpdateTime = null, $Type = null, $Locale = null){
        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

        return true;
    }
    #endregion Method
    
    #region Property
    public function URL($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;
            
            $this->Property["MetaTag"] = null;
            $this->Property["Meta"] = null;
            
            $Result = $this->Property[__FUNCTION__];
        }
        
        return $Result;
    }
    
    public function Title($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;
            
            $this->Property["MetaTag"] = null;
            $this->Property["Meta"] = null;
            
            $Result = $this->Property[__FUNCTION__];
        }
        
        return $Result;
    }
    
    public function Description($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;
            
            $this->Property["MetaTag"] = null;
            $this->Property["Meta"] = null;
            
            $Result = $this->Property[__FUNCTION__];
        }
        
        return $Result;
    }
    
    public function Image($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;
            
            $this->Property["MetaTag"] = null;
            $this->Property["Meta"] = null;
            
            $Result = $this->Property[__FUNCTION__];
        }
        
        return $Result;
    }
    
    public function ImageTitle($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;
            
            $this->Property["MetaTag"] = null;
            $this->Property["Meta"] = null;
            
            $Result = $this->Property[__FUNCTION__];
        }
        
        return $Result;
    }
    
    public function UpdateTime($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;
            
            $this->Property["MetaTag"] = null;
            $this->Property["Meta"] = null;
            
            $Result = $this->Property[__FUNCTION__];
        }
        
        return $Result;
    }
    
    public function Type($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;
            
            $this->Property["MetaTag"] = null;
            $this->Property["Meta"] = null;
            
            $Result = $this->Property[__FUNCTION__];
        }
        
        return $Result;
    }
    
    public function Locale($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;
            
            $this->Property[__FUNCTION__] = str_replace("-", "_", $this->Property[__FUNCTION__]);
            
            $this->Property["MetaTag"] = null;
            $this->Property["Meta"] = null;
            
            $Result = $this->Property[__FUNCTION__];
        }
        
        return $Result;
    }
    
    public function Meta($Property = null){
        if(is_null($this->Property[__FUNCTION__]))$this->Property[__FUNCTION__] = [
            ($PropertyName = "URL") => new HTML\Meta(null, $this->Property[$PropertyName], null, "og:" . strtolower($PropertyName) . ""), 
            ($PropertyName = "Title") => new HTML\Meta(null, $this->Property[$PropertyName], null, "og:" . strtolower($PropertyName) . ""), 
            ($PropertyName = "Description") => new HTML\Meta(null, $this->Property[$PropertyName], null, "og:" . strtolower($PropertyName) . ""), 
            ($PropertyName = "Image") => new HTML\Meta(null, $this->Property[$PropertyName], null, "og:" . strtolower($PropertyName) . ""), 
            ($PropertyName = "ImageTitle") => new HTML\Meta(null, $this->Property[$PropertyName], null, "og:image:alt"), 
            ($PropertyName = "UpdateTime") => new HTML\Meta(null, $this->Property[$PropertyName], null, "og:updated_time"), 
            ($PropertyName = "Type") => new HTML\Meta(null, $this->Property[$PropertyName], null, "og:" . strtolower($PropertyName) . ""), 
            ($PropertyName = "Locale") => new HTML\Meta(null, $this->Property[$PropertyName], null, "og:" . strtolower($PropertyName) . ""), 
        ];
        
        return $Property ? $this->Property[__FUNCTION__][$Property] : $this->Property[__FUNCTION__];
    }
    
    public function MetaHTML($Property = null){
        if(is_null($this->Property["Meta"]))$this->Meta();

        if($Property){
            $this->Property[__FUNCTION__] = $this->Property["Meta"][$Property]->HTML();
        }
        else{
            foreach($this->Property["Meta"] as $Meta)$HTML[] = $Meta->HTML();
            $this->Property[__FUNCTION__] = implode("", $HTML);
        }
        
        return $this->Property[__FUNCTION__];
    }
    #endregion Property
}
?>