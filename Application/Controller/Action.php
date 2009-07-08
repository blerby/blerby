<?php

require_once "Blerby/Application/Db/Table.php";

class Blerby_Application_Controller_Action extends Zend_Controller_Action
{
    protected $modelName = "Blerby_Application_Db_Table";
    protected $model     = null;
    protected $formName  = "Blerby_Application_Form";
    protected $form      = null;

    public function getModel($name = "")
    {

        if ($this->modelName == null && !$name)
        {
           return;
        }
        $name = ($name) ? $name : $this->modelName;

        if ($this->model === null)
        {
            // TODO: move to a factory
            require_once str_replace("_","/", $name) . ".php";

            $this->model = new $name;
        }

        return $this->model;
    }

    protected function getDb()
    {
        return Zend_Registry::get("db");
    }

    /**
     * Automatic form resolution convention vs configuration
     *
     * Uses request (controller/action) to determine what model to load
     * @param bool $reload forces reload of form
     * @return Twacka_Form
     */
    public function getForm($reload = false)
    {
        static $instance = null;
        if ($reload || !$instance) {

            $controller      = ucfirst(strtolower($this->getRequest()->getControllerName()));
            $action          = ucfirst(strtolower($this->getRequest()->getActionName()));
            $applicationName = Zend_Registry::get('config')->application->name;

            $class = "{$this->formName}_{$action}";
            
            // TODO: factory
            require_once str_replace("_","/", $class) . ".php";
            
            $instance = new $class;

            $instance->addElementPrefixPath("{$applicationName}_Form_Decorator",
                                            "{$applicationName}/Form/Decorator", 'decorator');

            $instance->addElementPrefixPath("Blerby_Application_Form_Decorator",
                                            "Blerby/Application/Form/Decorator", 'decorator');


            // setup default element decorators
            $default = array(array('ErrorHighlight', array('class'=>'errors')),
                             'ViewHelper',
                             array('Description', array('tag' => 'p', 'class' => 'description')),
                             array('HtmlTag', array('tag' => 'dd')),
                             array('Label', array('tag' => 'dt')),
                            );


            $instance->setElementDecorators($default, array("submit"), false);
        }
        return $instance;
    }

    public function init()
    {
        $this->getModel();
        parent::init();
    }

    public function preDispatch()
    {
        $this->view->baseUrl = $this->getRequest()->getBaseUrl();
        
        $themeDir = Zend_Registry::get("themeDir");
        require_once "{$themeDir}/config.php";
        
        if ($this->getRequest()->disableLayout) {
            $this->_helper->layout()->disableLayout();
        }

        return parent::preDispatch();
    

    }

}