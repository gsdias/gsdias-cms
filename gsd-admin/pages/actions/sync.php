<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.5.1
 */
defined('GVALID') or die;
$mysql->statement('UPDATE pages AS p
    LEFT JOIN pages AS pp ON pp.pid = p.parent
    SET p.beautify = CONCAT(IFNULL(pp.beautify, ""), p.url)
    WHERE p.pid = ?;', array(
        $site->arg(2)
    ));

$mysql->reset()
    ->select('title')
    ->from('pages')
    ->where('pid = ?')
    ->values($site->arg(2))
    ->exec();

$tpl->setarray('MESSAGES', array('MSG' => sprintf(lang('LANG_PAGE_SYNCED'), $mysql->singleresult())));
redirect('/admin/'.$site->arg(1));
