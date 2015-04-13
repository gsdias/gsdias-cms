<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.1
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

include_once('core/words.php');
/*************************************
 * File with image class information *
 *************************************/

class captcha {

    var $width;
    var $height;

    /* JPG | GIF | PNG */
    var $type;

    var $code;

    var $selected;

    /* Number of characters */
    var $number = 4;
    var $image = null;

    var $bgcolor = "#000080";
    var $txtcolor = "#FFFFFF";

    function captcha ($width, $height, $type) {
        $this->width = $width;
        $this->height = $height;
        $this->type = $type;
        $this->create();
    }
    function create () {
        global $words;
        $this->generate ();
        $this->image = @imagecreatetruecolor(320, 80)
            or die('Cannot Initialize new GD image stream');

        $white = imagecolorallocate($this->image, 255, 255, 255);
        $grey = imagecolorallocate($this->image, 255, 0, 0);
        $black = imagecolorallocate($this->image, 0, 0, 0);
        imagefilledrectangle($this->image, 0, 0, 320, 80, $white);

        // Replace path by your own font path
        $font = 'resources/css/fonts/2086.ttf';

        imagettftext($this->image, 15, rand(-10, 10), 5, 40, $grey, $font, $words[$this->selected[0]]['word']);
        imagettftext($this->image, 15, rand(-10, 10), 170, 40, $black, $font, $words[$this->selected[1]]['word']);

        $this->show();
    }
    function generate () {
        global $words;
        $this->code = "";

        $selected = array_rand ($words, 2);

        $this->selected = $selected;

        $this->code = $words[$selected[0]]['word'] . ' ' . $words[$selected[1]]['word'];

    }
    function code () {return $this->code;}
    function show () {
        switch ($this->type) {
            case 'jpeg':
            header("Content-Type: image/jpeg");
            imagejpeg($this->image);
            break;
            case 'gif':
            header("Content-Type: image/gif");
            imagegif($this->image);
            break;
            case 'png':
            header("Content-Type: image/png");
            imagepng($this->image);
            break;
        }
        imagedestroy($this->image);
    }
}
