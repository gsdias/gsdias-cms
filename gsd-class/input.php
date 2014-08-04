<?php

class input {
    private $args;

    public function __construct ($args = array()) {
        $defaults = array(
            'name' => '',
            'id' => '',
            'label' => '',
            'value' => ''
        );
        
        $this->args = array_merge($defaults, $args);
    }
    
    public function __toString () {
        
        return sprintf('<label>%s</label><input id="%s" name="%s" value="%s">', $this->args['label'], $this->args['id'], $this->args['name'], $this->args['value']);
    }
}
