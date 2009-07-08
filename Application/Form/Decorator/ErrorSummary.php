<?php

class Blerby_Application_Form_Decorator_ErrorSummary extends Zend_Form_Decorator_Abstract
{
    /**
     * Add a class to the
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $messages = $this->getElement()->getErrorMessages();
        if (!empty($messages))
        {
            $html = $this->getOption("html");
            $content = sprintf($html,array_shift($messages)) . $content;
        }
        return  $content;
    }
}

