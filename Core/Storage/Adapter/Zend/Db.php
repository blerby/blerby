<?php



require_once "Core/Storage/Adapter/Abstract.php";
require_once "Core/Storage/Result.php";


require_once "Zend/Db.php";

class Blerby_Core_Storage_Adapter_Zend_Db extends Blerby_Core_Storage_Adapter_Abstract
{
    
    protected $db    = null;
    
    protected $model = null;
    
    public function __construct($options = array())
    {
        $this->db = Zend_Db::factory($options['adapter'], $options['params']);
    }

    function handle($cmd)
    {
        $result = new Blerby_Core_Storage_Result();
        
        $result->fromArray(array("some", "test", "data"));
        return $result;
    }
}