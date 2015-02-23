<?php

class image {
    private $args, $width = 100, $height = 100;

    public function __construct ($args = array()) {
        $defaults = array(
            'iid' => 0,
            'src' => null,
            'alt' => '',
            'class' => '',
            'width' => $this->width,
            'height' => $this->height
        );
        
        $this->args = array_merge($defaults, $args);
        
        if ($this->args['src'] && !is_file(ROOTPATH . $this->args['src'])) {
            $width = is_numeric($this->args['width']) || $this->args['width'] == 'auto' ? $this->args['width'] : $this->width;
            $height = is_numeric($this->args['height']) || $this->args['height'] == 'auto' ? $this->args['height'] : $this->height;
            $this->args['src'] = sprintf("/gsd-image.php?width=%s&height=%s", $width, $height);
        } else {
            $this->args['src'] = sprintf("/gsd-assets/images/%s", $this->args['iid']);
        }
        
        $this->args['width'] = $this->args['width'] ? sprintf(' width="%s"', $this->args['width']) : '';
        $this->args['height'] = $this->args['height'] ? sprintf(' height="%s"', $this->args['height']) : '';
    }
    public function __toString () {
        $class = $this->args['class'] ? sprintf(' class="%s"', $this->args['class']) : '';
        $extra = @$this->args['max-height'] ? sprintf(' style="max-height: %spx;%s"', $this->args['max-height'], @$this->args['style']) : sprintf(' style="%s"', @$this->args['style']);

        return sprintf('<img src="%s"%s%s alt="%s"%s%s />', $this->args['src'], $this->args['width'], $this->args['height'], $this->args['alt'], $class, $extra);
    }
}
