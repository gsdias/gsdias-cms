<?php

class select {
    private $args;

    public function __construct ($args = array()) {
        $defaults = array(
            'list' => array(),
            'id' => null,
            'name' => null,
            'class' => null,
            'type' => null,
            'empty' => true,
            'valuecheck' => false,
            'selected' => ''
        );

        $this->args = array_merge($defaults, $args);

    }
    
    public function __toString () {
        
        $name = $this->args['name'] ? sprintf(' name="%s" data-name="%s"', $this->args['name'], $this->args['name']) : '';
        $class = $this->args['class'] ? sprintf(' class="%s"', $this->args['class'], $this->args['class']) : '';
        $type = $this->args['type'] ? sprintf(' data-type="%s"', $this->args['type'], $this->args['type']) : '';
        
        $list = sprintf('<select%s%s%s>' . "\n\r", $name, $class, $type);

        if (gettype($this->args['list']) === 'array') {

            foreach ($this->args['list'] as $key => $value) {

                $list .= $this->createoption($key, $value);
            }
        }
        $list .= '</select>' . "\n\r";
        return $list;
    }
    
    private function createoption ($value, $label) {

        $check = $this->args['valuecheck'] ? $label : $value;
        
        $selected = $check == $this->args['selected'] ? ' selected="selected"' : '';
                
        $result = sprintf('<option value="%s"%s>%s</option>' . "\n\r", $value, $selected, $label);
        
        if (!$this->args['empty'] && ($value == 'null' || $value === '' || $label === 'null' || $label === '')) {
            $result = '';
        }
        
        return $result;
    }
    
    public function object () {
        global $tpl;

        $list = array();

        if (gettype($this->args['list']) === 'array' && $this->args['id']) {

            foreach ($this->args['list'] as $value => $label) {

                if (!$this->args['empty'] && ($value == 'null' || $value === '' || $label === 'null' || $label === '')) {
                    continue;
                }

                $check = $this->args['valuecheck'] ? $label : $value;

                $list[$value] = array(
                    'KEY' => $value,
                    'VALUE' => $label
                );
                if ($check == $this->args['selected']) {
                    $list[$value]['SELECTED'] = ' selected="selected"';
                }
            }
        }
        $tpl->setarray(strtoupper($this->args['id']), $list);
    }
}
