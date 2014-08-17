<?php

if (!IS_ADMIN) {
    header("Location: /admin/pages", true, 302);
    exit;
}

if (@$_REQUEST['save']) {
    
    $_fields = $section . 'fields';

    $defaultfields = array('title', 'url', 'lid', 'description', 'keywords', 'tags', 'og_title', 'og_image', 'og_description');
    
    $extrafields = function_exists($_fields) ? $_fields() : array('list' => array());
    
    $fields = array_merge($defaultfields, $extrafields['list']);

    $values = array();
    
    foreach ($fields as $field) {
        $values[] = $_REQUEST[$field];
    }
    
    $fields = array_merge($fields, array('creator', 'require_auth', 'show_menu'));
    
    $values = array_merge($values, array(
        $user->id,
        @$_REQUEST['auth'] ? @$_REQUEST['auth'] : null,
        @$_REQUEST['menu'] ? @$_REQUEST['menu'] : null
    ));    
        
    $questions = str_repeat(", ? ", sizeof($fields));

    $mysql->statement(sprintf('INSERT INTO pages (%s) values(%s);', implode(', ', $fields), substr($questions, 2)), $values);

    if ($mysql->total) {
        $pid = $mysql->lastinserted();
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

        $_SESSION['message'] = sprintf('Pagina "%s" criada.', $_REQUEST['title']);
        header("Location: /admin/pages", true, 302);
        exit;
    } else {
        $tpl->setvar('ERRORS', 'Já existe uma página com esse endereço.' . $mysql->errmsg);
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
