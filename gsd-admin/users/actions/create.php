<?php

if (@$_REQUEST['save']) {
    
    include_once(CLIENTPATH . 'include/admin/fields' . PHPEXT);
    
    $extrafields = function_exists('usersfields') ? usersfields() : array();
    
    $password = substr(str_shuffle(sha1(rand() . time() . "gsdias-cms")), 2, 10);

    $fields = array('email', 'password', 'level', 'name', 'creator');
    
    $values = array(
        $_REQUEST['email'],
        md5($password),
        $_REQUEST['level'],
        $_REQUEST['name'],
        $user->id
    );    
    
    foreach ($extrafields['list'] as $key => $field) {
        $fields[] = $extrafields['list'][$key];
        $values[] = @$_REQUEST[$field];
    }
        
    $questions = str_repeat(", ? ", sizeof($fields));

    $mysql->statement(sprintf('INSERT INTO users (%s) values(%s);', implode(', ', $fields), substr($questions, 2)), $values);
    
    if ($mysql->total) {
        $email = new email();
        
        $email->setto($_REQUEST['email']);
        $email->setfrom($site->email);
        $email->setreplyto($site->email);
        $email->setsubject('Registo no site');
        $email->setbody(sprintf('Foi criado um registo com este email. Para poder aceder use o seu email e a password: ', $password));
        
        $email->sendmail();        
        header("Location: /admin/users", true, 302);
    }
}
