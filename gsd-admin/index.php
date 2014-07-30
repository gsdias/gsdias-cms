<?php

print_r($user);
if (!$user->isLogged()) {
    $startpoint = 'admin/login';
} else {

    if (@$_REQUEST['login']) {
        $user = new clientuser();

        if (filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL)) {

            $user->login($_REQUEST['email'], $_REQUEST['password']);
            
        } else {

        }

    }
}