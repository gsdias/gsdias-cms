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

class textarea
{
    private $args;

    public function __construct($args = array())
    {
        $defaults = array(
            'name' => '',
            'id' => '',
            'label' => '',
            'value' => '',
            'labelClass' => '',
            'class' => '',
        );

        $this->args = array_merge($defaults, $args);
    }

    public function __toString()
    {
        $output = $this->args['label'] ? sprintf('<label%s>%s</label>', $this->args['labelClass'] ? ' class="'.$this->args['labelClass'].'"' : '', $this->args['label']) : '';

        return sprintf('%s<textarea id="%s" name="%s" class="%s">%s</textarea>', $output, $this->args['id'], $this->args['name'], $this->args['class'], $this->args['value']);
    }
}
