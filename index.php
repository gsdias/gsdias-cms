<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.0
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

define('ROOTPATH', dirname(__FILE__) . '/');

define('IS_INSTALLED', !is_file('gsd-install.php'));

include_once('gsd-include/gsd-config.php');

$tpl->setvar('HTML_CLASS', 'gsd');

if (is_file('gsd-install' . PHPEXT)) {
    $site->main = 'STEP1';
    require_once('gsd-install' . PHPEXT);
    
} elseif ($site->arg(0) == 'gsd-assets') {
    require_once('gsd-assets' . PHPEXT);
} else {

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
