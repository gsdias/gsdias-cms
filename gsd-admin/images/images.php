<?php

if (@$path[3]) {
    $mysql->statement('SELECT * FROM pages AS p JOIN users AS u ON p.uid = u.uid WHERE pid = :pid ORDER BY pid;', array(':pid' => $path[2]));
    
    $page = $mysql->singleline();
    
    $created = explode(' ', $page['created']);
    
    $tpl->setvars(array(
        'CURRENT_PAGE_TITLE' => $page['title'],
        'CURRENT_PAGE_DESCRIPTION' => $page['description'],
        'CURRENT_PAGE_KEYWORDS' => $page['keywords'],
        'CURRENT_PAGE_URL' => $page['url'],
        'CURRENT_PAGE_OG_TITLE' => $page['og_title'],
        'CURRENT_PAGE_OG_DESCRIPTION' => $page['og_description'],
        'CURRENT_PAGE_OG_IMAGE' => $page['og_image'],
        'CURRENT_PAGE_CREATED' => timeago(dateDif($created[0], date('Y-m-d',time()))),
        'CURRENT_PAGE_AUTHOR' => $page['name'],
        'CURRENT_PAGE_UID' => $page['uid']
    ));
    
    $file = sprintf('gsd-admin/%s/actions/%s%s', $path[1], $path[3], PHPEXT);
    
    include_once($file);
    $main = sprintf('%s/%s', $path[1], $path[3]);

} else {
    $mysql->statement('SELECT i.name, i.created, iid, uid, u.name AS cname FROM images AS i JOIN users AS u ON i.creator = u.uid ORDER BY iid;');

    $images = array();

    if ($mysql->total) {
        $tpl->setcondition('IMAGES_EXIST');
        foreach ($mysql->result() as $pagelist) {
            $created = explode(' ', $pagelist['created']);
            $images[] = array(
                'ID' => $pagelist['iid'],
                'NAME' => $pagelist['name'],
                'UID' => $pagelist['uid'],
                'AUTHOR' => $pagelist['cname'],
                'CREATED' => timeago(dateDif($created[0], date('Y-m-d',time())))
            );
        }
        $tpl->setarray('IMAGES', $images);
    }
}