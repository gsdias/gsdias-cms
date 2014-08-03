<?php

$numberPerPage = 10;

include_once('gsd-paginator' . PHPEXT);
include_once(CLIENTPATH . 'include/admin/fields' . PHPEXT);

if (@$path[3]) {

    $main = sprintf('%s/%s', $path[1], $path[3]);

    $mysql->statement('SELECT * FROM users WHERE uid = :uid ORDER BY uid;', array(':uid' => $path[2]));
    
    $page = $mysql->singleline();
    
    $created = explode(' ', $page['created']);
    
    $extrafields = userfields();
    $fields = array();
    foreach ($extrafields['list'] as $key => $field) {
        $fields[] = array(
            'NAME' => $field,
            'LABEL' => $extrafields['labels'][$key],
            'VALUE' => $page[$field]
        );
    }

    $tpl->setarray('FIELD', $fields);



    $tpl->setvars(array(
        'CURRENT_USER_NAME' => $page['name'],
        'CURRENT_USER_EMAIL' => $page['email'],
        'CURRENT_USER_CREATED' => timeago(dateDif($created[0], date('Y-m-d',time()))),
        'CURRENT_USER_AUTHOR' => $page['name'],
        'CURRENT_USER_UID' => $page['uid']
    ));
    
    $file = sprintf('gsd-admin/%s/actions/%s%s', $path[1], $path[3], PHPEXT);
    
    include_once($file);

} else {

    if (@$path[2]) {
        $main = sprintf('%s/%s', $path[1], $path[2]);
        $file = sprintf('gsd-admin/%s/actions/%s%s', $path[1], $path[2], PHPEXT);

        include_once($file);
    }

    $mysql->statement('SELECT u.uid, u.name, c.uid AS cid, c.name AS cname, u.created, u.last_login FROM users as u LEFT JOIN users as c ON u.creator = c.uid ORDER BY u.uid ' . pageLimit(pageNumber(), $numberPerPage));

    $users = array();

    if ($mysql->total) {
        foreach ($mysql->result() as $userlist) {
            $created = explode(' ', $userlist['created']);
            $logged = explode(' ', $userlist['last_login']);
            $users[] = array(
                'NAME' => $userlist['name'],
                'UID' => $userlist['uid'],
                'CID' => $userlist['cid'],
                'AUTHOR' => $userlist['cname'],
                'CREATED' => timeago(dateDif($created[0], date('Y-m-d',time()))),
                'LOGGED' => $userlist['last_login'] ? timeago(dateDif($logged[0], date('Y-m-d',time()))) : 'Never'
            );
        }
        $tpl->setarray('USERS', $users);
    }
    $sql = 'FROM users as u LEFT JOIN users as c ON u.creator = c.uid ORDER BY u.uid';
 
    $pages = pageGenerator($sql, $numberPerPage, pageNumber(), $mysql->total);
    
    $first_page = new anchor(array('text' => '&lt;&lt;', 'href' => '?page=1'));
    $prev_page = new anchor(array('text' => '&lt;', 'href' => '?page=' . $pages['PREV']));
    $next_page = new anchor(array('text' => '&gt;', 'href' => '?page=' . $pages['NEXT']));
    $last_page = new anchor(array('text' => '&gt;&gt;', 'href' => '?page=' . $pages['LAST']));
    $tpl->setvars(array(
        'FIRST_PAGE' => $first_page,
        'PREV_PAGE' => $prev_page,
        'NEXT_PAGE' => $next_page,
        'LAST_PAGE' => $last_page,
        'CURRENT_PAGE' => $pages['CURRENT'],
        'TOTAL_PAGES' => $pages['TOTAL']
    ));
}
