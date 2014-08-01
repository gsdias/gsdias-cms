<?php


if (is_file('gsd-install.php')) {
    define('IS_INSTALLED', 0);
} else {
    define('IS_INSTALLED', 1);
}

include_once('gsd-config.php');

$startpoint = 'index';

if (is_file('gsd-install' . PHPEXT)) {
    
    require_once('gsd-install' . PHPEXT);
    
} else {
    
    require_once('gsd-credentials' . PHPEXT);

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

$tpl->includeFiles('MAIN', @$main);
$tpl->setFile($startpoint);

echo $tpl;
