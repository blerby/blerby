<?php

require_once "Blerby/Entity/Form.php";

class Blerby_Entity_Form_Entity_Create extends Blerby_Entity_Form
{
    
    public function init()
    {
    }
    
    public function getValues($suppressArrayNotation = false)
    {
        
        if (isset($_POST['components']))
        {

            $rawComponents = $_POST['components'];   
            $aComponents = json_decode(stripcslashes($rawComponents), true);
            
            // clean the component array
            foreach ($aComponents as $k=>$aComponent)
            {
                foreach ($aComponent as $prop=>$component)
                {
                    if (!$prop)
                    {
                        unset($aComponents[$k][$prop]);   
                    }   
                }
               
            }
            return $aComponents;
        }
        return array();
        
        
    }
}