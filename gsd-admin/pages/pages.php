<?php

if (@$path[3]) {
    $mysql->statement('SELECT *, p.created FROM pages AS p JOIN users AS u ON p.uid = u.uid WHERE pid = :pid ORDER BY pid;', array(':pid' => $path[2]));
    
    $page = $mysql->singleline();
    
    $created = explode(' ', $page['created']);
    
    $tpl->setvars(array(
        'CURRENT_PAGE_ID' => $page['pid'],
        'CURRENT_PAGE_TITLE' => $page['title'],
        'CURRENT_PAGE_DESCRIPTION' => $page['description'],
        'CURRENT_PAGE_KEYWORDS' => $page['keywords'],
        'CURRENT_PAGE_TAGS' => $page['tags'],
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

    if (@$path[2]) {
        $main = sprintf('%s/%s', $path[1], $path[2]);
        $file = sprintf('gsd-admin/%s/actions/%s%s', $path[1], $path[2], PHPEXT);

        include_once($file);
    }

    $mysql->statement('SELECT *, p.created FROM pages AS p JOIN users AS u ON p.uid = u.uid WHERE p.disabled IS NULL ORDER BY pid;');

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
}
