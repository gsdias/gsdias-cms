<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.0
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

if (!IS_ADMIN) {
    header("Location: /admin/pages", true, 302);
    exit;
}

if (@$_REQUEST['save']) {
    
    $content = file_get_contents(sprintf(CLIENTTPLPATH . '_layouts/%s', $_REQUEST['layout']));

    $fields = array(
        $_REQUEST['name'],
        str_replace('.html', '', $_REQUEST['layout']),
        $_REQUEST['ltid'],
        $user->id
    );

    $mysql->statement('INSERT INTO layouts (name, file, ltid, creator) values(?, ?, ?, ?);', $fields);
    $lid = $mysql->lastinserted();

    if ($lid) {
        preg_match_all(sprintf('#<!-- %s (.*?) -->#s', 'PLACEHOLDER'), $content, $matches, PREG_SET_ORDER);
        $list = array ();
        foreach ($matches as $match) array_push($list, $match[1]);

        while ($key = array_pop($list)) {
            $sectionname = explode(' ', $key);
            $mysql->statement('INSERT INTO layoutsections (lid, label, creator) values(?, ?, ?);', array(
                $lid,
                $sectionname[0],
                $user->id
            ));
            $lsid = $mysql->lastinserted();
            $mysql->statement('SELECT mtid FROM moduletypes WHERE name like ?', array( strtolower( $sectionname[1] ) ));
            $mtid = $mysql->singleresult();

            $mysql->statement('SELECT mtid FROM moduletypes WHERE name like ?', array( strtolower( @$sectionname[2] ) ));
            $smtid = $mysql->singleresult() ? $mysql->singleresult() : null;

            $mysql->statement('INSERT INTO layoutsectionmoduletypes (lsid, mtid, smtid, total) values(?, ?, ?, ?);', array(
                $lsid,
                $mtid,
                $smtid,
                @$sectionname[3] ? $sectionname[3] : 1
            ));
        }
        header("Location: /admin/layouts", true, 302);
        exit;
    } else {
        $tpl->setvar('ERRORS', '{LANG_LAYOUT_ALREADY_EXISTS}');
        $tpl->setcondition('ERRORS');
    }
}

$mysql->statement('SELECT * FROM layouttypes');

$types = array();
foreach ($mysql->result() as $item) {
    $types[$item['ltid']] = $item['name'];
}

$types = new select( array ( 'list' => $types, 'id' => 'LAYOUTTYPE' ) );
$types->object();

$templatefiles = scandir(CLIENTTPLPATH . '_layouts');

$templates = array();

foreach ($templatefiles as $file) {
    if ($file != '.' && $file != '..') {
        $templates[$file] = $file;
    }
}

$templates = new select( array ( 'list' => $templates, 'id' => 'LAYOUT' ) );
$templates->object();
