<?php

interface Blerby_Core_Event_Reciever_Interface
{
    public function handle(Blerby_Core_Event_Interface $event);
}