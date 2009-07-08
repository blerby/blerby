<?php
require_once "Core/Object/Interface.php";

class Blerby_Core_Object implements Blerby_Core_Object_Interface
{
    protected $store = array();
    
    public function __get($k)
    {
        return $this->get($k);    
    }
    
    public function get($k, $default = null)
    {
        if (!isset($this->$k))
        {
            return (isset($this->store[$k])) ?
                    $this->store[$k]         :
                    $default;
        }
        else
        {
            return $this->$k;   
        }   
    }
    
    public function __set($k, $v)
    {
        return $this->set($k, $v);
    }

    public function set($k, $v)
    {
        if (!isset($this->$k)) {
            $this->store[$k] = $v;    
        } else {
            $this->$k = $v;   
        }
        return $v;
    }
    
    public function clear()
    {
        $this->store = array();   
    }
    
    public function fromArray($array)
    {
        $this->store = array_merge($this->store, $array);   
    }
    
    public function toArray()
    {
        return $this->store;   
    }

    public function __toString()
    {
        return json_encode($this->toArray());   
    }
    
}
