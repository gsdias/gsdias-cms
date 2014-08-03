<?php

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
