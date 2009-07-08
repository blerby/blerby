<?php

interface Blerby_Core_Object_Interface
{
    public function __get($k);
    public function get($k, $default = null);
    public function __set($k, $v);
    public function set($k, $v);
    public function clear();
    public function fromArray($array);
    public function toArray();
    
}