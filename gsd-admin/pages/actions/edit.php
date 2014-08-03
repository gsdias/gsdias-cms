<?php

if (@$_REQUEST['save']) {
    $mysql->statement('UPDATE pages SET title = ?, description = ?, keywords = ?, og_title = ?, og_description = ?, og_image = ? WHERE pid = ?;', array(
        $_REQUEST['title'],
        $_REQUEST['description'],
        $_REQUEST['keywords'],
        $_REQUEST['og_title'],
        $_REQUEST['og_description'],
        $_REQUEST['og_image'],
        $path[2]
    ));
    header("Location: /admin/pages", true, 302);
}