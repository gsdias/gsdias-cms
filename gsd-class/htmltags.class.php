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

class image {
    private $args, $width = 910, $height = 455;

    public function __construct ($args = array()) {
        $defaults = array(
            'path' => null,
            'alt' => '',
            'width' => $this->width,
            'height' => $this->height
        );
        
        $this->args = array_merge($defaults, $args);
        
        if (!file_exists($this->args['path'])) {
            $width = is_numeric($this->args['width']) ? $this->args['width'] : $this->width;
            $height = is_numeric($this->args['height']) ? $this->args['height'] : $this->height;
            $this->args['path'] = sprintf("/image.php?width=%s&&height=%s", $width, $height);
        }
        
        $this->args['width'] = $this->args['width'] ? sprintf(' width="%s"', $this->args['width']) : '';
        $this->args['height'] = $this->args['height'] ? sprintf(' height="%s"', $this->args['height']) : '';
    }
    public function __toString () {
        return sprintf('<img src="%s"%s%s alt="%s" />', $this->args['path'], $this->args['width'], $this->args['height'], $this->args['alt']);
    }
}


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
