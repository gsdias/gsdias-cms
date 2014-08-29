<?php

if (!IS_ADMIN) {
    header("Location: /admin/pages", true, 302);
    exit;
}

if (@$_REQUEST['save']) {
    
    $defaultfields = array('title', 'url', 'lid', 'description', 'keywords', 'tags', 'og_title', 'og_image', 'og_description', 'show_menu', 'require_auth');
    
    $fields = array('creator');
    
    $values = array($user->id);        
    
    $_REQUEST['require_auth'] = @$_REQUEST['auth'] ? @$_REQUEST['auth'] : null;
    $_REQUEST['show_menu'] = @$_REQUEST['menu'] ? @$_REQUEST['menu'] : null;
    
    $result = $csection->add($defaultfields, $fields, $values);

    if ($result['total']) {
        $pid = $result['id'];
        $mysql->statement('SELECT *
        FROM layoutsections AS ls
        JOIN layoutsectionmoduletypes AS lsmt ON lsmt.lsid = ls.lsid
        WHERE lid = ?;', array(@$_REQUEST['lid']));

        foreach ($mysql->result() as $section) {
            $mysql->statement('INSERT INTO pagemodules (lsid, mtid, pid, creator) values(?, ?, ?, ?);', array(
                $section['lsid'],
                $section['mtid'],
                $pid,
                $user->id
            ));
        }

        $_SESSION['message'] = sprintf($lang[$config['lang']]['LANG_PAGE_CREATED'], $_REQUEST['title']);
        header("Location: /admin/pages", true, 302);
        exit;
    } else {
        $tpl->setvar('ERRORS', '{LANG_PAGE_ALREADY_EXISTS}');
        $tpl->setcondition('ERRORS');
    }
}

$mysql->statement('SELECT * FROM layouts');

$types = array();
foreach ($mysql->result() as $item) {
    $types[$item['lid']] = $item['name'];
}

$types = new select( array ( 'list' => $types, 'id' => 'LAYOUT' ) );
$types->object();
