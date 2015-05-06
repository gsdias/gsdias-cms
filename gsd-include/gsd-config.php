<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.2
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

include_once(ROOTPATH . 'gsd-settings.php');
include_once(ROOTPATH . 'gsd-class/interfaces' . PHPEXT);
include_once(INCLUDEPATH . 'gsd-functions' . PHPEXT);

date_default_timezone_set('Europe/Lisbon');
 
// Next, register it with PHP.
spl_autoload_register('GSDClassLoading');

@session_start();

$mysql = mysqlFactory::create($_mysql['db'], $_mysql['host'], $_mysql['user'], $_mysql['pass']);

$tpl = new tpl(DEBUG);

$site = new site();

$user = @$_SESSION['user'] ? $_SESSION['user'] : (class_exists('clientuser') ? new clientuser() : new user());

$language = getLanguage();

$folder = 'gsd-locale';
$domain = 'messages';
$encoding = 'UTF-8';

clearstatcache();
putenv('LANG=' . $language);
setlocale(LC_ALL, $language);

clearstatcache ();
if (function_exists('bindtextdomain')) {
    bindtextdomain($domain, ROOTPATH . $folder);
    bind_textdomain_codeset($domain, $encoding);

    textdomain($domain);
    if (is_dir(CLIENTPATH . 'locale')) {
        bindtextdomain('frontend', CLIENTPATH . 'locale');
        bind_textdomain_codeset('frontend', $encoding);
    }
}

$tpl->setpaths($config['tplpath']);

$resources = $config['resources'];

$tpl->setVar('SCRIPT', sprintf('GSD.ga = "%s";GSD.fb = "%s";GSD.App = { isCMS: %s };', $site->ga, $site->fb, !$site->isFrontend ? 1 : 0));
$tpl->setVar('CDN', $resources);
$tpl->setVar('CLIENT_RESOURCES', @$config['client_resources']);
$tpl->setVar('CLIENT_PATH', @$config['client_path']);
$tpl->setVar('ASSETPATH', ASSETPATHURL);
$tpl->setVar('REDIRECT', @$_REQUEST['redirect'] ? sprintf('?redirect=%s', $_REQUEST['redirect']) : '');

if (!$site->isFrontend) {
    $section = lang('LANG_' . strtoupper(@$site->arg(1)));
    $tpl->setvars(array(
        'PAGE_TITLE' => sprintf('%s - %s', $site->name, ucwords($section == 'LANG_' ? lang('LANG_DASHBOARD') : $section)),
        'PAGE_CANONICAL' => $site->protocol . $_SERVER['HTTP_HOST'] . '/' . $site->uri
    ));
}

$frontendindex = is_file(ROOTPATH . 'gsd-frontend/index.php') ? ROOTPATH . 'gsd-frontend/index.php' : '';

if ($site->arg(0) != 'admin' && is_file (CLIENTPATH . 'config' . PHPEXT) && IS_INSTALLED) {
    include_once(CLIENTPATH . 'config' . PHPEXT);
}
