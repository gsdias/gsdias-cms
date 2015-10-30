<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.3
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
if (!$csection->permission) {
    header('Location: /admin/'.$site->arg(1), true, 302);
    exit;
}

if (@$_REQUEST['save']) {
    $defaultfields = array('name', 'ltid', 'file');

    $fields = array('creator');

    $values = array($user->id);

    $content = file_get_contents(sprintf(CLIENTTPLPATH.'_layouts/%s', $_REQUEST['layout']));

    $_REQUEST['file'] = str_replace('.html', '', $_REQUEST['layout']);

    $result = $csection->add($defaultfields, $fields, $values);

    $lid = $result['id'];

    if ($lid) {
        preg_match_all('#<!-- PLACEHOLDER (.*?) -->#s', $content, $matches, PREG_SET_ORDER);
        $list = array();
        foreach ($matches as $match) {
            array_push($list, $match[1]);
        }
        $addedmodules = array();
        while ($key = array_pop($list)) {
            $sectionname = explode(' ', $key);

            if (in_array($sectionname[0], $addedmodules)) {
                continue;
            }

            array_push($addedmodules, $sectionname[0]);

            $mysql->reset()
                ->insert('layoutsections')
                ->fields(array('lid', 'label', 'creator'))
                ->values(array($lid, $sectionname[0], $user->id))
                ->exec();
            $lsid = $mysql->lastinserted();
            $mysql->reset()
                ->select('mtid')
                ->from('moduletypes')
                ->where('name like ?')
                ->values(strtolower($sectionname[1]))
                ->exec();
            $mtid = $mysql->singleresult()->mtid;

            $mysql->reset()
                ->select('mtid')
                ->from('moduletypes')
                ->where('name like ?')
                ->values(strtolower(@$sectionname[2]))
                ->exec();
            $smtid = @$mysql->singleresult()->mtid ? $mysql->singleresult()->mtid : null;

            $mysql->reset()
                ->insert('layoutsectionmoduletypes')
                ->fields(array('lsid', 'mtid', 'smtid', 'total'))
                ->values(array($lsid, $mtid, $smtid, @$sectionname[3] ? $sectionname[3] : 1))
                ->exec();
        }
        $_SESSION['message'] = sprintf(lang('LANG_LAYOUT_CREATED'), $_REQUEST['name']);
        header('Location: /admin/'.$site->arg(1), true, 302);
        exit;
    } else {
        $tpl->setvar('ERRORS', lang('LANG_LAYOUT_ALREADY_EXISTS'));
        $tpl->setcondition('ERRORS');
    }
}

$mysql->reset()
    ->select()
    ->from('layouttypes')
    ->exec();

$types = array();
foreach ($mysql->result() as $item) {
    $types[$item->ltid] = $item->name;
}

$types = new GSD\select(array('list' => $types, 'id' => 'LAYOUTTYPE'));
$types->object();

$templatefiles = scandir(CLIENTTPLPATH.'_layouts');

$templates = array();

foreach ($templatefiles as $file) {
    if ($file != '.' && $file != '..') {
        $templates[$file] = $file;
    }
}

$templates = new GSD\select(array('list' => $templates, 'id' => 'LAYOUT'));
$templates->object();
