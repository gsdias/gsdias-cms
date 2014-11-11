<?php

// Set language to German
putenv('LC_ALL=pt_PT');
setlocale(LC_ALL, 'pt_PT');

// Specify location of translation tables
bindtextdomain("gsdias", "./locale");

// Choose domain
textdomain("gsdias");

// Translation is looking for in ./locale/pt_PT/LC_MESSAGES/gsdias.po now

// Or use the alias _() for gettext()
echo _("My name is %s.\n");




define('ROOTPATH', dirname(__FILE__) . '/');

if (is_file('gsd-install.php')) {
    define('IS_INSTALLED', 0);
} else {
    define('IS_INSTALLED', 1);
}

include_once('gsd-include/gsd-config.php');

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
