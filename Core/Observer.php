<?php
/**
 * 
 * 
 * @package    Core
 * @subpackage Observer
 * @author     Elijah Insua <tmpvar@gmail.com>
 */


require_once "Core/Observer/Interface.php";
require_once "Core/Observable/Interface.php";

/**
 * Concrete observer class
 */
class Blerby_Core_Observer implements Blerby_Core_Observer_Interface
{
    /**
     * Basic update functionality 
     * 
     * @param  Blerby_Core_Observable_Interface $source
     * @param  array                     $extra
     * @return mixed
     */
    public function update(Blerby_Core_Observable_Interface $source = null, $extra = array())
    {
        // implementation specific
        return true;
    }
}