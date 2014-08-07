<?php

if (@$_REQUEST['save']) {

    $sectionextrafields = function_exists('pagesfields') ? pagesfields() : array();

    $defaultfields = array(
        $_REQUEST['title'],
        $_REQUEST['url'],
        $_REQUEST['description'],
        $_REQUEST['keywords'],
        $_REQUEST['tags'],
        $_REQUEST['og_title'],
        $_REQUEST['og_description'],
        $user->id
    );
    $valuefields = array();

    $fields = array_merge($defaultfields, $valuefields);
    $mysql->statement('INSERT INTO pages (title, url, description, keywords, tags, og_title, og_description, creator) values(?, ?, ?, ?, ?, ?, ?, ?);', $defaultfields);
    
    if ($mysql->total) {
        header("Location: /admin/pages", true, 302);
    }
}
