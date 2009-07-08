<?php

class Blerby_Entity_Object_Visitor_Format_JSON extends Blerby_Entity_Object_Visitor
{
    public function __construct()
    {
          
    }
    
    public function visit($object)
    {
        return json_encode($object->toArray());
    }

    public function mimeType()
    {
        return "text/json";   
    }       
    
}