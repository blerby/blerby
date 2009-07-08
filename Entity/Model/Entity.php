<?php
/**
 * Project Model
 */

class Blerby_Entity_Model_Entity extends Blerby_Application_Db_Table
{
    protected $_name = "entity";
    protected $_primary = "id";
    
    protected function saveComponents($data)
    {
        if (is_array($data) && isset($data['components']))
        {
            foreach ($data['components'] as $component)
            {
                $table = new Blerby_Entity_Model_Component();
                
                if (!isset($component['id']) || !$component['id'])
                {
                    unset($component['id']);
                    $component->insert($component);
                }
                else
                {
                    $where = $table->getAdapter()->quoteInto('id = ? AND entity_id = ?', (int)$component['id'], (int)$this->id);
                    $table->update($component, $where);
                }
            }
        }   
    }
    
    public function insert(array $data)
    {
        return parent::insert($data);
    }

    public function update(array $data, $where)
    {
        return parent::update($data, $where);
    }
    
}