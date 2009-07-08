<?php

require_once "Blerby/Entity/Object.php";
require_once "Blerby/Entity/Model/Component.php";

// TODO: dont rely on $this->meta['file'] as it could change.. in a terrible way.
class Blerby_Entity_Component extends Blerby_Entity_Object
{
    protected $meta = array('file' => __FILE__
                           );

    protected $defaultBinds = array('get.type.update.form',
                                    'get.type.update.config',
                                    'get.entity.update',
                                    'component.save',
                                   );
    
    
    protected static $metaSetup = false;

    protected $parent = null;

    public function init($options = array())    
    {
        if (!($this->meta instanceof Blerby_Entity_Object))
        { 
            $meta = new Blerby_Entity_Object;
            $meta->fromArray($this->meta);
            $this->meta = $meta; 
        }
        
        $dir = dirname($this->meta('file'));
        
        $metacache = $dir . "/metadata.xml";
        if (is_file($metacache))
        {
            $xml = new SimpleXMLElement(file_get_contents($metacache),LIBXML_NOCDATA);

            $this->parseMeta($xml, $this->meta);
        }
        
        
        // collect appropriate rows for config
        $componentTable     = new Blerby_Entity_Model_Component();
        $componentTableMeta = $componentTable->info();
        $this->meta->componentCols = array_keys($componentTableMeta['metadata']);

    }
    
    public function getDataForCols()
    {
        $ret = array();
        
        
        foreach ((array)$this->componentCols as $column)
        {
            $ret[$column] = $this->get($column,"");    
        }   
        return $ret;        
    }
    
    /**
     * Parse metadata into a usable structure
     * 
     */
    protected function parseMeta($data, $current = null)
    {
        
        if ($data instanceof SimpleXMLElement)
        {
            foreach ($data as $key=>$value)
            {
                
                if ($value->children())
                {
                    
                    $ourCurrent = $current;

                    // operating directly on $meta
                    if ($ourCurrent !== null)
                    {
                        $ourCurrent = new Blerby_Entity_Object;
                        $this->meta->$key = $ourCurrent;
                        $this->parseMeta($value->children(), $ourCurrent);
                    }
                    else
                    {
                        $current->$key = $value;   
                    }
                }
                else
                {
                    $current->$key = (string)$value;   
                }
            }
        }
    }

    public function update($event, $extra = array())
    {
        // TODO: add some sort of handle->method handling system
        switch ($event->type())
        {
            case 'get.type.update.form':
                return $this->meta('name');
            break;
            
            case 'get.type.update.config':
                ob_start();
                    include $this->directory() . '/templates/entity.type.update.phtml';
                return ob_get_clean();
            break;

            case 'get.entity.update':
                ob_start();
                    include $this->directory() . '/templates/entity.update.phtml';
                return ob_get_clean();
            break;
            
            case 'component.save':
            {
                // TODO: Fix this, tight coupling is aweful.  going agile though.
                $model = new Blerby_Entity_Model_Component();

                // collect appropriate rows for config
                $componentTable     = new Blerby_Entity_Model_Component();
                $componentTableMeta = $componentTable->info();
                $componentCols      = array_keys($componentTableMeta['metadata']);

                // collect type config keys
                $toStore = array( );

                foreach ($componentCols as $name)
                {
                    if ($this->$name)
                    {
                        // databases dont like objects, store only strings.
                        $toStore[$name] = (string)$this->$name;
                    }
                }
                

                $toStore['entity_id'] = (int)$this->parent()->id;

                // TODO: more rigorous checks
                // insert
                if ($this->get('id',false) === false)
                {
                    $componentTable->insert($toStore);
                    $this->id = $componentTable->getAdapter()->lastInsertId();
                }
                else
                {

                    $model->update($toStore, $model->getAdapter()->quoteInto("id=?",array($this->id)));
                }

                // TODO: Error checking
                foreach ($this->parent()->type->components->toArray() as $template)
                {
                    
                    if ($template->part == $this->part)
                    {       
                        // get all of the configuration for this component that is not set
                        // in the type.
                        $componentConfig = array_diff($this->toArray(), $template->toArray() );

                        // TODO: this needs to be reworked. at somepoint in the future, we may really need to map to another entity id
                        //       and this functionality kills it of if the config name is 'entity_id'                        
                        foreach (array('id','entity_id') as $name)
                        {
                            if (isset($componentConfig[$name]))
                            {
                                unset($componentConfig[$name]);
                            }
                        }
                        
                        $componentConfigTable = new Blerby_Entity_Model_Component_Config();

                        // TODO with auto-incrementing fields this is aweful, add indexes to table
                        $where = $componentConfigTable->getAdapter()->quoteInto("entity_component_id = ?", array((int)$this->id));
                        $componentConfigTable->delete($where);
                        
                        // store each piece of config                            
                        foreach ($componentConfig as $name=>$value)
                        {
                            // no need to store extra data
                            if (!isset($componentCols[$name]))
                            {
                                $toStore = array('entity_component_id' => (int)$this->id, 'name' => $name, 'value'=>$value);
                                $componentConfigTable->insert($toStore);
                            }
                        }          
                    }   
                }


            }
        }
        
        return parent::update($event);
    }
    
    public function isInstanceOfTypeTemplate(Blerby_Entity_Component $template)
    {
        
        if ($template->parent() instanceof Blerby_Entity_Type &&
            $template->uri === $this->uri                     &&
            $template->name === $this->name
        )
        {
            return true;                
        }
        return false;
    }
    
    public function directory()
    {
        return dirname($this->meta('file'));   
    }

    // Create an entity member by name
    public static function factory($name, $options = array())
    {
        @include_once str_replace("_","/",$name) . '.php';
        
        $fullName = $name;        
        if (!class_exists($name))
        {
            $fullName = "Blerby_Entity_Component_{$name}_Object";
            include_once str_replace("_","/",$fullName) . '.php';
        }

        // TODO: figure out how the repository works!
        if (!class_exists($fullName) && !class_exists($name))
        {
            
            
            include_once $name . '/Object.php';
        }



        if (class_exists($fullName))
        {        
            $built = new $fullName;
            $built->init($options);
            return $built;
        }
        return null;
    }

    /**
     * Storage retrieval helper
     * 
     * Note: overrides object, to use 'meta' if not found in object store
     * 
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        $ret = parent::get($key, -1);
        
        if ($ret === -1)
        {
            $ret = $this->meta($key);
        }
        
        return $ret;
    }

    public function __isset($key)
    {
        return (isset($this->store[$key]) || isset($this->meta->$key)) ? true : false;   
    }

    public function meta($key = null)
    {
        if ($key === null)
        {
            return $this->meta;   
        }
        
        if (is_object($this->meta) && $this->meta->$key)
        {
            return $this->meta->$key;
        }
        else if (!is_object($this->meta) && isset($this->meta[$key]))
        {
            return $this->meta[$key];   
        }
        
        return null;
    }

    public function parent(Blerby_Entity_Component $component = null)
    {
        if ($component instanceof Blerby_Entity_Component)
        {
            $this->parent = $component;
            
            // auto bind this objects event handling to the parent
            foreach ($this->defaultBinds as $bind)
            {
                $this->parent->bind($bind, $this);   
            }
        }

        return $this->parent;
    }
}