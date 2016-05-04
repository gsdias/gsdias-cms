<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.5
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.6
 */
if (!$csection->permission) {
    $_SESSION['error'] = lang('LANG_PAGE_NOPERMISSION');
    redirect('/admin/'.$site->arg(1));
}

$mysql->statement('UPDATE pages AS p
    LEFT JOIN pages AS pp ON pp.pid = p.parent
    SET p.beautify = concat(if(pp.beautify IS NULL, "", pp.beautify), p.url)
    WHERE p.pid = ?;', array(
        $site->arg(2)
    ));

$_SESSION['message'] = 'Page synced';
redirect('/admin/'.$site->arg(1));
