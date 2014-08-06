<?php

if (@$_REQUEST['save']) {
    
    include_once(CLIENTPATH . 'include/admin/fields' . PHPEXT);

    $sectionextrafields = sprintf('%sfields', $path[1]);
    
    $sectionextrafields = function_exists($sectionextrafields) ? $sectionextrafields() : array();

    $defaultfields = array(
        $_REQUEST['email'],
        $_REQUEST['level'],
        $_REQUEST['name'],
        $user->id
    );
    $valuefields = array();
    $questions = str_repeat(", ?", sizeof($sectionextrafields['list']));

    foreach ($sectionextrafields['list'] as $field) {
        $valuefields[] = @$_REQUEST[$field];
    }

    $fields = array_merge($defaultfields, $valuefields);
    $mysql->statement(sprintf('INSERT INTO users (email, level, name, creator %s) values(?, ?, ?, ? %s);', $sectionextrafields['label'], $questions), $fields);

    if ($mysql->total) {
        header("Location: /admin/users", true, 302);
    }
}
