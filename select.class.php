<?php
/*************************************
	* File with image class information *
	*************************************/

class image {
    private $id, $name, $class, $type;

    public function __construct ($args = []) {
        $defaults = [
            'list' => [],
            'id' => null,
            'name' => null,
            'class' => null,
            'type' => null
        ];

        $args = array_merge($defaults, $args);

    }
    public function __toString () {
        $list = sprintf('<select name="%s" data-name="%s" class="%s" data-type="%s">', $name, $name, $class, $type);

        if (gettype($this->list) === 'array') {

            foreach ($this->list as $key => $value) {

                $check = $valuecheck ? $value : $key;

                $list .= $this->createoption($key, $value);
            }
        }
        $list .= '</select>';
        return $list;
    }
    
    private function createoption ($key, $value) {
        return sprintf('<option value="%s" %s>%s</option>', $key, 'selected="selected"', $value);
    }
}
