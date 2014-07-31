<?php

include_once('gsd-config.php');

$startpoint = 'index';

if (file_exists('gsd-install' . PHPEXT)) {
    require_once('gsd-install' . PHPEXT);
}

require_once('gsd-credentials' . PHPEXT);

if ($path[0] == 'admin') {
    require_once('gsd-admin/index.php');
}
if ($path[0] == 'logout') {
    $user->logout();
}

if (file_exists('gsd-client/htdocs/index' . PHPEXT)) {
    require_once('gsd-client/htdocs/index' . PHPEXT);
}

$tpl->includeFiles('MAIN', $main);
$tpl->setFile($startpoint);

echo $tpl;
