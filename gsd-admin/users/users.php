<?php

$numberPerPage = 50;

include_once('gsd-paginator' . PHPEXT);

if (@$path[3]) {
    $mysql->statement('SELECT * FROM users WHERE uid = :uid ORDER BY uid;', array(':uid' => $path[2]));
    
    $page = $mysql->singleline();
    
    $created = explode(' ', $page['created']);
    
    $tpl->setvars(array(
        'CURRENT_USER_NAME' => $page['name'],
        'CURRENT_USER_CREATED' => timeago(dateDif($created[0], date('Y-m-d',time()))),
        'CURRENT_USER_AUTHOR' => $page['name'],
        'CURRENT_USER_UID' => $page['uid']
    ));
    
    $file = sprintf('gsd-admin/%s/actions/%s%s', $path[1], $path[3], PHPEXT);
    
    include_once($file);
    $main = sprintf('%s/%s', $path[1], $path[3]);

} else {
    $mysql->statement('SELECT u.uid, u.name, c.uid AS cid, c.name AS cname, u.created FROM users as u LEFT JOIN users as c ON u.creator = c.uid ORDER BY u.uid ' . pageLimit(pageNumber(), $numberPerPage));

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
    $sql = 'FROM users as u LEFT JOIN users as c ON u.creator = c.uid ORDER BY u.uid';
 
    $pages = pageGenerator($sql, $numberPerPage, 1000, pageNumber(), $mysql->total);
    
    print_r($pages);
    
    $first_page = new anchor(array('text' => '&lt;&lt;'));
    $prev_page = new anchor(array('text' => '&lt;'));
    $next_page = new anchor(array('text' => '&gt;'));
    $last_page = new anchor(array('text' => '&gt;&gt;'));
    $tpl->setvars(array(
        'FIRST_PAGE' => $first_page,
        'PREV_PAGE' => $prev_page,
        'NEXT_PAGE' => $next_page,
        'LAST_PAGE' => $last_page,
        'CURRENT_PAGE' => 1,
        'TOTAL_PAGES' => $pages
    ));
}
