<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.2
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

if (!IS_ADMIN && !IS_EDITOR) {
    header("Location: /admin/" . $site->arg(1), true, 302);
    exit;
}

if (@$_REQUEST['save']) {
    
    $defaultfields = array('title', 'url', 'lid', 'description', 'keywords', 'tags', 'og_title', 'og_image', 'og_description', 'parent');
    
    $fields = array('creator', '`index`', 'show_menu', 'require_auth', 'created');
    
    $mysql->statement('DELETE FROM redirect WHERE `from` = ?;', array($_REQUEST['url']));
    $mysql->statement('SELECT max(`index`) AS max FROM pages;');
    
    $index = @$mysql->singleresult()->max;

    $values = array(
        $user->id,
        ($index != null ? $index + 1 : 0),
        @$_REQUEST['menu'] ? @$_REQUEST['menu'] : null,
        @$_REQUEST['auth'] ? @$_REQUEST['auth'] : null,
        date('Y-m-d H:i:s', time())
    );

    $result = $csection->add($defaultfields, $fields, $values);

    if ($result['total']) {
        $pid = $result['id'];
        $mysql->statement('SELECT *
        FROM layoutsections AS ls
        JOIN layoutsectionmoduletypes AS lsmt ON lsmt.lsid = ls.lsid
        WHERE lid = ?;', array(@$_REQUEST['lid']));

        foreach ($mysql->result() as $section) {
            $defaultdata = array('class' => '', 'style' => '', 'value' => '');
            $data = array('list' => array(array_fill(0, $section->total, $defaultdata)), 'class' => '', 'style' => '');
            $mysql->statement('INSERT INTO pagemodules (lsid, mtid, pid, data, creator) values(?, ?, ?, ?, ?);', array(
                $section->lsid,
                $section->mtid,
                $pid,
                serialize($data),
                $user->id
            ));
        }

        $_SESSION['message'] = sprintf(lang('LANG_PAGE_CREATED'), $_REQUEST['title']);
        header("Location: /admin/pages/$pid/edit", true, 302);
        exit;
    } else {
        $tpl->setvar('ERRORS', lang('LANG_PAGE_ALREADY_EXISTS'));
        $tpl->setcondition('ERRORS');
    }
}

$mysql->statement('SELECT * FROM layouts');

$types = array();
foreach ($mysql->result() as $item) {
    $types[$item->lid] = $item->name;
}

$types = new select( array ( 'list' => $types, 'id' => 'LAYOUT' ) );
$types->object();

$mysql->statement('SELECT pid, title FROM pages');

$types = array();
foreach ($mysql->result() as $item) {
    $types[$item->pid] = $item->title;
}

$types = new select( array ( 'list' => $types, 'id' => 'PARENT' ) );
$types->object();
