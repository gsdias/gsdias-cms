<?php

$config['description'] = '';
$config['keywords'] = '';
$config['title'] = '';
$config['url'] = '';
$config['url_en'] = '';
$config['resources'] = '/resources';
$config['email'] = '';
$config['webmaster'] = '';
$config['lang'] = 'pt';

$config['facebook']['appid'] = '';
$config['facebook']['page'] = '';
$config['facebook']['secret'] = '';

$config['ga'] = '';

$mysql['host'] = 'home.gsdias.pt';

$mysql['user'] = 'cms';
$mysql['pass'] = 'cms';
$mysql['db'] = 'cms';

define('DEBUG', 1);
define('TPLEXT', '.html');
define('PHPEXT', '.php');
define('TPLPATH', dirname(__FILE__) . '/tpl/');
define('CLASSPATH', dirname(__FILE__) . '/class/');
define('COREPATH', '');

define('MAINTENANCE', 0);

error_reporting(1);


$config['tplpath'] = array(
    TPLPATH . '%s' . TPLEXT,
    TPLPATH . '%s/%s' . TPLEXT,
    TPLPATH . '_shared/%s' . TPLEXT,
    COREPATH . 'tpl/%s' . TPLEXT,
    COREPATH . 'tpl/%s/%s' . TPLEXT,
    TPLPATH . '_editable/%s' . TPLEXT,
    TPLPATH . '_editable/%s/%s' . TPLEXT
);