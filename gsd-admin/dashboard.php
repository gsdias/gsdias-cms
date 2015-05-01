<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.2
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

$mysql->statement('SELECT * FROM users WHERE disabled IS NULL LIMIT 0, 5');

$users = array();

foreach ($mysql->result() as $userlist) {
    $created = explode(' ', $userlist->last_login);
    $users[] = array(
        'ID' => $userlist->uid,
        'NAME' => $userlist->name,
        'CREATED' => $userlist->last_login ? timeago(dateDif($created[0], date('Y-m-d',time())), $created[1]) : lang('LANG_NEVER')
    );
}
$tpl->setarray('USERS', $users);

$mysql->statement('SELECT * FROM pages WHERE published IS NOT NULL LIMIT 0, 5');

$pages = array();

if ($mysql->total) {
    $tpl->setcondition('PAGES_EXIST');
    foreach ($mysql->result() as $pagelist) {
        $created = explode(' ', $pagelist->created);
        $pages[] = array(
            'ID' => $pagelist->pid,
            'NAME' => $pagelist->url,
            'CREATED' => timeago(dateDif($created[0], date('Y-m-d',time())), $created[1])
        );
    }
    $tpl->setarray('PAGES', $pages);
}

$mysql->statement('SELECT * FROM images LIMIT 0, 5');

$images = array();

if ($mysql->total) {
    $tpl->setcondition('IMAGES_EXIST');
    foreach ($mysql->result() as $imagelist) {
        $created = explode(' ', $imagelist->created);
        $images[] = array(
            'ID' => $imagelist->iid,
            'NAME' => $imagelist->name,
            'CREATED' => timeago(dateDif($created[0], date('Y-m-d',time())), $created[1])
        );
    }
    $tpl->setarray('IMAGES', $images);
}

$mysql->statement('SELECT * FROM documents LIMIT 0, 5');

$documents = array();

if ($mysql->total) {
    $tpl->setcondition('DOCUMENTS_EXIST');
    foreach ($mysql->result() as $documentlist) {
        $created = explode(' ', $documentlist->created);
        $documents[] = array(
            'ID' => $documentlist->did,
            'NAME' => $documentlist->name,
            'CREATED' => timeago(dateDif($created[0], date('Y-m-d',time())), $created[1])
        );
    }
    $tpl->setarray('DOCUMENTS', $documents);
}

$file = CLIENTPATH . 'include/admin/dashboard' . PHPEXT;

if (is_file($file)) {
    include_once($file);
}
