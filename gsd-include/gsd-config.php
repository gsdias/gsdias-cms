<?php

include_once('gsd-settings.php');
include_once('gsd-class/interfaces' . PHPEXT);
include_once(INCLUDEPATH . 'gsd-lang' . PHPEXT);
include_once(INCLUDEPATH . 'gsd-functions' . PHPEXT);
include_once(INCLUDEPATH . 'gsd-paginator' . PHPEXT);

date_default_timezone_set('Europe/Lisbon');
 
// Next, register it with PHP.
spl_autoload_register('GSDClassLoading');

@session_start();

$mysql = new mysql($_mysql['db'], $_mysql['host'], $_mysql['user'], $_mysql['pass']);

$tpl = new tpl();

$site = new site();

$user = @$_SESSION['user'] ? $_SESSION['user'] : (class_exists('clientuser') ? new clientuser() : new user());

$tpl->setpaths($config['tplpath']);

$resources = $config['resources'];

$tpl->setvars($lang[$config['lang']]);

$tpl->setVar('SCRIPT', sprintf('server = "undefined" === typeof server ? { } : server;server.lang = "%s";server.ga = "%s"', $config['lang'], $site->ga));
$tpl->setVar('CDN', $resources);
$tpl->setVar('CLIENT_RESOURCES', @$config['client_resources']);
$tpl->setVar('REDIRECT', @$_REQUEST['redirect'] ? sprintf("?redirect=%s", $_REQUEST['redirect']) : '');

if ($path[0] != 'admin' && is_file (CLIENTPATH . 'config' . PHPEXT) && IS_INSTALLED) {
    include_once(CLIENTPATH . 'config' . PHPEXT);
}
