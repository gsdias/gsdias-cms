<?php

if (@$_REQUEST['save']) {
    
    $mysql->statement('UPDATE options SET value = ? WHERE name = "email"', array($_REQUEST['email']));
    $mysql->statement('UPDATE options SET value = ? WHERE name = "name"', array($_REQUEST['name']));
    $mysql->statement('UPDATE options SET value = ? WHERE name = "ga"', array($_REQUEST['ga']));
    
    header("Location: /admin", true, 302);
}
