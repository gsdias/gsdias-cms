<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
defined('GVALID') or die;
if (!$site->p('confirm')) {
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
        ->values(array($site->a(2)))
        ->exec();

    $tpl->setcondition('IS_PARENT', $mysql->total > 0);

    $tpl->includeFiles('EXTRAINFO', 'pages/parentlist');

    $pages = array();
    foreach ($mysql->result() as $page) {
        $_parents = $parents;
        unset($_parents[$page->pid]);
        $pages[] = array(
            'NAME' => $page->title,
            'LIST' => new GSD\select(array(
                'name' => 'parent['.$page->pid.']',
                'list' => $_parents,
                'selected' => $site->a(2),
            )),
        );
    }

    $tpl->setarray('CHILDS', $pages);
}

if ($site->p('confirm') == $afirmative) {
    $mysql->reset()
        ->select('url, title')
        ->from('pages')
        ->where('pid = ?')
        ->values(array($site->a(2)))
        ->exec();

    $result = $mysql->singleline();

    $title = $result->title;
    $currenturl = $result->url;

    if (!empty($site->p('parent'))) {
        foreach ($site->p('parent') as $pid => $parent) {
            $parent = $parent == $site->a(2) ? 0 : $parent;

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

    if ($result['total'] === 0) {
        $tpl->setarray('ERRORS', array('MSG' => lang('LANG_PAGE_ERROR')));
        $tpl->setcondition('ERRORS');
    } else {
        $tpl->setarray('MESSAGES', array('MSG' => sprintf(lang('LANG_PAGE_REMOVED'), $title)));

        redirect('/admin/'.$site->a(1));
    }
}

if ($site->p('confirm') == $negative) {
    redirect('/admin/'.$site->a(1));
}
