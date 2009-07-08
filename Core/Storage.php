<?php

require_once "Core/Factory/Static/Interface.php";

class Blerby_Core_Storage implements Blerby_Core_Factory_Static_Interface
{
    /**
     * 
     * @throws Blerby_Core_Storage_Exception
     */
    public static function factory($name, $options = array())
    {
        @include str_replace("_", "/", $name) . ".php";
        
        if (class_exists($name))
        {
            return new $name($options);   
        }
        else
        {
            throw new Blerby_Core_Storage_Exception("Class {$name} not found");   
        }
    }
    
}
