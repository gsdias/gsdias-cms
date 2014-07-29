<?php

include_once('config.php');

if (file_exists('install' . PHPEXT)) {
    require_once('install.php');
}

if ($path[0] == 'admin') {
    require_once('admin/admin.php');
}


$tpl->includeFiles('MAIN', $main);
$tpl->setFile('INDEX');

echo $tpl;