<?php
/**
 * Generic Wrapper around Zend_Db_Table
 * 
 * 
 * @author  Elijah Insua <tmpvar@gmail.com>
 * @package Blerby_Application_Db
 */

require_once "Zend/Db/Table.php";

class Blerby_Application_Db_Table extends Zend_Db_Table
{
    /**
     * Constructor
     * 
     * @param array $options = array()
     * @return null
     */
    public function __construct(array $options = array())
    {
        // ** Automatically setup the metadata cache ONLY if not set **
        if (!isset($options['metadataCache']))
        {
            $options['metadataCache'] = Zend_Registry::get('cache');
        }
        
        // ** Continue normally **
        parent::__construct($options);
    }

    /**
     * Get primary key (helper function)
     * 
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->_primary;
    }

    /**
     * Delegate to get the current date time from the adapter
     * 
     * @access public
     * 
     * @param Zend_Date $date
     * @return string
     */
    public function getDateTime(Zend_Date $date)
    {
        return $this->getAdapter()->getDateTime($date);
    }    

    /**
     * Get extended representation of an item
     * 
     * @throws Exception not implemented
     * 
     * @return null
     */
    public function getExtended()
    {
        throw new Exception("Get extended not implemented for " . array_pop(explode("_", get_class($this))) );   
    }
}