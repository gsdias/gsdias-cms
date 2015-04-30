<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.1
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

if (!IS_ADMIN) {
    $_SESSION['error'] = '{LANG_PAGE_NOPERMISSION}';
    header("Location: /admin/pages", true, 302);
    exit;
}

if (!@$_REQUEST['confirm']) {
    $mysql->statement('SELECT * FROM pages;');
    $parents = array(0 => '{LANG_CHOOSE}');
    foreach($mysql->result() as $page) {
        $parents[$page->pid] = $page->title;
    }

    $mysql->statement('SELECT * FROM pages WHERE parent = ?;', array($site->arg(2)));
    $tpl->setcondition('IS_PARENT', $mysql->total > 0);

    $pages = array();
    foreach($mysql->result() as $page) {
        $_parents = $parents;
        unset($_parents[$page->pid]);
        $pages[] = array(
            'NAME' => $page['title'],
            'LIST' => new select(array(
                'name' => 'parent[' . $page->pid . ']',
                'list' => $_parents,
                'selected' => $site->arg(2)
            ))
        );
    }

    $tpl->setarray('CHILDS', $pages);
}

if (@$_REQUEST['confirm'] == $afirmative) {
    $mysql->statement('SELECT url, title FROM pages WHERE pid = ?;', array($site->arg(2)));

    $result = $mysql->singleline();

    $title = $result->title;
    $currenturl = $result->url;

    if (!empty(@$_REQUEST['parent'])) {
        foreach($_REQUEST['parent'] as $pid => $parent) {
            $parent = $parent == $site->arg(2) ? 0 : $parent;
            $mysql->statement('UPDATE pages AS p
            LEFT JOIN pages AS parent ON parent.pid = ?
            SET p.parent = ?, p.beautify = concat(if(parent.beautify IS NULL, "", parent.beautify), p.url)
            WHERE p.pid = ?;', array($parent, $parent, $pid));
        }
    }

    $mysql->statement('DELETE FROM redirect WHERE `destination` = ?;', array($currenturl));

    $mysql->statement('DELETE FROM pages WHERE pid = ?;', array($site->arg(2)));

    if ($mysql->errnum) {

        $tpl->setvar('ERRORS', '{LANG_PAGE_ERROR}');
        $tpl->setcondition('ERRORS');

    } else {

        $_SESSION['message'] = sprintf(lang('LANG_PAGE_REMOVED'), $title);

        header("Location: /admin/pages", true, 302);
        exit;

    }
}

if (@$_REQUEST['confirm'] == $negative) {
    header("Location: /admin/pages", true, 302);
    exit;
}
