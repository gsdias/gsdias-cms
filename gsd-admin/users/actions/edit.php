<?php

if (@$_REQUEST['save']) {
    $sectionextrafields = function_exists('usersfields') ? usersfields() : array();

    $defaultfields = array(
        $_REQUEST['email'],
        $_REQUEST['name'],
        $_REQUEST['level']
    );
    $valuefields = array();
    $sqlfields = '';

    foreach ($sectionextrafields['list'] as $field) {
        $valuefields[] = $_REQUEST[$field];
        $sqlfields .= sprintf(', %s = ?', $field, $field);
    }

    $fields = array_merge($defaultfields, $valuefields);

    array_push($fields, $path[2]);
    $mysql->statement(
        sprintf('UPDATE users SET email = ?, name = ?, level = ? %s WHERE uid = ?;', $sqlfields),
        $fields);

    if ($mysql->total) {
        header("Location: /admin/users", true, 302);
    }
}
