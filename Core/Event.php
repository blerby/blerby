<?php

// TODO: Add context (string/object/etc)
// TODO: Add caller
class Blerby_Core_Event implements Blerby_Core_Event_Interface
{
    protected $type   = "unknown.type";
    
    protected $origin = null;  

    public function __construct($type, $origin = null)
    {
        $this->type = $type;
        $this->origin = $origin;
    }

    public function origin($origin = null)
    {
        if ($origin != null)
        {
            $this->origin = $origin;   
        }
        
        return $this->origin;
    }    

    public function type($type = null)
    {
        if ($type != null)
        {
            $this->type = $type;   
        }
        
        return $this->type;
    }

    // TODO: possibly create a call chain.
}