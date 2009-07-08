<?php
/** ## HEADER ## **/

require_once "Blerby/Entity/Object/Interface.php";
require_once "Blerby/Core/Observable/Interface.php";
require_once "Blerby/Core/Event.php";

/**
 * Blerby Entity Object
 * 
 * @author   Elijah Insua <tmpvar@gmail.com>
 * @package  Blerby_Entity
 * @category Base
 */
class Blerby_Entity_Object implements Blerby_Entity_Object_Interface, 
                                      Blerby_Core_Observable_Interface,
                                      Blerby_Core_Observer_Interface
{
    /**
     * Key-Value storage for this class
     * 
     * @var array
     */
    protected $store = array();
    
    /**
     * Event bindings
     * 
     * @var array
     */
    protected $binds = array();
    
    /**
     * Magic getter function, utilizes get
     * 
     * @return mixed
     */
    public function __get($k)
    {
        return $this->get($k);    
    }
    
    /**
     * Get a value from the local store
     * 
     * @param  string $k
     * @param  mixed  $default default value to return if $k is not in store
     * @return mixed
     */
    public function get($k, $default = null, $chainByDefault = true)
    {
        // inform any listeners that a get is about to occur
        $this->trigger(new Blerby_Core_Event('pre.get'));
        
        if ($default === null && $chainByDefault)
        {
            $default = new Blerby_Entity_Object();
        }
        
        if (!isset($this->store[$k]))
        {
            $default =  (isset($this->store[$k])) ?
                    $this->store[$k]        :
                    $default;
        }
        else
        {
            $default =  $this->store[$k];   
        }
        
        // inform any listeners that a get occured to occur
        $this->trigger(new Blerby_Core_Event('post.get'));
        
        return $default;
    }
    
    /**
     * Magic setter function
     * 
     * @param  string $k
     * @param  string $v
     * @return mixed
     */    
    public function __set($k, $v)
    {
        $ret = $this->set($k, $v);
    }

    /**
     * Setter helper
     * 
     * @param  string $k
     * @param  string $v
     * @return mixed
     */    
    public function set($k, $v)
    {
        // inform any listeners that a get occured to occur
        $this->trigger(new Blerby_Core_Event('pre.set'));        
        
        if (!isset($this->$k)) {
            $this->store[$k] = $v;    
        } else {
            $this->$k = $v;   
        }
        
        // inform any listeners that a get occured to occur
        $this->trigger(new Blerby_Core_Event('post.set'));
        
        return $v;
    }

    /**
     * From array helper abstract function
     * 
     * Chainable
     * 
     * @param  array $array
     * @return Blerby_Entity_Object 
     */      
    public function fromArray($array)
    {
        $this->trigger(new Blerby_Core_Event('pre.fromArray'));
        
        if (is_object($array))
        {
            $array = (array)$array;   
        }
        
        if (is_array($array))
        {
            foreach ($array as $k=>$i)
            {
                if (is_array($i))
                {
                    $array[$k] = new Blerby_Entity_Object();
                    $array[$k]->fromArray($i);
                }
            }
            
            foreach ($array as $k=>$v)
            {
                $this->store[$k] = $v;
            }     
        }
        
        $this->trigger(new Blerby_Core_Event('post.fromArray'));
        
        // chainable
        return $this;
    }

    /**
     * To array helper
     * 
     * @return array
     */    
    public function toArray()
    {
        $this->trigger(new Blerby_Core_Event('pre.toArray'));        
        $this->trigger(new Blerby_Core_Event('post.toArray'));
        return $this->store;
        
           
    }

    /**
     * Magic toString method
     * 
     * @return string
     */
    public function __toString()
    {
        $array = $this->toArray();
        if (!empty($array))
        {
            return json_encode($this->toArray());
        }
        else
        {
            return "";
        }
    }
    
    /**
     * Magic isset method
     * 
     * @param  $key
     * @return bool
     */
    public function __isset($k)
    {
        return (isset($this->store[$k])) ? true : false;   
    }

    /**
     * Bind an event type to a handler object
     * 
     * @param string                         $eventType
     * @param Blerby_Core_Observer_Interface $observer
     * @return null
     */
    public function bind($eventType, $observer)
    {
        if (!isset($this->binds[$eventType]))
        {
            $this->binds[$eventType] = array();
        }
        
        $this->binds[$eventType][] = $observer;        
    }
    
    /**
     * Triger an event on this branch
     * 
     * @param  Blerby_Core_Event_Interface $event
     * @return null
     */
    public function trigger($event)
    {
        if ($this->hasBind($event->type()))
        {
            foreach ($this->binds[$event->type()] as $bind)
            {
                $bind->update($event);   
            }
        }
    }
    
    /**
     * Testing if a bind point exists
     * 
     * @param  string $event
     * @return bool
     */
    public function hasBind($eventType)
    {
        return isset($this->binds[(string)$eventType]);   
    }
    
    /**
     * 
     */
    public function update($event, $extra = array())
    {
    } 
}
