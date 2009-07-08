<?php

class Blerby_Application_Form_Decorator_ErrorHighlight extends Zend_Form_Decorator_Abstract
{
    /**
     * Add a class to the
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        if ($this->getElement()->hasErrors())
        {
            $this->getElement()->class = $this->getElement()->class . " " . $this->getOption("class");
        }
        return $content;
    }
}

