<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

if (@$_REQUEST['save']) {
    $mysql->reset()
        ->select('count(*) AS total, pid')
        ->from('pages')
        ->where('url = ? AND parent = ?')
        ->values(array($_REQUEST['url'], $_REQUEST['current_parent']))
        ->exec();

    $condition = $mysql->singleline();

    if ($condition->total > 0 && $condition->pid != $site->arg(2)) {
        $tpl->setarray('ERRORS', array('MSG' => lang('LANG_PAGE_ALREADY_EXISTS')));
        $tpl->setcondition('ERRORS');
    } else {
        if ($_REQUEST['prid']) {
            $defaultfields = array_merge(array('pid'), $csection->getfields(true));
            $valid = 1;

            $mysql->reset()
                ->select()
                ->from('pages')
                ->where('pid = ?')
                ->values($site->arg(2))
                ->exec();

            $currentpage = $mysql->singleline();
            $fields = array();

            foreach ($defaultfields as $field) {
                array_push($fields, $currentpage->{$field});
            }

            $mysql->reset()
                ->select()
                ->from('pages_review')
                ->where('prid = ?')
                ->values($_REQUEST['prid'])
                ->exec();

            $reviewpage = $mysql->singleline();
            $review = array();
            $fieldsupdate = '';

            foreach ($defaultfields as $field) {
                $fieldsupdate .= sprintf(', `%s` = ?', $field);
                $review[] = $reviewpage->{$field};
            }

            $review[] = $site->arg(2);

            $mysql->reset()
                ->insert('pages_review')
                ->fields($defaultfields)
                ->values($fields)
                ->exec();

            $valid = $mysql->errnum ? 0 : $valid;

            $mysql->reset()
                ->update('pages')
                ->fields($defaultfields)
                ->values($review)
                ->where('pid = ?')
                ->exec();

            $valid = $mysql->errnum ? 0 : $valid;

            $mysql->reset()
                ->delete()
                ->from('pages_review')
                ->where('prid = ?')
                ->values($_REQUEST['prid'])
                ->exec();

            $valid = $mysql->errnum ? 0 : $valid;

            if ($valid) {
                $tpl->setarray('MESSAGES', array('MSG' => 'Page changed to revision X'));
            }
        }

        if ($_REQUEST['current_url'] != $_REQUEST['url']) {

            $currenturl = $_REQUEST['current_url'];

            $mysql->reset()
                ->delete()
                ->from('redirect')
                ->where('`from` = ?')
                ->values($_REQUEST['url'])
                ->exec();

            $mysql->reset()
                ->select('destination')
                ->from('redirect')
                ->where('destination = ?')
                ->order('created')
                ->values($currenturl)
                ->exec();

            if ($mysql->total) {
                foreach ($mysql->result() as $url) {
                    $mysql->reset()
                        ->insert('redirect')
                        ->fields(array('pid', 'from', 'destination', 'creator'))
                        ->values(array($site->arg(2), $url->destination, $_REQUEST['url'], $user->id))
                        ->exec();
                }
            } else {
                $mysql->reset()
                    ->insert('redirect')
                    ->fields(array('pid', 'from', 'destination', 'creator'))
                    ->values(array($site->arg(2), $currenturl, $_REQUEST['url'], $user->id))
                    ->exec();
            }

            $mysql->statement('UPDATE pages AS p
            LEFT JOIN pages AS pp ON pp.pid = p.parent
            SET p.url = ?, p.beautify = CONCAT(IFNULL(pp.beautify, ""), ?)
            WHERE p.pid = ?;', array(
                $_REQUEST['url'],
                $_REQUEST['url'],
                $site->arg(2),
            ));

            if (!$mysql->errnum) {
                $tpl->setarray('MESSAGES', array('MSG' => 'Url for the page changed to X'));
            }

            foreach($_REQUEST['pages'] as $pid) {
                $mysql->statement('UPDATE pages AS p
                SET p.beautify = concat(?, p.url)
                WHERE p.pid = ?;', array(
                    $_REQUEST['url'],
                    $pid,
                ));
            }

        }

        redirect('/admin/'.$site->arg(1));
    }
}

$mysql->reset()
    ->select('pid, title')
    ->from('pages')
    ->where('parent = ?')
    ->values($site->arg(2))
    ->exec();

$pages = array();
foreach ($mysql->result() as $child) {
    $pages[] = array('PID' => $child->pid, 'TITLE' => $child->title);
}
$tpl->setarray('PAGES', $pages);
$tpl->setcondition('HASCHILDS', !empty($pages));
