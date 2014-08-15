<?php

if (!IS_ADMIN) {
    header("Location: /admin/users", true, 302);
    exit;
}

if (@$_REQUEST['save']) {
    
    $.fields = $section . 'fields';

    $defaultfields = array('email', 'level', 'name');

    $extrafields = function_exists($.fields) ? $.fields() : array('list' => array());
    
    $password = substr(str_shuffle(sha1(rand() . time() . "gsdias-cms")), 2, 10);

    $fields = array_merge($defaultfields, @$extrafields['list']);
    
    $values = array();
    
    foreach ($fields as $field) {
        $values[] = $_REQUEST[$field];
    }

    $fields = array_merge($fields, array('creator', 'password'));

    $values = array_merge($values, array(
        $user->id,
        md5($password)
    ));
        
    $questions = str_repeat(", ? ", sizeof($fields));

    $mysql->statement(sprintf('INSERT INTO users (%s) values(%s);', implode(', ', $fields), substr($questions, 2)), $values);
    
    if ($mysql->errnum) {
        $tpl->setvar('ERRORS', 'Já existe um utilizador com esse email.');
        $tpl->setcondition('ERRORS');

    } else {
        $email = new email();
        
        $email->setto($_REQUEST['email']);
        $email->setfrom($site->email);
        $email->setreplyto($site->email);
        $email->setsubject('Registo no site');
        $email->setbody(sprintf('Foi criado um registo com este email. Para poder aceder use o seu email e a password: ', $password));
        
        #$email->sendmail();        

        $_SESSION['message'] = 'Utilizador criado.';

        header("Location: /admin/users", true, 302);
        exit;
    }
}
