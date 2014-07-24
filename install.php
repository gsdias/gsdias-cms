<?php

include_once('settings.php');
include_once('config' . PHPEXT);

$mysql->statement('SHOW TABLES;');

$table['users'] = 1;

if ($mysql->total) {

    foreach($mysql->result() as $table) {
        print_r($table[0]);
        if ($table[0] == 'users') {
            $table['users'] = 0;
        }
        flush();
    }
}

if ($table['users']) {
    $mysql->statement(file_get_contents('sql/users.sql'));
}
$tpl->sendout();
