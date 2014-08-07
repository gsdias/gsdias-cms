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
        @$_REQUEST['menu'] ? @$_REQUEST['menu'] : null,
        @$_REQUEST['auth'] ? @$_REQUEST['auth'] : null,
        $user->id
    );
    $valuefields = array();

    $fields = array_merge($defaultfields, $valuefields);
    $mysql->statement('INSERT INTO pages (title, url, description, keywords, tags, og_title, og_description, show_menu, require_auth, creator) values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?);', $defaultfields);
    
    if ($mysql->total) {
        header("Location: /admin/pages", true, 302);
    }
}
