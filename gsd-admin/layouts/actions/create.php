<?php

if (@$_REQUEST['save']) {

    $fields = array(
        $_REQUEST['name'],
        $_REQUEST['file'],
        $user->id
    );

    $mysql->statement('INSERT INTO layouts (name, file, creator) values(?, ?, ?);', $fields);

    if ($mysql->total) {

        header("Location: /admin/layouts", true, 302);
        exit;
    }
}
