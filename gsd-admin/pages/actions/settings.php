<?php

if (@$_REQUEST['save']) {

    $mysql->statement('SELECT count(*), pid FROM pages WHERE url = ?;', array($_REQUEST['url']));
    $condition = $mysql->singleline();
    
    if ($condition[0] > 0 && $condition[1] != $site->arg(2)) {
        $tpl->setvar('ERRORS', 'There are already a page with that url.');
        $tpl->setcondition('ERRORS');
    } else {
    
        $mysql->statement('SELECT url FROM pages WHERE pid = ?;', array($site->arg(2)));

        $currenturl = $mysql->singleresult();

        $mysql->statement('DELETE FROM redirect WHERE `from` = ?;', array($_REQUEST['url']));

        $mysql->statement('SELECT `from`, destination FROM redirect WHERE destination = ? ORDER BY created;', array($currenturl));

        if ($mysql->total) {
            foreach($mysql->result() as $url) {
                $mysql->statement('INSERT INTO redirect (`pid`, `from`, `destination`, `creator`) VALUES (?, ?, ?, ?);', array($site->arg(2), $url[1], $_REQUEST['url'], $user->id));
            }
        } else {
            $mysql->statement('INSERT INTO redirect (`pid`, `from`, `destination`, `creator`) VALUES (?, ?, ?, ?);', array($site->arg(2), $currenturl, $_REQUEST['url'], $user->id));
        }
        $mysql->statement('UPDATE pages SET url = ? WHERE pid = ?;', array(
            $_REQUEST['url'],
            $site->arg(2)
        ));

        header("Location: /admin/pages", true, 302);
        exit;
    }
}
