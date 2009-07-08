<?php

require_once "Blerby/Application/Controller/Action.php";

/**
 * Cudle actions
 * 
 * @author Elijah Insua <tmpvar@gmail.com>
 * @package Blerby_Application_Controller 
 */


/**
 * Base cudle actions w/ "forms"
 * 
 * If you want to extend how these forms work, extend this class and retrieve your form
 * from another source, the interface should be the same as a Zend_Form though.
 * 
 * @author Elijah Insua <tmpvar@gmail.com>
 */
class Blerby_Application_Controller_Action_Cudle extends Blerby_Application_Controller_Action
{
    /**
     * Create a new element
     *
     * @access public
     * @return null
     */
    public function createAction()
    {
        $this->view->form = $this->getForm();

        // check if its posting
        if ($this->getRequest()->isPost())
        {
            // check validation
            if ($this->view->form->isValid($this->getRequest()->getPost()))
            {
                // get the model data and save them to the db
                $model = $this->getModel();
                
                // handle auto serialize
                // TODO: Make auto serialization configurable?
                $values = $this->view->form->getValues();
                foreach ($values as $k=>$value)
                {
                    if (is_array($value) || is_object($value))
                    {
                        $values[$k] = json_encode($value);   
                    }   
                }

                if (!$model->insert($values))
                {
                    $this->view->form->addErrorMessage("There was a problem with your request, please try again.");
                }
                else
                {
                    // redirect
                    return $this->_helper->redirector->gotoRoute(array(),$this->getRequest()->module . "_" . $this->getRequest()->getControllerName() . '_list');
                }
            }
        }
    }

    /**
     * Delete an item w/confirmation
     *
     * @access public
     * @return null
     */
    public function deleteAction()
    {

        $model = $this->getModel();

        // get the primary key of the model
        $primaryKey       = $model->getPrimaryKey();
        $id               = $this->getRequest()->$primaryKey;
        $sql              = $model->select()->where("{$primaryKey} = ?",array($id));
        $this->view->result = $model->fetchRow($sql);

        // check if its posting
        if ($this->getRequest()->isPost())
        {
// TODO: Check ownership
// TODO: Check exist
           
            if (!$this->view->result->delete())
            {
                $this->view->form->addErrorMessage("There was a problem with your request, please try again.");
            }
            else
            {
                // redirect
                return $this->_helper->redirector->gotoRoute(array(),$this->getRequest()->module . "_" . $this->getRequest()->getControllerName() . '_list');
            }
        }
    }

    /**
     * Retrieve an extended view of a table
     *
     *
     */
    public function extendedAction()
    {
        $model = $this->getModel();

        // TODO: check ownership

        $this->view->result = $model->getExtended($this->getRequest());
    }

    /**
     * Edit an item
     *
     * @access public
     * @return null
     */
    public function updateAction()
    {

        $this->view->form = $this->getForm();
        $model            = $this->getModel();

        // get the primary key of the model
        $primaryKey       = $model->getPrimaryKey();
        $id               = $this->getRequest()->$primaryKey;
        
        $sql              = $model->select()->where("{$primaryKey} = ?",array($id));
        $this->view->result = $model->fetchRow($sql);
        
        // check if its posting
        if ($this->getRequest()->isPost())
        {
            // check validation
            if ($this->view->form->isValid($this->getRequest()->getPost()))
            {

                // handle auto serialize
                // TODO: Make auto serialization configurable?
                $values = $this->view->form->getValues();
                foreach ($values as $k=>$value)
                {
                    if (is_array($value) || is_object($value))
                    {
                        $values[$k] = json_encode($value);   
                    }   
                }
                
                $this->view->result->setFromArray($values);

                // save row
                if (!$this->view->result->save())
                {
                    // dump a form error
                    $this->view->form->addErrorMessage("There was a problem with your request, please try again.");
                }
                else
                {
                    // redirect
                    return $this->_helper->redirector->gotoRoute(array(),$this->getRequest()->module . "_" . $this->getRequest()->getControllerName() . '_list');
                }
            }
        }
        
        // populate the form
        $this->view->form->populate($this->view->result->toArray());
    }

    /**
     * List The current items
     *
     * @access public
     * @return null
     */
    public function listAction()
    {
        $model = $this->getModel();
        $this->view->results = $model->fetchAll();
    }
}