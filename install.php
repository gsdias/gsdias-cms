<?php

include_once('settings.php');
include_once('config' . PHPEXT);

$mysql->statement('SHOW TABLES;');

if ($mysql->total) {

    foreach($mysql->result() as $table) {
        print_r($table[0]);
        flush();
    }
}

$tpl->sendout();