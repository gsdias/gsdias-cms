<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.2
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

namespace GSD;

class input {
    private $args;

    public function __construct ($args = array()) {
        $defaults = array(
            'name' => '',
            'id' => '',
            'label' => '',
            'value' => '',
            'type' => 'text'
        );
        
        $this->args = array_merge($defaults, $args);
    }
    
    public function __toString () {
        $output = $this->args['label'] ? sprintf('<label>%s</label>', $this->args['label']) : '';
        
        return sprintf('%s<input type="%s" id="%s" name="%s" value="%s">', $output, $this->args['type'], $this->args['id'], $this->args['name'], $this->args['value']);
    }
}
