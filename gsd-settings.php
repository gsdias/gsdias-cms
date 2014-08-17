<?php

$pattern = '/(\?)(.*)/';
$uri = preg_replace($pattern, '', $_SERVER['REQUEST_URI']);
    
$path = explode("/", $uri);
array_shift($path);

$config['resources'] = '/gsd-resources';
$config['lang'] = 'pt';

$_mysql['host'] = 'home.gsdias.pt';

$_mysql['user'] = 'gsdias';
$_mysql['pass'] = '12348876';
$_mysql['db'] = 'portfolio';

define('DEBUG', 0);
define('TPLEXT', '.html');
define('PHPEXT', '.php');
define('TPLPATH', dirname(__FILE__) . '/gsd-tpl/');
define('CLASSPATH', dirname(__FILE__) . '/gsd-class/');
define('INCLUDEPATH', dirname(__FILE__) . '/gsd-include/');
define('ASSETPATH', dirname(__FILE__) . '/gsd-assets/');

define('CLIENTPATH', dirname(__FILE__) . '/gsd-client/');
define('CLIENTINCLUDEPATH', 'include/');
define('CLIENTTPLPATH', 'tpl/');

define('MAINTENANCE', 0);

error_reporting(E_ALL);

$config['tplpath'] = array(
    TPLPATH . '%s' . TPLEXT,
    TPLPATH . '%s/%s' . TPLEXT,
    TPLPATH . '_shared/%s' . TPLEXT,
    TPLPATH . '_editable/%s' . TPLEXT,
    TPLPATH . '_editable/%s/%s' . TPLEXT,
    CLIENTPATH . CLIENTTPLPATH . 'admin/%s' . TPLEXT,
    CLIENTPATH . CLIENTTPLPATH . 'admin/%s/%s' . TPLEXT
);

$tables = array(
    'options' => 1,
    'users' => 1,
    'pages' => 1,
    'redirect' => 1,
    'images' => 1,
    'documents' => 1
);

if ($path[0] != 'admin' && is_file (CLIENTPATH . 'settings' . PHPEXT) && IS_INSTALLED) {
    include_once(CLIENTPATH . 'settings' . PHPEXT);
}
