<?php

include_once('gsd-config.php');

if (file_exists('gsd-install' . PHPEXT)) {
    require_once('gsd-install.php');
}

if ($path[0] == 'admin') {
    require_once('gsd-admin/index.php');
}


$tpl->includeFiles('MAIN', $main);
$tpl->setFile('INDEX');

echo $tpl;