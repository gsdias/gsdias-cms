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
    $fields = array(
        array('name', array('isRequired', 'isString')),
        array('email', array('isRequired', 'isEmail')),
        array('password', array('isPassword')),
        array('level', array('isRequired', 'isString')),
        array('locale', array('isString')),
        array('creator', array('isNumber')),
    );

    $result = $csection->add($fields);

    if (!$csection->showErrors(lang('LANG_USER_ALREADY_EXISTS'))) {
        $_SESSION['message'] = sprintf(lang('LANG_USER_CREATED'), $_REQUEST['name']);

        redirect('/admin/'.$site->arg(1));
    }
}

$types = new GSD\select(array('list' => $languages, 'id' => 'LANGUAGE'));
$types->object();

$permissionsfield = new GSD\select(array(
    'list' => $permissions,
    'label' => lang('LANG_PERMISSION'),
    'name' => 'level',
));
$tpl->setvar('PERMISSION', $permissionsfield);
