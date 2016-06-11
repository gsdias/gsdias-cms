<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
namespace GSD;
defined('GVALID') or die;

class field
{
    private $args;

    public function __construct($args = array())
    {
        $defaults = array(
            'name' => '',
            'type' => 'input',
            'validator' => array(),
            'label' => '',
            'isRequired' => 0,
            'notRender' => 0,
            'noValue' => 0,
            'extra' => 0,
            'autofocus' => 0,
            'value' => '',
            'values' => array()
        );

        $this->args = array_merge($defaults, $args);
        
        if (in_array('isRequired', $this->args['validator'])) {
            $this->args['isRequired'] = true;
        }
    }
    
    public function setName($name)
    {
        $this->args['name'] = $name;
    }
    
    public function getName()
    {
        return $this->args['name'];
    }
    
    public function getRequired()
    {
        return $this->args['isRequired'];
    }
    
    public function getNoValue()
    {
        return $this->args['noValue'];
    }
    
    public function getType()
    {
        return $this->args['type'];
    }
    
    public function getValidator()
    {
        return $this->args['validator'];
    }
    
    public function getLabel()
    {
        return $this->args['label'];
    }
    
    public function getValue()
    {
        return $this->args['value'];
    }
    
    public function getValues()
    {
        return $this->args['values'];
    }
    
    public function getNotRender()
    {
        return $this->args['notRender'];
    }

    public function getExtra()
    {
        return $this->args['extra'];
    }

    public function getAutofocus()
    {
        return $this->args['autofocus'];
    }

    public function __toString()
    {
        return '';
    }
}
