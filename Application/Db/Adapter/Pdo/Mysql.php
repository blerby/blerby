<?php
/**
 * Blerby's Extension of a mysql dbo adapter for performing pdo_mysql specific
 * operations
 * 
 * @author Elijah Insua <tmpvar@gmail.com>
 * @package Blerby_Application_Db_Adapter_Pdo
 */

class Blerby_Application_Db_Adapter_Pdo_Mysql extends Zend_Db_Adapter_Pdo_Mysql
{
    /**
     * Get MySQL Datetime (generally standard)
     * 
     * @access public
     * @param Zend_Date $date
     * @return string
     */
    public function getDateTime(Zend_Date $date)
    {
        return $date->toString("YYYY-MM-dd HH:mm:ss");
    } 
}