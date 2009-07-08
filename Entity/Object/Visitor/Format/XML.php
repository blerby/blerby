<?php

class Blerby_Entity_Object_Visitor_Format_XML extends Blerby_Entity_Object_Visitor
{
    public function __construct()
    {
          
    }
    
    protected function generateXML($val)
    {
        $ret = '';
        
        if (is_object($val))
        {
            if (!method_exists($val, 'toArray'))
            {
                return;
            }
            
            $val = $val->toArray();
        }
        
        if (is_array($val))
        {
            foreach ($val as $k=>$v)
            {
                if (is_object($v) || is_array($v))
                {
                    $ret .= "<$k>\r\n";
                    $ret .= $this->generateXML($v);
                    $ret .= "</$k>\r\n";   
                }
                else
                {
                    if (preg_match("/[\<\>'\"\n\/\\\\]/",$v))
                    {
                        $ret .= "<{$k}><![CDATA[\r\n{$v}\n]]>\n</{$k}>\n";
                    }
                    else
                    {
                        $ret .= "<{$k}>{$v}</{$k}>\n";
                    }   
                }
            }   
        }
        else
        {
            $ret .= "<{$k} value=\"{$v}\" />\r\n";
        }
        
        
        // TODO: valid xml.
        return $ret;        
    }
    
    public function visit($object)
    {  
        $ret = '<' . '?xml version="1.0"?' . '>' . "\n\n<component>\n";
        return $ret . $this->generateXML($object) . "\n</component>";
    }

    public function mimeType()
    {
        return "text/xml";
    }
}