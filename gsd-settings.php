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
define('TPLPATH', dirname(__FILE__) . '/gsd-tpl/');
define('CLASSPATH', dirname(__FILE__) . '/gsd-class/');
define('CLIENTPATH', dirname(__FILE__) . '/gsd-client/htdocs');

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

if (is_file (CLIENTPATH . 'settings' . PHPEXT)) {
    include_once(CLIENTPATH . 'settings' . PHPEXT);
}