<?php

if (@$_REQUEST['save']) {
    $tags = $_REQUEST['tags'];

    $mysql->statement('SELECT url FROM pages WHERE pid = ?;', array($path[2]));
    
    $currenturl = $mysql->singleresult();
    
    $mysql->statement('SELECT `from`, destination FROM redirect WHERE destination = ? ORDER BY created;', array($currenturl));
    
    if ($mysql->total) {
        foreach($mysql->result() as $url) {
            $mysql->statement('INSERT INTO redirect (`pid`, `from`, `destination`, `creator`) VALUES (?, ?, ?, ?);', array($path[2], $url[1], $_REQUEST['url'], $user->id));
        }
    } else {
        $mysql->statement('INSERT INTO redirect (`pid`, `from`, `destination`, `creator`) VALUES (?, ?, ?, ?);', array($path[2], $currenturl, $_REQUEST['url'], $user->id));
    }
    $mysql->statement('UPDATE pages SET url = ?, tags = ? WHERE pid = ?;', array(
        $_REQUEST['url'],
        $_REQUEST['tags'],
        $path[2]
    ));
    
    header("Location: /admin/pages", true, 302);
}
