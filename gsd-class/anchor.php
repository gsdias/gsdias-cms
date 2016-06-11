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

class anchor
{
    private $args;

    public function __construct($args = array())
    {
        $defaults = array(
            'href' => null,
            'alt' => null,
            'external' => false,
            'title' => '',
            'text' => '',
            'class' => '',
        );

        $this->args = array_merge($defaults, $args);
    }

    public function __toString()
    {
        $target = $this->args['external'] ? ' target="_blank"' : '';
        $title = $this->args['title'] ? sprintf(' title="%s"', $this->args['title']) : '';
        $class = $this->args['class'] ? sprintf(' class="%s"', $this->args['class']) : '';

        return sprintf('<a href="%s"%s%s%s>%s</a>', $this->args['href'], $target, $title, $class, $this->args['text']);
    }
}
