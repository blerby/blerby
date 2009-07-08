<?php
/**
 * Web resource optimizer
 * 
 * This for local files only (css/javascript for now)
 * creates a link that can be interpreted by www/optimizer.php
 * 
 * 
 * @package Blerby_Application_Controller_Helper
 * @author Elijah Insua <tmpvar@gmail.com>
 */
 
/**
 * Optimize helper class
 */
class Blerby_Application_Controller_Helper_Optimize extends Zend_View_Helper_Abstract
{
    /**
     * Resource storage
     * 
     * @access protected
     * @var array
     */
    protected $resources = array();
    
    /**
     * Current resource type (for chaining)
     * 
     * @access protected
     * @var string
     */
    protected $current = null;
    
    /**
     * Current options associated with current
     * 
     * ie: for css this will contain media=screen|print
     *
     * @access protected 
     * @var array
     */
    protected $currentOptions = array();
    
    /**
     * Hook into the optimization system (chainable)
     * 
     * @param string $type   css|javascript
     * @param array  $options
     * @return Blerby_Application_Controller_Helper_Optimize
     */
    public function optimize($type = "", $options = false)
    {
        if ($type && isset($this->resources[$type]))
        {
            $this->current = $type;   
        }
        
        // ** Either update the current options array or reset it **
        if (is_array($options)) 
        {
            $this->currentOptions = $options;
        }
        else
        {
            $this->currentOptions = array();   
        }
        
        return $this;
    }

    /**
     * Css optimization
     * 
     * @param string $meda screen|print
     * @param string $file 
     * @return Blerby_Application_Controller_Helper_Optimize
     */
    public function css($media, $file = "")
    {
        if ($file && $media)
        {
            $this->add("css", $file, array("media"=>$media, "rel"=>"stylesheet", "type"=>"css"));
        }
        
        $this->currentOptions['media'] = $media;        
        $this->current = "css";        
        return $this;
    }

    /**
     * Javascript optimization
     * 
     * @param string $file
     * @return Blerby_Application_Controller_Helper_Optimize
     */
    public function javascript($file = "")
    {
        $this->current = "javascript";
        
        $this->add("javascript", $file, array());
        
        return $this;
    }
    
    /**
     * Add a resource to be optimized
     * 
     * 
     * @param string type   css|javascript
     * @param string $file  local filename
     * @param array  $options 
     * @return Blerby_Application_Controller_Helper_Optimize
     */    
    protected function add($type, $file, $options)
    {
        if ($file)
        {
            if (!isset($this->resources[$type]))
            {
                $this->resources[$type] = array();
            }
                
            $this->resources[$type][$file] = $options; 
        }
        
        return $this;
    }
    
    /**
     * Convert the last touched resource type into an optimized link
     * 
     * @return string
     */
    public function __toString()
    {
        switch ($this->current)
        {
            // ** Handle CSS Resources **
            case 'css':

                // *** Calculate which resources should be used according to the current media spec **
                $queue = array();
                if (isset($this->resources[$this->current]))
                {
                    foreach ($this->resources[$this->current] as $file=>$resource)
                    {
                        if (isset($resource['media'])    &&
                            isset($this->currentOptions['media']) &&
                            $resource['media'] == $this->currentOptions['media'])
                        {
                            $queue[] = $file;
                        }
                    }
                }
                                
                // *** Ensure that some css resource was found before dumping the link **                
                if (!empty($queue))
                {
                    $resources = implode(",", $queue);
                    return "<link href=\"{$this->view->baseUrl}/optimizer.php?type=css&amp;resources={$resources}\" media=\"{$this->currentOptions['media']}\" rel=\"stylesheet\" type=\"text/css\" />";
                }
            break;
            
            // ** Handle javascript resources **
            case 'javascript':
                
                // ** Compress the files into an implodable array **
                $queue = array();
                if (isset($this->resources[$this->current]))
                {
                    foreach ($this->resources[$this->current] as $file=>$resource)
                    {
                        $queue[] = $file;
                    }
                }
                        
                // *** Ensure that the queue is not empty before dumping the link **
                if (!empty($queue))
                {
                    $resources = implode(",", $queue);
                    return "<script src=\"{$this->view->baseUrl}/optimizer.php?type=javascript&amp;resources={$resources}\" type=\"text/javascript\"></script>";
                }
                
            break;
        }
           
        return "";
    }
}