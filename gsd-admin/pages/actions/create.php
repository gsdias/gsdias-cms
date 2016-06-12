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
    $result = $csection->add();

    if ($result['total']) {
        $mysql->reset()
            ->delete()
            ->from('redirect')
            ->where('`from` = ?')
            ->values(array(escapeText($site->p('url'))))
            ->exec();
        
        $pid = $result['id'];

        $mysql->reset()
            ->select()
            ->from('layoutsections AS ls')
            ->join('layoutsectionmoduletypes AS lsmt')
            ->on('lsmt.lsid = ls.lsid')
            ->where('lid = ?')
            ->values(array($site->p('lid')))
            ->exec();

        $pmid = array();

        foreach ($mysql->result() as $section) {
            $defaultdata = array('class' => '', 'style' => '', 'value' => '');
            $data = array('list' => array(array_fill(0, $section->total, $defaultdata)), 'class' => '', 'style' => '');
            $mysql->reset()
                ->insert('pagemodules')
                ->fields(array('lsid', 'mtid', 'pid', 'data', 'creator'))
                ->values(array($section->lsid, $section->mtid, $pid, serialize($data), $user->id))
                ->exec();

            array_push($pmid, $mysql->lastInserted());
        }
        
        $result['pmid'] = $pmid;

        if (!isset($api)) {
            $tpl->setarray('MESSAGES', array('MSG' => sprintf(lang('LANG_PAGE_CREATED'), $site->p('title'))));
            redirect(sprintf("/admin/%s/%d/edit", $site->a(1), $result['id']));
        }
    } else {
        if (!isset($api)) {
            $csection->showErrors(lang('LANG_PAGE_ALREADY_EXISTS'));
        }
    }
}
