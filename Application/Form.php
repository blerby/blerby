<?php

require_once "Zend/Form.php";

class Blerby_Application_Form extends Zend_Form
{

    /**
     * Get Validation Messages
     *
     * @param string $name
     * @param bool   $suppressArrayNotation
     * @return array
     */
    public function getMessages($name = null, $suppressArrayNotation = false)
    {
        $messages = parent::getMessages($name, $suppressArrayNotation);

        if (empty($messages))
        {
            $messages = array_merge($messages, $this->getErrorMessages());
        }

        return $messages;
    }

    /**
     * Get calculated form values
     *
     * Note: strips off input named 'submit'
     *
     * @return array
     */
    public function getValues($suppressArrayNotation = false)
    {
        $values = parent::getValues();

        if (isset($values['submit']))
        {
            unset($values['submit']);
        }

        return $values;
    }

}