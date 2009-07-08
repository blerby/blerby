<?php

class Blerby_Core_Singleton implements Blerby_Core_Singleton_Interface
{
    public static function instance()
    {
        static $instance = null;

        if (!$instance)
        {
            $class = __CLASS__;
            $instance = new $class;
        }
        return $instance;
    }

    
}