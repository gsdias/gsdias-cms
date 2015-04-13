<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.1
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

if (@$_REQUEST['save']) {

    $mysql->statement('SELECT count(*), pid FROM pages WHERE url = ?;', array($_REQUEST['url']));
    $condition = $mysql->singleline();
    
    if ($condition[0] > 0 && $condition[1] != $site->arg(2)) {
        
        $tpl->setvar('ERRORS', '{LANG_PAGE_ALREADY_EXISTS}');
        $tpl->setcondition('ERRORS');
        
    } else {
    
        if ($_REQUEST['prid']) {
            $defaultfields = array('pid', 'title', 'description', 'tags', 'keywords', 'og_title', 'og_description', 'og_image', 'show_menu', 'require_auth', 'published', 'creator', 'modified');
            $questions = str_repeat(", ? ", sizeof($defaultfields));

            $mysql->statement('SELECT * FROM pages WHERE pid = ?;', array($site->arg(2)));
            $currentpage = $mysql->singleline();
            $fields = array();

            foreach ($defaultfields as $field) {
                array_push($fields, $currentpage[$field]);
            }

            $mysql->statement('SELECT * FROM pages_review WHERE prid = ?;', array($_REQUEST['prid']));
            $reviewpage = $mysql->singleline();
            $review = array();
            $fieldsupdate = '';

            foreach ($defaultfields as $field) {
                $fieldsupdate .= sprintf(", `%s` = ?", $field);
                $review[] = $reviewpage[$field];
            }

            $review[] = $site->arg(2);

            $mysql->statement(sprintf('INSERT INTO pages_review (%s) values (%s);', implode(',', $defaultfields), substr($questions, 2)), $fields);
            $mysql->statement(sprintf('UPDATE pages SET %s WHERE pid = ?;', substr($fieldsupdate, 2)), $review);
            $mysql->statement('DELETE FROM pages_review WHERE prid = ?;', array($_REQUEST['prid']));

        }

        $mysql->statement('SELECT url FROM pages WHERE pid = ?;', array($site->arg(2)));

        $currenturl = $mysql->singleresult();

        $mysql->statement('DELETE FROM redirect WHERE `from` = ?;', array($_REQUEST['url']));

        $mysql->statement('SELECT `from`, destination FROM redirect WHERE destination = ? ORDER BY created;', array($currenturl));

        if ($mysql->total) {
            foreach($mysql->result() as $url) {
                //REFACTOR: THIS PART IS OUTDATED
                $mysql->statement('INSERT INTO redirect (`pid`, `from`, `destination`, `creator`) VALUES (?, ?, ?, ?);', array($site->arg(2), $url[1], $_REQUEST['url'], $user->id));
            }
        } else {
            $mysql->statement('INSERT INTO redirect (`pid`, `from`, `destination`, `creator`) VALUES (?, ?, ?, ?);', array($site->arg(2), $currenturl, $_REQUEST['url'], $user->id));
        }

        $mysql->statement('UPDATE pages AS p JOIN pages AS pp ON pp.pid = p.parent SET p.url = ?, p.beautify = concat(pp.beautify, ?) WHERE p.pid = ?;', array(
            $_REQUEST['url'],
            $_REQUEST['url'],
            $site->arg(2)
        ));

        header("Location: /admin/pages", true, 302);
        exit;
    }
}
