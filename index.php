<?php


if (is_file('gsd-install.php')) {
    define('IS_INSTALLED', 0);
} else {
    define('IS_INSTALLED', 1);
}

$startpoint = 'index';
$main = '';

include_once('gsd-include/gsd-config.php');

if (is_file('gsd-install' . PHPEXT)) {
    $main = 'STEP1';
    require_once('gsd-install' . PHPEXT);
    
} elseif ($path[0] == 'gsd-assets') {
    require_once('gsd-assets' . PHPEXT);
} else {
    
    require_once(INCLUDEPATH . 'gsd-credentials' . PHPEXT);

    if ($path[0] == 'admin') {
        require_once('gsd-admin/index.php');
    }
    if ($path[0] == 'logout') {
        $user->logout();
    }

    if (is_file('gsd-client/index.php') && $path[0] != 'admin') {
        require_once('gsd-client/index.php');
    }
}

$tpl->includeFiles('MAIN', $main);
$tpl->setFile($startpoint);

echo $tpl;
