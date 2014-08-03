<?php

include_once(CLIENTPATH . 'include/admin/fields' . PHPEXT);

$extrafields = userfields();

if (@$_REQUEST['save']) {
    $defaultfields = array(
        $_REQUEST['email'],
        $_REQUEST['password'],
        $_REQUEST['name'],
        $user->id
    );
    $valuefields = array();
    $questions = str_repeat(", ?", sizeof($extrafields['list']));

    foreach ($extrafields['list'] as $field) {
        $valuefields[] = $_REQUEST[$field];
    }

    $fields = array_merge($defaultfields, $valuefields);
    $mysql->statement(sprintf('INSERT INTO users (email, password, name, level, creator %s) values(?, ?, ?, 1, ? %s);', $extrafields['label'], $questions), $fields);

    if ($mysql->total) {
        header("Location: /admin/users", true, 302);
    }
}

$fields = array();
foreach ($extrafields['list'] as $key => $field) {
    $fields[] = array(
        'NAME' => $field,
        'LABEL' => $extrafields['labels'][$key]
    );
}
$tpl->setcondition('EXTRAFIELDS');
$tpl->setarray('FIELD', $fields);
