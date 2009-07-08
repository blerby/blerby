<?php
/**
 * Entity Core
 *
 * This project is a wrapper for all things resource.
 * Using an extremely flexible infrastructure developers
 * are able to quickly bring their application up to speed
 * with minimal effort.
 *
 * @author Elijah Insua <tmpvar@gmail.com>
 * @copyright Copyright &copy; 2009, Elijah Insua
 */

require_once "Blerby/Entity/Component.php";

/**
 * Temporary storage for Entity data
 */

class Blerby_Entity_Entity extends Blerby_Entity_Component
{
    protected $children = array();

    public function init($options = array())
    {
        // TODO: dont depend on zend_db_model
        $componentTable = new Blerby_Entity_Model_Component();
        $components = $componentTable->fetchAll($componentTable->getAdapter()->quoteInto("entity_id = ?", $this->id), "id ASC");
        
        // TODO: move towards a "object set" for iteration and array ops
        $aComponents = array();
        
        foreach ($components as $component)
        {
            // TODO: handle this all in the type!
            $found = false;
            foreach ($this->components as $typeComponent)
            {
                if ((string)$typeComponent->part == (string)$component->part)
                {
                    $found = $typeComponent;
                    break;   
                }   
            }
            
            if (!$found)
            {
                continue;    
            }
            
            $oComponent = Blerby_Entity_Component::factory($found->uri);   
            $oComponent->parent($this);
            // add type config
            $oComponent->fromArray($found->toArray());
            
            // merge in component data
            $oComponent->fromArray($component->toArray());
            
            // TODO: move this into the component (functionally complete objects)
            $componentConfigTable = new Blerby_Entity_Model_Component_Config();
            $componentConfig = $componentConfigTable->fetchAll($componentTable->getAdapter()->quoteInto("entity_component_id = ?", $oComponent->id), "id ASC");
            foreach ($componentConfig as $configItem)
            {
                $oComponent->set($configItem->name, $configItem->value);   
            }
            $aComponents[] = $oComponent; 
        }
        
        $this->bind('entity.save', $this);
        $this->set("components",$aComponents);
        parent::init($options);
    }

    // in this case only accept components
    public function fromArray($array)
    {
        $components = array();
        if (isset($array['components']))
        {
            $components = $array['components'];
            unset($array['components']);   
            
            $aComponents = array();
            foreach ($components as $component)
            {
                if (!($component instanceof Blerby_Entity_Component))
                {
                    print_r($component);
                    $oComponent = Blerby_Entity_Component::factory($component['uri']);
    
                    $oComponent->fromArray($component);
                    $oComponent->parent($this);
                    $aComponents[] = $oComponent;//array_push($this->components, $oComponent);
                }
                else
                {
                    $aComponents[] = $component;   
                }   
            }
            $this->components = $aComponents;
            
        }
        parent::fromArray($array);
     }
    
    public function update($event, $extra = array())
    {
        switch ($event->type())
        {
            case 'entity.save':
                foreach ($this->components as $child)
                {
                    $event->type('component.save');
                    $child->update($event);   
                }
            break;   
        }
        
           
    }


    // TODO: rethink this
    
    public function componentFromPart($part)
    {
        print_r($this);exit;
        
        foreach ($this->type->config->toArray() as $aComponent)
        {
            if ($part == $aComponent->part)
            {
                $ret = Blerby_Entity_Component::factory($aComponent->uri);
                $ret->fromArray($aComponent);
                return $ret;
            }
        }                
    }
    
    // very infantile search!
    public function find($query)
    {
        // TODO: this needs to be moved and fixed
        $logic = explode(' is ', $query);
        
        $clientObj = $this;
        $accessor = explode(".", $logic[0]);
        
        foreach ($accessor as $k=>$name)
        {
            
            if ($k == count($accessor)-1)
            {   
                foreach ((array)$clientObj as $clientV)
                {
                    if ($clientV->$name == $logic[1])
                    {
                        return $clientV;
                    }
                }   
            }
            else
            {
                $clientObj = $this->$name;
            }   
            
        }
        
        switch ($accessor)
        {
             case "component.name":

                foreach ($this->components as $component)
                {
                    if ($component->name == trim($logic[1]))
                    {
                        return $component;
                    }   
                }
             break;
        }
    }
}