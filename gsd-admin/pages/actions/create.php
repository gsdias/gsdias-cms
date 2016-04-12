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
    $defaultfields = array(
        array('title', array('isString')),
        array('url', array('isString')),
        array('lid', array('isNumber')),
        array('description', array('isString')),
        array('keywords', array('isString')),
        array('tags', array('isString')),
        array('og_title', array('isString')),
        array('og_image', array('isNumber')),
        array('og_description', array('isString')),
        array('parent', array('isNumber')),
        array('show_menu', array('isCheckbox')),
        array('require_auth', array('isCheckbox'))
    );

    $fields = array(
        'creator',
        'index',
        'created'
    );

    $mysql->reset()
        ->select('max(`index`) AS max')
        ->from('pages')
        ->exec();

    $index = @$mysql->singleresult();

    $values = array(
        $user->id,
        ($index != null ? $index + 1 : 0),
        date('Y-m-d H:i:s', time()),
    );

    $result = $csection->add($defaultfields, $fields, $values);

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

            while (!empty($result['errmsg'])) {
                $tpl->setvar('ERRORS', array_pop($result['errmsg']));
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
