<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.5.1
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
namespace GSD;

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
            'isRequired' => false,
            'notRender' => false,
            'noValue' => false,
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

    public function __toString()
    {
        return '';
    }
}
