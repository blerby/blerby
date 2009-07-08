<?php

require_once "Core/Object.php";
require_once "Core/Storage/Command/Interface.php";

class Blerby_Core_Storage_Command_Abstract extends Blerby_Core_Object implements Blerby_Core_Storage_Command_Interface
{
    protected $adapter;

    protected $context;
    
    public function __construct(Blerby_Core_Storage_Adapter_Interface $adapter)
    {
        $this->adapter = $adapter;
    }
    
    /**
     * 
     * @return Blerby_Core_Storage_Result
     */
    public function execute()
    {
        // simple dependency injection, yo.
        return $this->adapter->handle($this);        
    }
    
    public function context($name)
    {
        $this->set("context", $name);   
    }
    
       
}