<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.3
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
if (!$csection->permission) {
    $_SESSION['error'] = lang('LANG_USER_NOPERMISSION');
    header('Location: /admin/'.$site->arg(1), true, 302);
    exit;
}

if (@$_REQUEST['save']) {
    $defaultfields = array('email', 'level', 'name', 'locale', 'disabled', 'password');

    if (!@$_REQUEST['password']) {
        array_pop($defaultfields);
    }

    $_REQUEST['disabled'] = @$_REQUEST['disabled'] ? @$_REQUEST['disabled'] : null;

    $result = $csection->edit($defaultfields);

    if ($result['errnum'] == 1062) {
        $tpl->setvar('ERRORS', lang('LANG_USER_ALREADY_EXISTS'));
        $tpl->setcondition('ERRORS');
    } else {
        if ($result['id'] == $user->id) {
            $user->locale = $_REQUEST['locale'];
        }

        $_SESSION['message'] = sprintf(lang('LANG_USER_SAVED'), $_REQUEST['name']);

        header('Location: /admin/'.$site->arg(1), true, 302);
        exit;
    }
}
