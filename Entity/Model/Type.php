<?php
/**
 * Project Model
 */

class Blerby_Entity_Model_Type extends Blerby_Application_Db_Table
{
    protected $_name = "entity_type";
    protected $_primary = "id";

    public function insert(array $data)
    {
        return parent::insert($data);
    }

    public function update(array $data, $where)
    {
        
        
        if (isset($data['config']))
        {
            $config = array();
            if (substr($data['config'],0,2) == "[{")
            {
                $config = json_decode(stripcslashes($data['config']), true);
            }
            // TODO: check for is_array
            else
            {
                $config = $data['config'];   
            }
            
            // TODO: move this down a level into components:
            foreach ($config as $k=>$v)
            {
                if (!isset($v['part']) || !$v['part'])
                {
                    $config[$k]['part'] = uniqid("",true);   
                }   
            }
            
            $data['config'] = json_encode($config);

        }
        return parent::update($data, $where);
    }
    

}