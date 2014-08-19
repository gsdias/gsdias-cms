<?php

if (!IS_ADMIN) {
    header("Location: /admin/users", true, 302);
    exit;
}

if (@$_REQUEST['save']) {
    
    $defaultfields = array('email', 'password', 'level', 'name');

    $fields = array('creator');

    $values = array($user->id);
    
    $_REQUEST['password'] = substr(str_shuffle(sha1(rand() . time() . "gsdias-cms")), 2, 10);
        
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
        $email->setbody(sprintf('Foi criado um registo com este email. Para poder aceder use o seu email e a password: ', $password));
        
        #$email->sendmail();        

        $_SESSION['message'] = '{LANG_USER_CREATED}';

        header("Location: /admin/users", true, 302);
        exit;
    }
}
