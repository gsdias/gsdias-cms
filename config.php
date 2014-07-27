<?php

include_once('settings.php');
include_once(CLASSPATH . 'email.class' . PHPEXT);
include_once(CLASSPATH . 'htmltags.class' . PHPEXT);
include_once(CLASSPATH . 'menu.class' . PHPEXT);
include_once(CLASSPATH . 'mysql.class' . PHPEXT);
include_once(CLASSPATH . 'notification.class' . PHPEXT);
include_once(CLASSPATH . 'template.class' . PHPEXT);
include_once(CLASSPATH . 'user.class' . PHPEXT);
include_once('functions' . PHPEXT);

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

$tpl = class_exists('tpl') ? new tpl() : '';

$_mysql = array();
$_mysql['host'] = $mysql['host'];
$_mysql['user'] = $mysql['user'];
$_mysql['pass'] = $mysql['pass'];
$_mysql['db'] = $mysql['db'];

$temp_root = explode('/', $_SERVER['PHP_SELF']);
$root = '';
foreach ($temp_root as $id => $path) {
    if ($id < (sizeof($temp_root)-1)) {
        $root .= $path . '/';
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

$mysql = class_exists('mySQL') ? new mySQL($_mysql['db'], $_mysql['host'], $_mysql['user'], $_mysql['pass']) : null;
