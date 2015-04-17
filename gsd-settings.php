<?php

$pattern = '/(\?)(.*)/';
$uri = preg_replace($pattern, '', $_SERVER['REQUEST_URI']);

$path = explode("/", $uri);
array_shift($path);

$config['resources'] = '/gsd-resources';
$config['url'] = 'localhost';
$_mysql['host'] = '127.0.0.1';
$_mysql['user'] = '';
$_mysql['pass'] = '';
$_mysql['db'] = '';

define('DEBUG', 0);
define('TPLEXT', '.html');
define('PHPEXT', '.php');
define('TPLPATH', dirname(__FILE__) . '/gsd-tpl/');
define('CLASSPATH', dirname(__FILE__) . '/gsd-class/');
define('INCLUDEPATH', dirname(__FILE__) . '/gsd-include/');
define('ASSETPATH', dirname(__FILE__) . '/gsd-assets/');
define('CLIENTPATH', dirname(__FILE__) . '/gsd-frontend/');
define('ASSETPATHURL', '/gsd-frontend/assets/');
define('CLIENTINCLUDEPATH', CLIENTPATH . 'include/');
define('CLIENTTPLPATH', CLIENTPATH . 'tpl/');
define('CLIENTCLASSPATH', CLIENTPATH . 'class/');
define('MAINTENANCE', 0);

error_reporting(E_ALL);

$config['tplpath'] = array(
    CLIENTTPLPATH . 'admin/%s' . TPLEXT,
    CLIENTTPLPATH . 'admin/%s/%s' . TPLEXT,
    TPLPATH . '%s' . TPLEXT,
    TPLPATH . '%s/%s' . TPLEXT,
    TPLPATH . '_shared/%s' . TPLEXT,
    TPLPATH . '_modules/%s' . TPLEXT,
    TPLPATH . '_editable/%s' . TPLEXT,
    TPLPATH . '_editable/%s/%s' . TPLEXT
);

$tables = array(
    'options' => 1,
    'moduletypes' => 1,
    'layouttypes' => 1,
    'layouts' => 1,
    'layoutsections' => 1,
    'layoutsectionmoduletypes' => 1,
    'pagemodules' => 1,
    'users' => 1,
    'pages' => 1,
    'pages_review' => 1,
    'redirect' => 1,
    'images' => 1,
    'documents' => 1,
    'emails' => 1
);

$languages = array(
    array('pt_PT' => 'Português'),
    array('en_GB' => 'English'),
    array('fr_FR' => 'Français')
);

if ($path[0] != 'admin' && is_file (CLIENTPATH . 'settings' . PHPEXT) && IS_INSTALLED) {
    include_once(CLIENTPATH . 'settings' . PHPEXT);
}