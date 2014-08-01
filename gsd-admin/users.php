<?php

$mysql->statement('SELECT u.uid, u.name, c.uid AS cid, c.name AS cname, u.created FROM users as u LEFT JOIN users as c ON u.creator = c.uid ORDER BY u.uid;');

$users = array();

if ($mysql->total) {
    foreach ($mysql->result() as $userlist) {
        $created = explode(' ', $userlist['created']);
        $users[] = array(
            'NAME' => $userlist['name'],
            'UID' => $userlist['uid'],
            'CID' => $userlist['cid'],
            'AUTHOR' => $userlist['cname'],
            'CREATED' => timeago(dateDif($created[0], date('Y-m-d',time())))
        );
    }
    $tpl->setarray('USERS', $users);
}