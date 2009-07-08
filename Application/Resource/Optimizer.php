<?php

class Blerby_Application_Resource_Optimizer
{
    protected static $aDirectories = array();
    
    public static function addDir($dir)
    {
        
        if (!session_id())
        {
            session_start();   
        }
        
        if (!isset($_SESSION['Blerby_Resource_Optimizer']) ||
            !is_array($_SESSION['Blerby_Resource_Optimizer']))
        {
            $_SESSION['Blerby_Resource_Optimizer'] = array();   
        }
        
        $_SESSION['Blerby_Resource_Optimizer'][$dir] = $dir;
    }
    
    public static function getResource($path)
    {
        if (!session_id())
        {
            session_start();   
        }        
        
        if (isset($_SESSION['Blerby_Resource_Optimizer']) &&
            is_array($_SESSION['Blerby_Resource_Optimizer']))
        {
            foreach ($_SESSION['Blerby_Resource_Optimizer'] as $directory)
            {
                
                $file = str_replace(array("\\\\","//"),"/", $directory . "/" . str_replace("..","",$path));
                if (file_exists($file))
                {
                    return $file;
                }
            }
        }
                
        return "";
    }
    
}