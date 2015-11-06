<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.4
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
if (IS_ADMIN) {
    $mysql->reset()
        ->select()
        ->from('users')
        ->where('disabled IS NULL')
        ->order('created', 'DESC')
        ->limit(0, 5)
        ->exec();

    $users = array();

    foreach ($mysql->result() as $userlist) {
        $created = explode(' ', $userlist->last_login);
        $users[] = array(
            'ID' => $userlist->uid,
            'NAME' => $userlist->name,
            'CREATED' => $userlist->last_login ? timeago(dateDif($created[0], date('Y-m-d', time())), $created[1]) : lang('LANG_NEVER'),
        );
    }
    $tpl->setarray('USERS', $users);

}

if (IS_ADMIN || IS_EDITOR) {
    $mysql->reset()
        ->select()
        ->from('pages')
        ->where('published IS NOT NULL')
        ->order('created', 'DESC')
        ->limit(0, 5)
        ->exec();

    $pages = array();

    if ($mysql->total) {
        $tpl->setcondition('PAGES_EXIST');
        foreach ($mysql->result() as $pagelist) {
            $created = explode(' ', $pagelist->created);
            $pages[] = array(
                'ID' => $pagelist->pid,
                'NAME' => $pagelist->beautify,
                'CREATED' => timeago(dateDif($created[0], date('Y-m-d', time())), $created[1]),
            );
        }
        $tpl->setarray('PAGES', $pages);
    }

    $mysql->reset()
        ->select()
        ->from('images')
        ->order('created', 'DESC')
        ->limit(0, 5)
        ->exec();

    $images = array();

    if ($mysql->total) {
        $tpl->setcondition('IMAGES_EXIST');
        foreach ($mysql->result() as $imagelist) {
            $created = explode(' ', $imagelist->created);
            $images[] = array(
                'ID' => $imagelist->iid,
                'NAME' => $imagelist->name,
                'CREATED' => timeago(dateDif($created[0], date('Y-m-d', time())), $created[1]),
            );
        }
        $tpl->setarray('IMAGES', $images);
    }

    $mysql->reset()
        ->select()
        ->from('documents')
        ->order('created', 'DESC')
        ->limit(0, 5)
        ->exec();

    $documents = array();

    if ($mysql->total) {
        $tpl->setcondition('DOCUMENTS_EXIST');
        foreach ($mysql->result() as $documentlist) {
            $created = explode(' ', $documentlist->created);
            $documents[] = array(
                'ID' => $documentlist->did,
                'NAME' => $documentlist->name,
                'CREATED' => timeago(dateDif($created[0], date('Y-m-d', time())), $created[1]),
            );
        }
        $tpl->setarray('DOCUMENTS', $documents);
    }
}

$file = CLIENTPATH.'include/admin/dashboard'.PHPEXT;

if (is_file($file)) {
    include_once $file;
}
