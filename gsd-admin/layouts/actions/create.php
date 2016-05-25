<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

if (@$_REQUEST['save']) {
    $content = file_get_contents(sprintf(CLIENTTPLPATH.'_layouts/%s', $_REQUEST['file']));

    $_REQUEST['file'] = str_replace('.html', '', $_REQUEST['file']);

    $result = $csection->add();

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
            $mtid = $mysql->singleresult();

            $mysql->reset()
                ->select('mtid')
                ->from('moduletypes')
                ->where('name like ?')
                ->values(strtolower(@$sectionname[2]))
                ->exec();
            $smtid = @$mysql->singleresult() ? $mysql->singleresult() : null;

            $mysql->reset()
                ->insert('layoutsectionmoduletypes')
                ->fields(array('lsid', 'mtid', 'smtid', 'total'))
                ->values(array($lsid, $mtid, $smtid, @$sectionname[3] ? $sectionname[3] : 1))
                ->exec();
        }
    }

    if (!$csection->showErrors(lang('LANG_LAYOUT_ALREADY_EXISTS'))) {
        $tpl->setarray('MESSAGES', array('MSG' => sprintf(lang('LANG_LAYOUT_CREATED'), $_REQUEST['name'])));

        redirect('/admin/'.$site->arg(1));
    }
}
