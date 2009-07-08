<?php
/**
 * Entity Core
 * 
 * This project is a wrapper for all things definable.
 * Using an extremely flexible infrastructure developers
 * are able to quickly bring their application up to speed
 * with minimal effort.
 * 
 * @author Elijah Insua <tmpvar@gmail.com>
 * @copyright Copyright &copy; 2009, Elijah Insua 
 */

require_once "Blerby/Entity/Object.php";

require_once "Blerby/Entity/Model/Type.php";
require_once "Blerby/Entity/Model/Component/Config.php";


/**
 * Temporary storage for Entity data
 */
class Blerby_Entity_Core extends Blerby_Entity_Object
{
    public function __construct()
    {
        
        
    }

    public static function instance(array $options = array())
    {
        static $instance = null;

        if (!$instance)
        {
            $class = __CLASS__;
            $instance = new $class;
            $instance->fromArray($options);
        }
        return $instance;
    }

    // TODO: Make new repositories accessable

    // TODO: Make this use Core_Storage + Filesystem_Storage (code)
    public function components($filter = "")
    {
        $items = glob(dirname(__FILE__) . "/Component/*", GLOB_ONLYDIR);

        $ret = array();
        
        foreach ($items as $item)
        {
            $aItem = explode("/", str_replace("\\", "/", $item));

            $ret[$aItem[count($aItem)-1]] = $item;
        }

        return $ret;
    }
    
    public function component($uri)
    {
        // locate the repo to use
        
        
     
     
     
        
    }
    
    public function types()
    {
        $typeTable = new Blerby_Entity_Model_Type();
        $types = $typeTable->fetchAll();
        $ret = array();
        foreach ($types as $type)
        {
            $ret[$type->id] = $type;
        }
        return $ret;
    }

}