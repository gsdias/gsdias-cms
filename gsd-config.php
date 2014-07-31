<?php

$path = isset($_SERVER['REQUEST_URI']) ? explode("/", $_SERVER['REQUEST_URI']) : '';

array_shift($path);
$path[0] = @$path[0];
$path[1] = @$path[1];
$path[2] = @$path[2];

include_once('gsd-settings.php');
include_once('gsd-functions' . PHPEXT);

/*
ACCESS LEVEL

ROOT          100
ADMIN_MODIFY   90
ORG            20
AVIAGENS       16
TRADUTOR       15
ADMIN_VIEW     10

*/

date_default_timezone_set('Europe/Lisbon');
 
// Next, register it with PHP.
spl_autoload_register('GSDClassLoading');

@session_start();

$tpl = new tpl();

$user = @$_SESSION['user'] ? $_SESSION['user'] : (class_exists('clientuser') ? new clientuser() : new user());

$mysql = new mysql($mysql['db'], $mysql['host'], $mysql['user'], $mysql['pass']);

$tpl->setpaths($config['tplpath']);

$temp_root = explode('/', $_SERVER['PHP_SELF']);
$root = '';
foreach ($temp_root as $id => $_path) {
    if ($id < (sizeof($temp_root) - 1)) {
        $root .= $_path . '/';
    }
}

$sitemail = $config['email'];
$pathsite = $config['url'];
$resources = $config['resources'];

/* Facebook information */
$fb = sprintf('var appId="%s", page="%s";', $config['facebook']['appid'], $config['facebook']['page']);
$fbconfig = array('appId' => $config['facebook']['appid'], 'secret' => $config['facebook']['secret']);

$tpl->setVar('SCRIPT', sprintf('server = "undefined" === typeof server ? { } : server;server.lang = "%s";%s', $config['lang'], $fb));
$tpl->setVar('TITLE', $config['title']);
$tpl->setVar('DESCRIPTION', $config['description']);
$tpl->setVar('KEYWORDS', $config['keywords']);
$tpl->setVar('CDN', $resources);
$tpl->setVar('WEBMASTER', $config['webmaster']);
$tpl->setVar('CLIENT_RESOURCES', $config['client_resources']);

if ($path[0] != 'admin' && is_file (CLIENTPATH . 'config' . PHPEXT)) {
    include_once(CLIENTPATH . 'config' . PHPEXT);
}
