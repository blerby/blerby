<?php

require_once "Core/Delegate/Exception.php";

class Blerby_Core_Delegate
{
    protected $delegationTarget = null;
    
    public function __construct($target = "", $params = array())
    {
        if (class_exists($target))
        {
            $this->delegationTarget = new $target($params); 
        }    
    }
    
    public function __call($method, $args)
    {
        if (is_object($this->delegationTarget) && method_exists($this->delegationTarget, $method))
        {
            return call_user_func_array(array($this->delegationTarget, $method), $args);
        }
        else
        {
            throw new Blerby_Core_Delegate_Exception("Delegate target does not implement {$method}");
        }
    }
    
    /**
     * Recursively test if the delegation target has a method
     * 
     * @param string $method
     * @return bool
     */
    public function hasTargetMethod($method)
    {
        return (method_exists($this->delegationTarget, $method) || $this->delegationTarget->hasTargetMethod($method)); 
    }
    
}