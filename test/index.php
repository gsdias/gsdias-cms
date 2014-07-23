<?php

include_once('../mysql.class.php');
//include_once('../email.class.php');
include_once('../template.class.php');

$mysql = new mysql('chaves_proatlantico', 'home.gsdias.pt', 'chaves_geral', 'geral@2011');

//$email = new email();
//
//$email->setto('gsdias@gmail.com', 'Gonçalo to Assunção');
//$email->setfrom('gsdias@gmail.com', 'Gonçalo from Assunção');
//$email->setsubject('Gonçalo nome Assunção da Silva Dias');
//$email->setbody('Gonçalo body assunção');
//
//echo $email->sendmail();


$mysql->statement('SELECT * FROM users where data_nasc < :data LIMIT 0, 1', array(':data' => '10-01-1990'));


$tpl = class_exists('tpl') ? new tpl() : '';

define('TPLPATH', dirname(__FILE__) . '/tpl/');
define('TPLEXT', '.html');
define('DEBUG', 1);

$path = array(0 => 'test');

$tpl->includeFiles('MAIN', 'test');
$mysql->close();
$tpl->setFile('index');
$tpl->sendOut();
