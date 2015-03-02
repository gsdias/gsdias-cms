<?php

if (@$_REQUEST['save']) {

    $defaultfields = array('email', 'level', 'name', 'disabled', 'password');

    if (!@$_REQUEST['password']) {
        array_pop($defaultfields);
    }

    $_REQUEST['disabled'] = @$_REQUEST['disabled'] ? @$_REQUEST['disabled'] : null;

    $result = $csection->edit($defaultfields);
    
    if ($result['errnum'] == 1062) {

        $tpl->setvar('ERRORS', '{LANG_USER_ALREADY_EXISTS}');
        $tpl->setcondition('ERRORS');
    } else {

        $_SESSION['message'] = '{LANG_USER_SAVED}';

        header("Location: /admin/users", true, 302);
        exit;
    }
}
