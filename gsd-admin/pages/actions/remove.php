<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.5.1
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

if (!@$_REQUEST['confirm']) {
    $mysql->reset()
        ->select()
        ->from('pages')
        ->exec();

    $parents = array(0 => lang('LANG_CHOOSE'));
    foreach ($mysql->result() as $page) {
        $parents[$page->pid] = $page->title;
    }

    $mysql->reset()
        ->select()
        ->from('pages')
        ->where('parent = ?')
        ->values(array($site->arg(2)))
        ->exec();

    $tpl->setcondition('IS_PARENT', $mysql->total > 0);

    $pages = array();
    foreach ($mysql->result() as $page) {
        $_parents = $parents;
        unset($_parents[$page->pid]);
        $pages[] = array(
            'NAME' => $page->title,
            'LIST' => new GSD\select(array(
                'name' => 'parent['.$page->pid.']',
                'list' => $_parents,
                'selected' => $site->arg(2),
            )),
        );
    }

    $tpl->setarray('CHILDS', $pages);
}

if (@$_REQUEST['confirm'] == $afirmative) {
    $mysql->reset()
        ->select('url, title')
        ->from('pages')
        ->where('pid = ?')
        ->values(array($site->arg(2)))
        ->exec();

    $result = $mysql->singleline();

    $title = $result->title;
    $currenturl = $result->url;

    if (!empty($_REQUEST['parent'])) {
        foreach ($_REQUEST['parent'] as $pid => $parent) {
            $parent = $parent == $site->arg(2) ? 0 : $parent;

            $mysql->statement('UPDATE pages AS p
            LEFT JOIN pages AS parent ON parent.pid = ?
            SET p.parent = ?, p.beautify = concat(if(parent.beautify IS NULL, "", parent.beautify), p.url)
            WHERE p.pid = ?;', array($parent, $parent, $pid));
        }
    }

    $mysql->reset()
        ->delete()
        ->from('redirect')
        ->where('destination = ?')
        ->values($currenturl)
        ->exec();

    $result = $csection->remove();

    if ($result['errnum']) {
        $tpl->setvar('ERRORS', lang('LANG_PAGE_ERROR'));
        $tpl->setcondition('ERRORS');
    } else {
        $_SESSION['message'] = sprintf(lang('LANG_PAGE_REMOVED'), $title);

        redirect('/admin/'.$site->arg(1));
    }
}

if (@$_REQUEST['confirm'] == $negative) {
    redirect('/admin/'.$site->arg(1));
}
