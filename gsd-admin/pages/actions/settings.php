<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.4
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
if (@$_REQUEST['save']) {
    $mysql->reset()
        ->select('count(*) AS total, pid')
        ->from('pages')
        ->where('url = ?')
        ->values($_REQUEST['url'])
        ->exec();

    $condition = $mysql->singleline();

    if ($condition->total > 0 && $condition->pid != $site->arg(2)) {
        $tpl->setvar('ERRORS', lang('LANG_PAGE_ALREADY_EXISTS'));
        $tpl->setcondition('ERRORS');
    } else {
        if ($_REQUEST['prid']) {
            $defaultfields = array('pid', 'title', 'description', 'tags', 'keywords', 'og_title', 'og_description', 'og_image', 'show_menu', 'require_auth', 'published', 'creator', 'modified');

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

            $mysql->reset()
                ->update('pages')
                ->fields($defaultfields)
                ->values($review)
                ->exec();

            $mysql->reset()
                ->delete()
                ->from('pages_review')
                ->where('prid = ?')
                ->values($_REQUEST['prid'])
                ->exec();
        }

        $mysql->reset()
            ->select('url')
            ->from('pages')
            ->where('pid = ?')
            ->values($site->arg(2))
            ->exec();

        $currenturl = $mysql->singleresult();

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
                //REFACTOR: THIS PART IS OUTDATED
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

//        $mysql->reset()
//            ->update('pages AS p')
//            ->join('pages AS pp', 'LEFT')
//            ->on('pp.pid = p.parent')
//            ->fields(array('p.url', 'p.beautify'))
//            ->values(array(
//                $_REQUEST['url'],
//                sprintf('concat(if(pp.beautify IS NULL, "", pp.beautify), %s)', mysql_real_escape_string($_REQUEST['url'])),
//                $site->arg(2)
//            ))
//            ->where('p.pid = ?')
//            ->exec();

        $mysql->statement('UPDATE pages AS p
        LEFT JOIN pages AS pp ON pp.pid = p.parent
        SET p.url = ?, p.beautify = concat(if(pp.beautify IS NULL, "", pp.beautify), ?)
        WHERE p.pid = ?;', array(
            $_REQUEST['url'],
            $_REQUEST['url'],
            $site->arg(2),
        ));

//        $mysql->reset()
//            ->update('pages AS p')
//            ->join('pages AS pp', 'LEFT')
//            ->on('pp.pid = p.parent')
//            ->fields(array('p.beautify'))
//            ->values(array(
//                'concat(if(pp.beautify IS NULL, "", pp.beautify), p.url)',
//                $site->arg(2)
//            ))
//            ->where('p.parent = ?')
//            ->exec();

        $mysql->statement('UPDATE pages AS p
        LEFT JOIN pages AS pp ON pp.pid = p.parent
        SET p.beautify = concat(if(pp.beautify IS NULL, "", pp.beautify), p.url)
        WHERE p.parent = ?;', array($site->arg(2)));

        redirect('/admin/'.$site->arg(1));
    }
}
