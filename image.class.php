<?php
/*************************************
	* File with image class information *
	*************************************/

class image {
    private $path, $alt, $width, $height;

    public function __construct ($path, $alt = null, $width = null, $height = null) {
        $this->alt = $alt ? $alt : '';
        $this->width = $width ? $width : '910';
        $this->height = $height ? $height : '455';
        if (file_exists($path)) {
            $this->path = "{URL}" . $path;
        } else {
            $this->path = sprintf("image.php?width=%s&&height=%s", $this->width, $this->height);
        }
    }
    public function __toString () {
        return sprintf('<img src="%s" width="%s" height="%s" alt="%s" />', $this->path, $this->width, $this->height, $this->alt);
    }
}
