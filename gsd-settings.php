<?php

$config['description'] = '';
$config['keywords'] = '';
$config['title'] = '';
$config['url'] = '';
$config['url_en'] = '';
$config['resources'] = '/gsd-resources';
$config['email'] = '';
$config['webmaster'] = '';
$config['lang'] = 'pt';
$config['client'] = 'Stock Iberico';

$config['facebook']['appid'] = '';
$config['facebook']['page'] = '';
$config['facebook']['secret'] = '';

$config['ga'] = '';

$_mysql['host'] = 'home.gsdias.pt';

$_mysql['user'] = 'root';
$_mysql['pass'] = '12348876';
$_mysql['db'] = 'cmss';

$_mysql['user'] = 'corporat_iberica';
$_mysql['pass'] = 'UTFFvV@%WaGG';
$_mysql['db'] = 'corporat_iberica';

define('DEBUG', 0);
define('TPLEXT', '.html');
define('PHPEXT', '.php');
define('TPLPATH', dirname(__FILE__) . '/gsd-tpl/');
define('CLASSPATH', dirname(__FILE__) . '/gsd-class/');

define('CLIENTPATH', dirname(__FILE__) . '/gsd-client/');
define('CLIENTINCLUDEPATH', 'include/');

define('MAINTENANCE', 0);

//error_reporting(1);

$config['tplpath'] = array(
    TPLPATH . '%s' . TPLEXT,
    TPLPATH . '%s/%s' . TPLEXT,
    TPLPATH . '_shared/%s' . TPLEXT,
    TPLPATH . '_editable/%s' . TPLEXT,
    TPLPATH . '_editable/%s/%s' . TPLEXT
);

if ($path[0] != 'admin' && is_file (CLIENTPATH . 'settings' . PHPEXT) && IS_INSTALLED) {
    include_once(CLIENTPATH . 'settings' . PHPEXT);
}
