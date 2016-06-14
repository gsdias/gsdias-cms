<?php
defined('GVALID') or die;

class GSDConfig {
    public $url = 'localhost';

    public $mysql = array(
        'host' => '127.0.0.1',
        'user' => '',
        'pass' => '',
        'db' => ''
    );

    public $email = array(
        'host' => 'localhost',
        'port' => 25,
        'user' => '',
        'pass' => ''
    );

    public $tables = array(
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
        'pages_extra' => 1,
        'redirect' => 1,
        'images' => 1,
        'documents' => 1,
        'emails' => 1,
    );

    public $languages = array(
        '' => 'LANG_CHOOSE',
        'de_DE' => 'Deutsch',
        'en_GB' => 'English',
        'es_ES' => 'Español',
        'fr_FR' => 'Français',
        'pt_PT' => 'Português',
    );

    public $permissions = array(
        'admin' => 'admin',
        'editor' => 'editor',
        'user' => 'user',
    );

    public $numberPerPage = array(10 => 10, 20 => 20, 25 => 25, 50 => 50, 100 => 100);
}


$pattern = '/(\?)(.*)/';
$uri = preg_replace($pattern, '', $_SERVER['REQUEST_URI']);

$path = explode('/', $uri);
array_shift($path);

define('TPLEXT', '.html');
define('PHPEXT', '.php');
define('ADMINPATH', ROOTPATH.'gsd-admin/');
define('TPLPATH', ROOTPATH.'gsd-tpl/');
define('CLASSPATH', ROOTPATH.'gsd-class/');
define('INCLUDEPATH', ROOTPATH.'gsd-include/');
define('ASSETPATH', ROOTPATH.'gsd-frontend/assets/');
define('CLIENTPATH', ROOTPATH.'gsd-frontend/');
define('RESOURCESURL', '/gsd-resources');
define('ASSETPATHURL', '/gsd-frontend/assets/');
define('CLIENTINCLUDEPATH', CLIENTPATH.'include/');
define('CLIENTTPLPATH', CLIENTPATH.'tpl/');
define('CLIENTCLASSPATH', CLIENTPATH.'class/');

error_reporting(E_ALL);

$config['tplpath'] = array(
    CLIENTTPLPATH.'admin/%s'.TPLEXT,
    CLIENTTPLPATH.'admin/%s/%s'.TPLEXT,
    TPLPATH.'%s'.TPLEXT,
    TPLPATH.'%s/%s'.TPLEXT,
    TPLPATH.'_shared/%s'.TPLEXT,
    TPLPATH.'_modules/%s'.TPLEXT,
);
