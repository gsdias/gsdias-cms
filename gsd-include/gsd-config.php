<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
defined('GVALID') or die;
include_once ROOTPATH.'gsd-include/gsd-defines.php';
include_once ROOTPATH.'gsd-settings'.PHPEXT;
if ($path[0] != 'admin' && is_file(CLIENTPATH.'settings'.PHPEXT)) {
    include_once CLIENTPATH.'settings'.PHPEXT;
}
include_once ROOTPATH.'gsd-class/interfaces'.PHPEXT;
include_once INCLUDEPATH.'gsd-functions'.PHPEXT;

date_default_timezone_set('Europe/Lisbon');

// Next, register it with PHP.
spl_autoload_register('GSDClassLoading');

@session_start();

$GSDConfig = new GSDConfig;

$tpl = new GSD\tpl();

$mysql = GSD\mysqlFactory::create($GSDConfig->mysql['db'], $GSDConfig->mysql['host'], $GSDConfig->mysql['user'], $GSDConfig->mysql['pass']);

$site = new GSD\site();

$user = $site->p('user', 1) ? $site->p('user', 1) : (class_exists('\\GSD\\Extended\\extendeduser') ? new GSD\Extended\extendeduser() : new GSD\user());

$language = getLanguage();

$tpl->setvar('LANG', explode('_', $language)[0]);

$folder = 'gsd-locale';
$domain = 'messages';
$encoding = 'UTF-8';

clearstatcache();
putenv('LANG='.$language.'.'.$encoding);
setlocale(LC_ALL, $language.'.'.$encoding);

clearstatcache();
if (function_exists('bindtextdomain')) {
    bindtextdomain($domain, ROOTPATH.$folder);
    bind_textdomain_codeset($domain, $encoding);

    textdomain($domain);
    if (is_dir(CLIENTPATH.'locale') && is_dir(CLIENTPATH.'locale/'.$language)) {
        bindtextdomain('extended', CLIENTPATH.'locale');
        bind_textdomain_codeset('extended', $encoding);
    } else {
        $tpl->setarray('WARNINGS', array('MSG' => lang('LANG_MISSING_EXTENDED_LANGUAGE')));
        $tpl->setcondition('WARNINGS');
    }
}

$tpl->setpaths($config['tplpath']);

$tpl->setVar('SCRIPT', sprintf('GSD.locale = "%s";GSD.ga = "%s";GSD.fb = "%s";GSD.gtm = "%s";GSD.App = { isCMS: %s };', $language, $site->options['ga']->value, $site->options['fb']->value, $site->options['gtm']->value, !$site->isFrontend ? 1 : 0));
$tpl->setVar('GTM', $site->options['gtm']->value);
$tpl->setcondition('GTM', !!$site->options['gtm']->value);
$tpl->setcondition('FB', !!$site->options['fb']->value);
$tpl->setcondition('GA', !!$site->options['ga']->value && !$site->options['gtm']->value);
$tpl->setVar('CDN', RESOURCESURL);
$tpl->setVar('RESOURCESURL', RESOURCESURL);
$tpl->setVar('CLIENT_RESOURCES', @$config['client_resources']);
$tpl->setVar('CLIENT_PATH', @$config['client_path']);
$tpl->setVar('ASSETPATH', ASSETPATHURL);
$tpl->setVar('REDIRECT', $site->p('redirect') ? sprintf('?redirect=%s', $site->p('redirect')) : '');
$tpl->setVar('SITE_URL', $site->protocol.@$GSDConfig->url);

displaymessages('ERRORS', $site->p('ERRORS', 1));
displaymessages('MESSAGES', $site->p('MESSAGES', 1));

$_SESSION['ERRORS'] = array();
$_SESSION['MESSAGES'] = array();

if (!$site->isFrontend) {
    $section = @$site->a(1) ? lang('LANG_'.strtoupper($site->a(1))) : lang('LANG_DASHBOARD');
    $tpl->setvars(array(
        'PAGE_TITLE' => sprintf('%s - %s', $site->name, ucwords($section)),
        'PAGE_CANONICAL' => $site->protocol.$_SERVER['HTTP_HOST'].$site->uri,
    ));
}

$frontendindex = is_file(ROOTPATH.'gsd-frontend/index.php') ? ROOTPATH.'gsd-frontend/index.php' : '';

if (IS_INSTALLED && $site->a(0) != 'admin' && is_file(CLIENTPATH.'config'.PHPEXT)) {
    include_once CLIENTPATH.'config'.PHPEXT;
}
