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
    
    $defaultfields = array('email', 'password', 'level', 'name');

    $fields = array('creator');

    $values = array($user->id);
    
    $password = substr(str_shuffle(sha1(rand() . time() . "gsdias-cms")), 2, 10);

    $_REQUEST['password'] = md5($password);
        
    $result = $csection->add($defaultfields, $fields, $values);
    
    if ($result['errnum']) {
        $tpl->setvar('ERRORS', '{LANG_USER_ALREADY_EXISTS}');
        $tpl->setcondition('ERRORS');

    } else {
        $email = new email();
        
        $email->setto($_REQUEST['email']);
        $email->setfrom($site->email);
        $email->setreplyto($site->email);
        $email->setsubject('Registo no site');
        $email->setbody(sprintf('Foi criado um registo com este email. Para poder aceder use o seu email e a password: %s', $password));
        
        $email->setvar('PASSWORD', $password);
        $email->setvar('SUBDOMAIN', $password);

        $email->sendmail();

        $_SESSION['message'] = sprintf($lang[$config['lang']]['LANG_USER_CREATED'], $_REQUEST['name']);

        header("Location: /admin/users", true, 302);
        exit;
    }
}
