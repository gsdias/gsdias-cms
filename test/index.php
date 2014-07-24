<?php

include_once('../class/htmltags.class.php');
include_once('../class/template.class.php');

define('DEBUG', 1);

$tpl = new tpl();

$anchor1 = new anchor(array(
    'path' => '/vagas',
    'title' => 'abc',
    'text' => 'Vagas'
));
$anchor2= new anchor(array(
    'path' => '/about',
    'text' => 'About',
    'external' => true
));

$image = new image(array(
    'path' => '2560x11440.jpg',
    'width' => 300,
    'alt' => 'Some cool text',
    'height' => 'auto'
));

$select = new select(array(
    'list' => array(1 => 'a', 2 => 'b', '' => 'e', 3 => 'c', 4 => 'd', 5 => ''),
    'selected' => '3',
    'id' => 'selecttest'
));

$select->object();

$tpl->includeHtml(sprintf('%s%s<select><!-- LOOP SELECTTEST --><option value="{SELECTTEST_KEY}"{SELECTTEST_SELECTED}>{SELECTTEST_VALUE}</option><!-- ENDLOOP SELECTTEST --></select>%s%s', $image, $select, $anchor1, $anchor2));

$tpl->sendout();