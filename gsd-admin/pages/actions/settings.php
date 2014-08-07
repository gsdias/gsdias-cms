<?php

if (@$_REQUEST['save']) {

    $mysql->statement('SELECT url FROM pages WHERE pid = ?;', array(@$site->path[2]));
    
    $currenturl = $mysql->singleresult();
    
    $mysql->statement('DELETE FROM redirect WHERE `from` = ?;', array($_REQUEST['url']));
    
    $mysql->statement('SELECT `from`, destination FROM redirect WHERE destination = ? ORDER BY created;', array($currenturl));
    
    if ($mysql->total) {
        foreach($mysql->result() as $url) {
            $mysql->statement('INSERT INTO redirect (`pid`, `from`, `destination`, `creator`) VALUES (?, ?, ?, ?);', array(@$site->path[2], $url[1], $_REQUEST['url'], $user->id));
        }
    } else {
        $mysql->statement('INSERT INTO redirect (`pid`, `from`, `destination`, `creator`) VALUES (?, ?, ?, ?);', array(@$site->path[2], $currenturl, $_REQUEST['url'], $user->id));
    }
    $mysql->statement('UPDATE pages SET url = ? WHERE pid = ?;', array(
        $_REQUEST['url'],
        @$site->path[2]
    ));
    
    header("Location: /admin/pages", true, 302);
}
