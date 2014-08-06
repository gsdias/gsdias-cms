<?php

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
        
        if (!is_file(ROOTPATH . $this->args['path'])) {
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
