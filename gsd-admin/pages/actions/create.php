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
if (!$csection->permission) {
    redirect('/admin/'.$site->arg(1));
}

if (@$_REQUEST['save']) {
    $fields = array(
        array('title', array('isRequired', 'isString'), lang('LANG_TITLE')),
        array('url', array('isRequired', 'isString'), lang('LANG_URL')),
        array('lid', array('isRequired', 'isNumber'), lang('LANG_LAYOUT')),
        array('description', array('isString'), lang('LANG_DESCRIPTION')),
        array('keywords', array('isString'), lang('LANG_KEYWORDS')),
        array('tags', array('isString'), lang('LANG_TAGS')),
        array('og_title', array('isString'), lang('LANG_OG_TITLE')),
        array('og_image', array('isNumber'), lang('LANG_OG_IMAGE')),
        array('og_description', array('isString'), lang('LANG_OG_DESCRIPTION')),
        array('parent', array('isNumber'), lang('LANG_PARENT')),
        array('show_menu', array('isCheckbox'), lang('LANG_SHOW_MENU')),
        array('require_auth', array('isCheckbox'), lang('LANG_REQUIRE_AUTH')),
        array('creator', array('isNumber')),
        array('index', array('isNumber')),
        array('created', array('isRequired')),
    );

    $result = $csection->add($fields);

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

if (!isset($api)) {
    $mysql->reset()
        ->select('lid, name')
        ->from('layouts')
        ->exec();

    $types = array();
    foreach ($mysql->result() as $item) {
        $types[$item->lid] = $item->name;
    }

    $types = new GSD\select(array('list' => $types, 'id' => 'LAYOUT'));
    $types->object();

    $mysql->reset()
        ->select('pid, title')
        ->from('pages')
        ->exec();

    $types = array();
    foreach ($mysql->result() as $item) {
        $types[$item->pid] = $item->title;
    }

    $types = new GSD\select(array('list' => $types, 'id' => 'PARENT'));
    $types->object();
}
