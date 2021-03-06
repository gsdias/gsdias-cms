<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
defined('GVALID') or die;
if ($site->p('save')) {
    $mysql->reset()
        ->select('count(*) AS total, pid')
        ->from('pages')
        ->where('url = ? AND parent = ?')
        ->values(array($site->p('url'), $site->p('current_parent')))
        ->exec();

    $condition = $mysql->singleline();

    if ($mysql->total > 0 && $condition->pid != $site->a(2)) {
        $tpl->setarray('ERRORS', array('MSG' => lang('LANG_PAGE_ALREADY_EXISTS')));
        $tpl->setcondition('ERRORS');
    } else {
        if ($site->p('prid')) {
            $defaultfields = array_merge(array('pid'), $csection->getfields(true));
            $valid = 1;

            $mysql->reset()
                ->select()
                ->from('pages')
                ->where('pid = ?')
                ->values($site->a(2))
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
                ->values($site->p('prid'))
                ->exec();

            $reviewpage = $mysql->singleline();
            $review = array();
            $fieldsupdate = '';

            foreach ($defaultfields as $field) {
                $fieldsupdate .= sprintf(', `%s` = ?', $field);
                $review[] = $reviewpage->{$field};
            }

            $review[] = $site->a(2);

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
                ->values($site->p('prid'))
                ->exec();

            $valid = $mysql->errnum ? 0 : $valid;

            if ($valid) {
                $tpl->setarray('MESSAGES', array('MSG' => 'Page changed to revision X'));
            }
        }

        if ($site->p('current_url') != $site->p('url')) {
            $currenturl = $site->p('current_url');

            $mysql->reset()
                ->select('beautify')
                ->from('pages')
                ->where('pid = ?')
                ->values($site->p('current_parent'))
                ->exec();

            $parent_beautify = $mysql->singleresult();

            $mysql->reset()
                ->delete()
                ->from('redirect')
                ->where('`from` = ?')
                ->values($site->p('url'))
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
                        ->values(array($site->a(2), $url->destination, $parent_beautify.$site->p('url'), $user->id))
                        ->exec();
                }
            } else {
                $mysql->reset()
                    ->insert('redirect')
                    ->fields(array('pid', 'from', 'destination', 'creator'))
                    ->values(array($site->a(2), $currenturl, $parent_beautify.$site->p('url'), $user->id))
                    ->exec();
            }

            $mysql->statement('UPDATE pages AS p
            SET p.url = ?, p.beautify = ?
            WHERE p.pid = ?;', array(
                $site->p('url'),
                $parent_beautify.$site->p('url'),
                $site->a(2),
            ));

            if (!$mysql->errnum) {
                $tpl->setarray('MESSAGES', array('MSG' => 'Url for the page changed to X'));
            }
            if ($site->p('pages')) {
                foreach ($site->p('pages') as $pid) {
                    $mysql->statement('UPDATE pages AS p
                    SET p.beautify = concat(?, p.url)
                    WHERE p.pid = ?;', array(
                        $site->p('url'),
                        $pid,
                    ));
                }
            }
        }

        redirect('/admin/'.$site->a(1));
    }
}

$mysql->statement('SELECT * FROM pages_review WHERE pid = ?;', array($id));

if ($mysql->total) {
    $review = array();
    foreach ($mysql->result() as $field) {
        $review[] = array(
            'KEY' => $field->prid,
            'VALUE' => $field->modified,
        );
    }
    $tpl->setarray('VERSION', $review);
}

$mysql->reset()
    ->select('pid, title')
    ->from('pages')
    ->where('parent = ?')
    ->values($site->a(2))
    ->exec();

$pages = array();
foreach ($mysql->result() as $child) {
    $pages[] = array('PID' => $child->pid, 'TITLE' => $child->title);
}
$tpl->setarray('PAGES', $pages);
$tpl->setcondition('HASCHILDS', !empty($pages));
