<?php

if (@$_REQUEST['save']) {
    
    foreach ($_REQUEST as $name => $value) {
        if (strpos('gsd-', $name) !== false) {
            $mysql->statement('UPDATE options SET value = ? WHERE name = "email"', array($_REQUEST['email']));
        }
    }
    
    header("Location: /admin", true, 302);
}
