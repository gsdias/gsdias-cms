<?php

class anchor {
    private $args;

    public function __construct ($args = array()) {
        $defaults = array(
            'path' => null,
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
        
        return sprintf('<a href="%s"%s%s>%s</a>', $this->args['path'], $target, $title, $this->args['text']);
    }
}
