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
    header('Location: /admin/'.$site->arg(1), true, 302);
    exit;
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

        header('Location: /admin/'.$site->arg(1), true, 302);
        exit;
    }
}

$types = new GSD\select(array('list' => $languages, 'id' => 'LANGUAGE'));
$types->object();
