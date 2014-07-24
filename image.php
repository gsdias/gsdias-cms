<?php

$width = @$_REQUEST['width'] ? $_REQUEST['width'] : 50;
$height = @$_REQUEST['height'] ? $_REQUEST['height'] : 50;

header("Content-type: image/gif");

$img = imagecreatetruecolor($width, $height);
$white = imagecolorallocate($img, 255, 255, 255);

imagefill($img, 0, 0, $white);

imagegif($img);

imagedestroy($img);