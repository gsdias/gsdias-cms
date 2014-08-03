<?php

if (@$_REQUEST['save']) {
    $tags = $_REQUEST['tags'];

    $mysql->statement('UPDATE pages SET url = ?, tags = ? WHERE pid = ?;', array(
        $_REQUEST['url'],
        $_REQUEST['tags'],
        $path[2]
    ));
    header("Location: /admin/pages", true, 302);
}
