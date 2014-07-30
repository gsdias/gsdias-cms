<?php

if (@$_REQUEST['login']) {

    if (filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL)) {

        $logged = $user->login($_REQUEST['email'], $_REQUEST['password']);
        
        if ($logged) {
            header('location: /');
        }
    } else {

    }

}

if ($user->isLogged()) {

    $tpl->setcondition('IS_LOGGED');
    define('IS_LOGGED', 1);
    
} else {
    define('IS_LOGGED', 0);
}
