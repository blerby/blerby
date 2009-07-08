<?php

require_once "Blerby/Entity/Object/Visitor.php";

class Blerby_Entity_Object_Visitor_Format_HTML extends Blerby_Entity_Object_Visitor
{
    public function __construct()
    {
          
    }
    
    public function visit($object)
    {
        // TODO: find the appropriate template to use.
        
        
    }
    
    public function mimeType()
    {
        return "text/html";   
    }
       
    
}