<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.5.1
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

if (@$_REQUEST['save']) {
    $result = $csection->add();

    if ($result['total']) {
        
        $mysql->reset()
            ->delete()
            ->from('redirect')
            ->where('`from` = ?')
            ->values(
                array(
                    escapeText($_REQUEST['url'])
                )
            )
            ->exec();
        
        $pid = $result['id'];

        $mysql->reset()
            ->select()
            ->from('layoutsections AS ls')
            ->join('layoutsectionmoduletypes AS lsmt')
            ->on('lsmt.lsid = ls.lsid')
            ->where('lid = ?')
            ->values(array(@$_REQUEST['lid']))
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
            $_SESSION['message'] = sprintf(lang('LANG_PAGE_CREATED'), $_REQUEST['title']);
            header("Location: /admin/pages/$pid/edit", true, 302);
            exit;
        }
    } else {
        if (!isset($api)) {
            if ($result['errnum'] === 1000) {
                array_unshift($result['errmsg'], lang('LANG_PAGE_ALREADY_EXISTS'));
            }

            foreach($result['errmsg'] as $msg) {
                $tpl->setvar('ERRORS', $msg.'<br>');
            }

            $tpl->setcondition('ERRORS');
        }
    }
}
