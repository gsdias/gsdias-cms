<?php

if (@$_REQUEST['save']) {
    
    foreach ($_REQUEST as $name => $value) {
        if (strpos($name, 'gsd-') !== false) {
            $mysql->statement('UPDATE options SET value = ? WHERE name = ?', array($value, $name));
        }
    }
    
    header("Location: /admin", true, 302);
}
