<?php

include_once('gsd-config.php');

$startpoint = 'index';

if (is_file('gsd-install' . PHPEXT)) {
    require_once('gsd-install' . PHPEXT);
}

require_once('gsd-credentials' . PHPEXT);

if ($path[0] == 'admin') {
    require_once('gsd-admin/index.php');
}
if ($path[0] == 'logout') {
    $user->logout();
}

<<<<<<< HEAD
if (file_exists('gsd-client/htdocs/index' . PHPEXT)) {
    require_once('gsd-client/htdocs/index' . PHPEXT);
=======
if (is_file('gsd-client/index.php')) {
    require_once('gsd-client/index.php');
>>>>>>> 203079ab37844e24cf7ff6dd58a5f3a14a074bb2
}

$tpl->includeFiles('MAIN', $main);
$tpl->setFile($startpoint);

echo $tpl;
