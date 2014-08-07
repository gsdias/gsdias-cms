<?php

if (@$_REQUEST['save']) {
    
    include_once(CLIENTPATH . 'include/admin/fields' . PHPEXT);
    
    $extrafields = function_exists('usersfields') ? usersfields() : array();

    $fields = array('email', 'level', 'name', 'creator');
    
    $values = array(
        $_REQUEST['email'],
        $_REQUEST['level'],
        $_REQUEST['name'],
        $user->id
    );    
    
    foreach ($sectionextrafields['list'] as $key => $field) {
        $fields[] = $sectionextrafields['list'][$key];
        $values[] = @$_REQUEST[$field];
    }
        
    $questions = str_repeat(", ? ", sizeof($fields));

    $mysql->statement(sprintf('INSERT INTO users (%s) values(%s);', implode(', ', $fields), substr($questions, 2)), $values);
    
    if ($mysql->total) {
        header("Location: /admin/users", true, 302);
    }
}
