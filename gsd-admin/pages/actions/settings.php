<?php

$mysql->statement('SELECT * FROM pages AS p JOIN users AS u ON p.uid = u.uid ORDER BY pid;');

$pages = array();

if ($mysql->total) {
    $tpl->setcondition('PAGES_EXIST');
    foreach ($mysql->result() as $pagelist) {
        $created = explode(' ', $pagelist['created']);
        $pages[] = array(
            'ID' => $pagelist['pid'],
            'NAME' => $pagelist['url'],
            'UID' => $pagelist['uid'],
            'AUTHOR' => $pagelist['name'],
            'CREATED' => timeago(dateDif($created[0], date('Y-m-d',time())))
        );
    }
    $tpl->setarray('PAGES', $pages);
}