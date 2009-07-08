<?php

require_once "Core/Object.php";

class Blerby_Core_Storage_Result extends Blerby_Core_Object
{
    
    public function __construct()
    {
    }
    
    // TODO: Conversion to other formats.
    
    public function render()
    {
  
        // TODO: Seriously? this needs to be done in a view or something that doesnt 'really'
        //       need to know what is going on.
        
        $result = "<ul>";
        foreach ($this->results as $v)
        {
            $result .= "<li><a href=''>{$v['ApplicationName']}</a></li>";
        }
        return $result . "</ul>";
        
    }
       
    
}