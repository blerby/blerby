<?php

require_once "Blerby/Entity/Form/Type/Create.php";

class Blerby_Entity_Form_Type_Update extends Blerby_Entity_Form_Type_Create
{
    public function init()
    {
        parent::init();
        $this->setAction($this->getView()->url(array(),"entity_type_update"));
    }
    
    public function getValue($name)
    {
        $ret = parent::getValue($name);

        // config gets special treatment
        if ($name == 'config')
        {
            if ($ret)
            {
                $obj = new Blerby_Entity_Object();
                $obj->fromArray(json_decode(stripcslashes($ret), true));
                $ret = $obj;
            }   
        }
        return $ret;
    }
}