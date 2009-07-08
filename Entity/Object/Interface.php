<?php

/** ## HEADER ## **/

interface Blerby_Entity_Object_Interface
{
    /**
     * Magic getter abstract function 
     * 
     * @param  string $k
     * @return mixed
     */
    public function __get($k);
    
    /**
     * Get a value from the local store
     * 
     * @param  string $k
     * @param  mixed  $default default value to return if $k is not in store
     * @return mixed
     */
    public function get($k, $default = null);
    
    /**
     * Magic setter abstract function
     * 
     * @param  string $k
     * @param  string $v
     * @return mixed
     */
    public function __set($k, $v);

    /**
     * Setter helper
     * 
     * @param  string $k
     * @param  string $v
     * @return mixed
     * 
     */
    public function set($k, $v);

    /**
     * From array helper abstract function
     * 
     * Chainable
     * 
     * @param  array $array
     * @return Blerby_Entity_Object 
     */    
    public function fromArray($array);
    
    /**
     * To array helper
     * 
     * @return array
     */
    public function toArray();
    
    /**
     * Magic toString abstract method
     * 
     * @return string
     */
    public function __toString();
}