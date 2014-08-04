<?php

if (@$_REQUEST['save']) {
    $defaultfields = array(
        $_REQUEST['title'],
        $_REQUEST['url'],
        $user->id
    );
    $valuefields = array();
    //$questions = str_repeat(", ?", sizeof($extrafields['list']));

    //foreach ($extrafields['list'] as $field) {
    //    $valuefields[] = $_REQUEST[$field];
    //}

    $fields = array_merge($defaultfields, $valuefields);
    $mysql->statement('INSERT INTO pages (title, url, creator) values(?, ?, ?);', $fields);
echo $mysql->errmsg;
    if ($mysql->total) {
        header("Location: /admin/pages", true, 302);
    }
}
