<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
namespace GSD;

class input
{
    private $args;

    public function __construct($args = array())
    {
        $defaults = array(
            'name' => '',
            'id' => '',
            'label' => '',
            'value' => '',
            'required' => false,
            'type' => 'text',
            'labelClass' => '',
            'selected' => false,
        );

        $this->args = array_merge($defaults, $args);
        
        if ($this->args['type'] === 'checkbox') {
            $this->args['value'] = 'on';
        }
    }

    public function __toString()
    {
        $output = $this->args['label'] ? sprintf('<label for="%s"%s>%s</label>', $this->args['id'], $this->args['labelClass'] ? ' class="'.$this->args['labelClass'].'"' : '', $this->args['label']) : '';

        $outputLeft = $this->args['type'] === 'checkbox' ? '' : $output;
        $outputRight = $this->args['type'] === 'checkbox' ? $output : '';
        
        
        $name = $this->args['name'] ? sprintf(' name="%s"', $this->args['name']) : '';
        $type = $this->args['type'];
        $required = $this->args['required'] ? ' required' : '';
        $checked = $this->args['selected'] ? ' checked="checked"': '';
        $isEmail = $this->args['type'] === 'email' ? ' data-rule-email="true"' : '';
        $isPassword = $this->args['type'] === 'password' ? ' <i class="fa fa-eye gsd-password"></i>' : '';

        return sprintf('%s<input type="%s" id="%s"%s value="%s"%s%s%s>%s%s', $outputLeft, $type, $this->args['id'], $name, $this->args['value'], $checked, $required, $isEmail, $outputRight, $isPassword);
    }
}
