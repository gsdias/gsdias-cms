<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.5
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
$width = is_numeric(@$_REQUEST['width']) ? $_REQUEST['width'] : 100;
$height = is_numeric(@$_REQUEST['height']) ? $_REQUEST['height'] : 100;

header('Content-type: image/png');

$out = imagecreatetruecolor($width, $height);
$white = imagecolorallocate($out, 255, 255, 255);

imagefill($out, 0, 0, $white);

$png = imagecreatefrompng('gsd-resources/css/img/icons/normal/logo_small.png');
list($newwidth, $newheight) = getimagesize('gsd-resources/css/img/icons/normal/logo_small.png');

$x = $width / 2 - $newwidth / 2;
$y = $height / 2 - $newheight / 2;

imagecopyresampled($out, $png, $x, $y, 0, 0, $newwidth, $newheight, $newwidth, $newheight);
imagejpeg($out);

imagedestroy($out);
