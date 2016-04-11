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
    redirect('/admin/'.$site->arg(1));
}

if (@$_REQUEST['save']) {
    $defaultfields = array('email', 'password', 'level', 'name', 'locale');

    $fields = array('creator');

    $values = array($user->id);

    $password = substr(str_shuffle(sha1(rand().time().'gsdias-cms')), 2, 10);

    $_REQUEST['password'] = $password;

    $result = $csection->add($defaultfields, $fields, $values);

    if ($result['errnum']) {
        $tpl->setvar('ERRORS', lang('LANG_USER_ALREADY_EXISTS'));
        $tpl->setcondition('ERRORS');
    } else {
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
