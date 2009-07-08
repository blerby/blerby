<?php

require_once "Blerby/Core/Observer/Interface.php";
require_once "Blerby/Core/Event/Interface.php";

/**
 * Interface for observable actions
 */
interface Blerby_Core_Observable_Interface
{ 
    /**
     * Abstract function for binding an event type to a handler object
     * 
     * @param string                         $eventType
     * @param Blerby_Core_Observer_Interface $observer
     * @return null
     */
    public function bind($eventType, $observer);
    
    /**
     * Abstract function for trigering an event on this branch
     * 
     * @param  Blerby_Core_Event_Interface $event
     * @return null
     */
    public function trigger($event);

    /**
     * Abstract function for testing if a bind point exists
     * 
     * @param  string $event
     * @return bool
     */
    public function hasBind($event);
}