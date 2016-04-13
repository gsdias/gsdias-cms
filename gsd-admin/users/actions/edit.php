<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.4
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
if (!$csection->permission) {
    $_SESSION['error'] = lang('LANG_USER_NOPERMISSION');
    redirect('/admin/'.$site->arg(1));
}

if (@$_REQUEST['save']) {
    $defaultfields = array(
        'email',
        'level',
        array('name', array('isString')),
        'locale',
        array('disabled', array('isCheckbox')),
        array('password', array('isPassword')),
    );

    if (!@$_REQUEST['password']) {
        array_pop($defaultfields);
    }

    $result = $csection->edit($defaultfields);

    if ($result['errnum'] == 1062) {
        $tpl->setvar('ERRORS', lang('LANG_USER_ALREADY_EXISTS'));
        $tpl->setcondition('ERRORS');
    } else {
        if ($result['id'] == $user->id) {
            $user->locale = $_REQUEST['locale'];
        }

        $_SESSION['message'] = sprintf(lang('LANG_USER_SAVED'), $_REQUEST['name']);

        redirect('/admin/'.$site->arg(1));
    }
}
