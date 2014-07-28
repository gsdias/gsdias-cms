<?php

include_once('../config.php');

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

$main = 'TEST/TEST';

$tpl->includeFiles('MAIN', $main);
$tpl->setFile('INDEX');

echo $tpl;
