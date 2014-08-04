<?php

if (@$_REQUEST['save']) {
    $defaultfields = array(
        $_REQUEST['email'],
        $_REQUEST['name']
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
        sprintf('UPDATE users SET email = ?, name = ? %s WHERE uid = ?;', $sqlfields),
        $fields);

    if ($mysql->total) {
        header("Location: /admin/users", true, 302);
    }
}
