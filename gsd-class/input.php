<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.4
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
            'type' => 'text',
            'labelClass' => '',
            'selected' => '',
        );

        $this->args = array_merge($defaults, $args);
    }

    public function __toString()
    {
        $output = $this->args['label'] ? sprintf('<label for="%s"%s>%s</label>', $this->args['id'], $this->args['labelClass'] ? ' class="'.$this->args['labelClass'].'"' : '', $this->args['label']) : '';

        $outputLeft = $this->args['type'] === 'checkbox' ? '' : $output;
        $outputRight = $this->args['type'] === 'checkbox' ? $output : '';

        return sprintf('%s<input type="%s" id="%s" name="%s" value="%s"%s>%s', $outputLeft, $this->args['type'], $this->args['id'], $this->args['name'], $this->args['value'], $this->args['selected'] ? ' checked="checked"': '', $outputRight);
    }
}
