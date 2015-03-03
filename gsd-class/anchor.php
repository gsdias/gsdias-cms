<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.0
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

class anchor {
    private $args;

    public function __construct ($args = array()) {
        $defaults = array(
            'href' => null,
            'alt' => null,
            'external' => false,
            'title' => '',
            'text' => ''
        );
        
        $this->args = array_merge($defaults, $args);
    }
    
    public function __toString () {
        $target = $this->args['external'] ? ' target="_blank"' : '';
        $title = $this->args['title'] ? sprintf(' title="%s"', $this->args['title']) : '';
        
        return sprintf('<a href="%s"%s%s>%s</a>', $this->args['href'], $target, $title, $this->args['text']);
    }
}
