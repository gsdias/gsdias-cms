<?php

if (@$_REQUEST['save']) {
    
    $file = CLIENTPATH . 'include/admin/fields' . PHPEXT;

    if (is_file($file)) {
        include_once($file);
    }
    
    $extrafields = function_exists('usersfields') ? usersfields() : array();

    $defaultfields = array('email', 'level', 'name', 'disabled');
    
    $extrafieldslist = sizeof(@$extrafields['list']) ? $extrafields['list'] : array();
    
    $values = array();
    
    $fields = '';
    
    $allfields = array_merge($defaultfields, $extrafieldslist);
    
    foreach ($allfields as $field) {
        $fields .= sprintf(", %s = ?", $field);
        $values[] = @$_REQUEST[$field];
    }
        
    $values[] = $site->path[2];
    
    $mysql->statement(sprintf('UPDATE users SET %s WHERE uid = ?;', substr($fields, 2)), $values);
    
    if ($mysql->total) {
        header("Location: /admin/users", true, 302);
        exit;
    }
}
