<?php

require_once "Blerby/Entity/Form.php";

class Blerby_Entity_Form_Type_Create extends Blerby_Entity_Form
{
    public function init()
    {
        // set the method for displaying form to POST
        $this->setMethod('post');

        $this->setAction($this->getView()->url(array(),"entity_type_create"));

        $this->addElement('text', 'name', array(
            'label'        => 'Name',
            'size'         => 40,
            'required'     => true
        ));

        $this->addElement('textarea', 'description', array(
            'label'        => 'Description',
            'rows'         => 5,
            'cols'         => 40,
            'required'     => true,
        ));

        $this->addElement('hidden', 'config', array(
            'label'        => '',
            'required'     => false,
        ));

        // add submit button
        $this->addElement('submit', 'submit', array(
            'label'      => 'Save',
            'ignore'     => true,
        ));
    }
}