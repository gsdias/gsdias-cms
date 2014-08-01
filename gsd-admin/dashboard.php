<?php

$mysql->statement('SELECT * FROM users LIMIT 0, 4');

$users = array();

foreach ($mysql->result() as $userlist) {
    $created = explode(' ', $userlist['created']);
    $users[] = array(
        'ID' => $userlist['uid'],
        'NAME' => $userlist['name'],
        'CREATED' => timeago(dateDif($created[0], date('Y-m-d',time())))
    );
}
$tpl->setarray('USERS', $users);

$mysql->statement('SELECT * FROM pages LIMIT 0, 4');

$pages = array();

if ($mysql->total) {
    $tpl->setcondition('PAGES_EXIST');
    foreach ($mysql->result() as $pagelist) {
        $created = explode(' ', $pagelist['created']);
        $pages[] = array(
            'ID' => $pagelist['pid'],
            'NAME' => $pagelist['url'],
            'CREATED' => timeago(dateDif($created[0], date('Y-m-d',time())))
        );
    }
    $tpl->setarray('PAGES', $pages);
}