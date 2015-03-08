<?php

$language = 'pt_PT';

$folder = "locale";
$domain = "messages";
$encoding = "UTF-8";

clearstatcache();
putenv("LANG=" . $language);
setlocale(LC_ALL, $language);

if (function_exists('bindtextdomain')) {
    bindtextdomain($domain, './locale/nocache');
    bindtextdomain($domain, $folder);
    bind_textdomain_codeset($domain, $encoding);

    textdomain($domain);

    if (is_dir('gsd-client/locale')) {
        bindtextdomain('client', 'gsd-client/' . $folder);
        bind_textdomain_codeset('client', $encoding);
    }
}

define('ROOTPATH', dirname(__FILE__) . '/');

define('IS_INSTALLED', !is_file('gsd-install.php'));

include_once('gsd-include/gsd-config.php');

if (is_file('gsd-install' . PHPEXT)) {
    $site->main = 'STEP1';
    require_once('gsd-install' . PHPEXT);
    
} elseif ($site->arg(0) == 'gsd-assets') {
    require_once('gsd-assets' . PHPEXT);
} else {
    
    $tpl->setvar('HTML_CLASS', 'gsd');

    require_once(INCLUDEPATH . 'gsd-credentials' . PHPEXT);

    if ($site->arg(0) == 'admin') {
        require_once('gsd-admin/index.php');
    }
    if ($site->arg(0) == 'logout') {
        $user->logout();
    }

    if (is_file(ROOTPATH . 'gsd-client/index.php') && $site->arg(0) != 'admin') {
        if (!IS_LOGGED && $site->page['require_auth']) {
            header('location: /login?redirect=' . urlencode($site->uri));
            exit;
        }
        require_once(ROOTPATH . 'gsd-client/index.php');
    }
    if (@$_SESSION['error']) {
        $tpl->setvar('ERRORS', $_SESSION['error']);
        $tpl->setcondition('ERRORS');
        unset($_SESSION['error']);
    }
    if (@$_SESSION['message']) {
        $tpl->setvar('MESSAGES', $_SESSION['message']);
        $tpl->setcondition('MESSAGES');
        unset($_SESSION['message']);
    }
}

$tpl->includeFiles('MAIN', $site->main);
$tpl->setFile($site->startpoint);

echo $tpl;
