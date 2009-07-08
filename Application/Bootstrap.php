<?php
/**
 * Class for bootstrapping twacka into a usable state
 * 
 * @author Elijah Insua <tmpvar@gmail.com>
 */

require_once "Zend/Registry.php";
require_once "Zend/Config/Ini.php";
require_once "Zend/Controller/Front.php";
require_once "Zend/Cache.php";
require_once "Zend/Db.php";


// TODO: probably should remove this.
require_once "Zend/Db/Adapter/Pdo/Mysql.php";
require_once "Zend/Db/Table/Abstract.php";
require_once "Zend/View.php";
require_once "Zend/Layout.php";
require_once "Zend/Auth.php";
require_once "Zend/Auth/Storage/Session.php";


class Blerby_Application_Bootstrap 
{
    
    protected $registry;
    
    protected $boostrapped = false;
    
    /**
     * Singleton
     * 
     * 
     * @access public
     * @param string $wwwDir
     * @return Twacka_Application
     */
    public static function instance($wwwDir = "", $appRoot = "")
    {
        static $instance = false;
        
        if (!$instance)
        {
            $class = __CLASS__;
            $instance =  new $class($wwwDir, $appRoot);
        }
        
        return $instance;
    }

    public function registry()
    {
        return $this->registry;   
        
    }

    /**
     * Constructor
     */
    public function __construct($webroot, $appRoot)
    {
        $this->registry = Zend_Registry::getInstance();
        
        $this->registry()->webroot = $webroot;
        
        $this->registry()->appRoot = realpath($appRoot);
        
        Zend_Registry::set("application", $this); 
    }

    /**
     * Setup the config
     * 
     * uses default section of config unless there is an environment.ini file 
     * in the configuration directory.
     * 
     * @access protected
     * @return null
     */    
    protected function setupConfig()
    {
        $environment = 'default';
        
        // ** Attempt an override of the environment **
        if (is_readable($this->registry->appRoot . "/etc/conf/environment.ini"))
        {
            $aEnvironment = parse_ini_file($this->registry->appRoot . "/etc/conf/environment.ini");   
            if (isset($aEnvironment['environment']))
            {
                $environment = $aEnvironment['environment'];   
            }
        }
        
        // ** Create Configuration **
        $config = new Zend_Config_Ini($this->registry->appRoot . "/etc/conf/config.ini");
        
        // ** Move to the correct environment **        
        if (isset($config->$environment))
        {
            $this->registry->config = $config->$environment;
        }
        else
        {
            $environment = "base";
            $this->registry->config = $config->$environment;  
        }
           
    }
    
    /**
     * Setup routes from configuration
     * 
     * @access protected
     * @return null
     */
    protected function setupRoutes()
    {
        // setup routes
        $this->registry()->routeConfig = new Zend_Config_Ini($this->registry->appRoot . "/etc/conf/routes.ini");        
    }

    /**
     * Prepare frontcontroller for use
     * 
     * @access protected
     * @return null
     */
    protected function setupFrontController()
    {
        // ** Instantiate/Setup the front controller **
        $this->registry->frontController = Zend_Controller_Front::getInstance();
        
        
        // ** Setup Module Path **
        $this->registry->frontController
                       //->addModuleDirectory($this->registry->appRoot . "/app/modules/")
                       ->addModuleDirectory($this->registry->appRoot . "/../app/modules/")
                       ;

        
        $this->registry->frontController
             ->getRouter()->addConfig($this->registry()->routeConfig, 'routes')
             ->removeDefaultRoutes();        
    }

    /**
     * Setup caching from configuration
     * 
     * @access protected
     * @return null
     */
    protected function setupCaching()
    {

        // setup cache
        $backendOptions = $this->registry->config->cache->backend->options->toArray();
        if (isset($backendOptions['cache_dir']))
        {
            $backendOptions['cache_dir'] = $this->registry->appRoot . $backendOptions['cache_dir']; 
        }        

        $this->registry->cache = Zend_Cache::factory($this->registry->config->cache->frontend->name,
                                                     $this->registry->config->cache->backend->name, 
                                                     $this->registry->config->cache->frontend->options->toArray(),
                                                     $backendOptions);
    }
    
    /**
     * Setup the view
     * 
     * @access protected
     * @return null
     */
    protected function setupView()
    {
        // *** Collect the current theme ***
        $theme = $this->registry()->config->site->theme;
        $themeDir = $this->registry()->appRoot . "/www/themes/";

        // ** Simple Fallback to default theme **
        if (!is_readable($themeDir . $theme . "/templates"))
        {
            $theme = "default";
        }
        
        $this->registry()->themeDir = $themeDir . $theme;

        // ** Setup View **
        $view = new Zend_View();
        
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view)
                 ->setViewBasePathSpec($this->registry()->themeDir)
                 ->setViewScriptPathSpec(':module/:controller.:action.:suffix')
                 ->setViewScriptPathNoControllerSpec(':controller.:action.:suffix')
                 ->setViewSuffix('phtml');
        
        $view->setScriptPath($this->registry()->themeDir . "/templates");
        $view->doctype('XHTML1_TRANSITIONAL');
        
        // ** make the view globally accessible **
        $this->registry()->view = $view;      
    }
    
    /**
     * Setup view helpers
     * 
     * @access protected
     * @return null
     */
    protected function setupViewHelpers()
    {
        $this->registry->view->addHelperPath('Blerby/Application/Controller/Helper/', 'Blerby_Application_Controller_Helper');
        
    }

    /**
     * Setup the database
     * 
     * @access protected
     * @return null
     */
    protected function setupDatabase()
    {
        // Create and store the database connection **
        $this->registry->db = Zend_Db::factory($this->registry->config->database->adapter,
                                               $this->registry->config->database->params);
        
        // ** Always fetch as row objects **        
        $this->registry->db->setFetchMode(Zend_Db::FETCH_OBJ);
        
        // ** Set the default adapter for this application **
        Zend_Db_Table_Abstract::setDefaultAdapter($this->registry->db);
    }
    
    /**
     * Setup view layout
     * 
     * @access protected
     * @return null 
     */    
    protected function setupViewLayout()
    {
        $layout = Zend_Layout::startMvc();
        $layout->setLayoutPath($this->registry()->themeDir . '/layout/');
    }

    /**
     * Setup authentication
     * 
     * @access protected
     * @return null
     */
    protected function setupAuth()
    {
        // setup auth
        $this->registry->auth = Zend_Auth::getInstance();
        $this->registry->auth->setStorage(new Zend_Auth_Storage_Session());
    }
    
    /**
     * Get the application to a usable state
     * 
     * @access public
     * @return null
     */
    public function bootstrap()
    {
        $this->setupConfig();        
        $this->setupRoutes();
        $this->setupCaching();
        $this->setupDatabase();
        $this->setupView();
        $this->setupViewLayout();
        $this->setupAuth();
        $this->setupViewHelpers();
        $this->setupFrontController();
        
        $this->boostrapped = true;
    }
    
    /**
     * Execute the application
     * 
     * @access public
     * @return null
     */
    public function execute()
    {
        // ** Boostrap if required **
        if (!$this->boostrapped)
        {
            $this->bootstrap();   
        }
             
        // ** Kick off the actual execution of the app **
        $this->registry->frontController->dispatch();      
    }
}