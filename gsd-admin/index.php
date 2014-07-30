<?php

print_r($_SESSION['user']);

if (!$user->isLogged()) {

    $startpoint = 'admin/login';
    if (@$_REQUEST['login']) {

        if (filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL)) {

            $user->login($_REQUEST['email'], $_REQUEST['password']);
            print_r($_SESSION['user']);
        } else {

        }

    }
} else {

}
