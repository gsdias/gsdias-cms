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
            'required' => 0,
            'type' => 'text',
            'labelClass' => '',
            'selected' => 0,
            'autofocus' => 0,
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
        $class = $this->args['type'] === 'password' ? ' class="gsd-password"' : '';
        $isEmail = $this->args['type'] === 'email' ? ' data-rule-email="true"' : '';
        $isPassword = $this->args['type'] === 'password' ? ' <i class="fa fa-eye gsd-pass-toggle"></i><i class="gsd-complexity"></i>' : '';
        $autofocus = $this->args['autofocus'] ? ' autofocus="autofocus"' : '';

        return sprintf('%s<input type="%s" id="%s"%s%s value="%s"%s%s%s%s>%s%s', $outputLeft, $type, $this->args['id'], $autofocus, $name, $this->args['value'], $checked, $required, $isEmail, $class, $outputRight, $isPassword);
    }
}
