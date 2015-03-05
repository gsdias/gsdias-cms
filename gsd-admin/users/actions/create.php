<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.0
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

if (!IS_ADMIN) {
    header("Location: /admin/users", true, 302);
    exit;
}

if (@$_REQUEST['save']) {
    
    $defaultfields = array('email', 'password', 'level', 'name', 'locale');

    $fields = array('creator');

    $values = array($user->id);
    
    $password = substr(str_shuffle(sha1(rand() . time() . "gsdias-cms")), 2, 10);

    $_REQUEST['password'] = $password;
        
    $result = $csection->add($defaultfields, $fields, $values);
    
    if ($result['errnum']) {
        $tpl->setvar('ERRORS', '{LANG_USER_ALREADY_EXISTS}');
        $tpl->setcondition('ERRORS');

    } else {
        $_SESSION['message'] = sprintf(_('LANG_USER_CREATED'), $_REQUEST['name']);

        header("Location: /admin/users", true, 302);
        exit;
    }
}

$types = new select( array ( 'list' => array('pt_PT' => 'Português', 'en_GB' => 'Inglês'), 'id' => 'LANGUAGE' ) );
$types->object();
