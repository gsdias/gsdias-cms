<?php

if (@$_REQUEST['save']) {
    
    $defaultfields = array(
        $_REQUEST['title'],
        $_REQUEST['description'],
        $_REQUEST['tags'],
        $_REQUEST['keywords'],
        $_REQUEST['og_title'],
        $_REQUEST['og_description'],
        $_REQUEST['og_image']
    );
    $valuefields = array();
    $sqlfields = '';

    foreach ($sectionextrafields['list'] as $field) {
        $valuefields[] = $_REQUEST[$field];
        $sqlfields .= sprintf(', %s = ?', $field, $field);
    }

    $fields = array_merge($defaultfields, $valuefields);

    array_push($fields, $path[2]);
    
    $mysql->statement(sprintf('UPDATE pages SET title = ?, description = ?, tags = ?, keywords = ?, og_title = ?, og_description = ?, og_image = ? %s WHERE pid = ?;', $sqlfields), $fields);
    header("Location: /admin/pages", true, 302);
}
