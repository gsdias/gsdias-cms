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
if (!IS_ADMIN && !IS_EDITOR) {
    header('Location: /admin/'.$site->arg(1), true, 302);
    exit;
}

if (@$_REQUEST['save']) {
    $defaultfields = array('title', 'url', 'lid', 'description', 'keywords', 'tags', 'og_title', 'og_image', 'og_description', 'parent');

    $fields = array('creator', 'index', 'show_menu', 'require_auth', 'created');

    $mysql->reset()
        ->delete()
        ->from('redirect')
        ->where('`from` = ?')
        ->values(array($_REQUEST['url']))
        ->exec();

    $mysql->reset()
        ->select('max(`index`) AS max')
        ->from('pages')
        ->exec();

    $index = @$mysql->singleresult()->max;

    $values = array(
        $user->id,
        ($index != null ? $index + 1 : 0),
        @$_REQUEST['menu'] ? @$_REQUEST['menu'] : null,
        @$_REQUEST['auth'] ? @$_REQUEST['auth'] : null,
        date('Y-m-d H:i:s', time()),
    );

    $result = $csection->add($defaultfields, $fields, $values);

    if ($result['total']) {
        $pid = $result['id'];

        $mysql->reset()
            ->select()
            ->from('layoutsections AS ls')
            ->join('layoutsectionmoduletypes AS lsmt')
            ->on('lsmt.lsid = ls.lsid')
            ->where('lid = ?')
            ->values(array(@$_REQUEST['lid']))
            ->exec();

        foreach ($mysql->result() as $section) {
            $defaultdata = array('class' => '', 'style' => '', 'value' => '');
            $data = array('list' => array(array_fill(0, $section->total, $defaultdata)), 'class' => '', 'style' => '');
            $mysql->reset()
                ->insert('pagemodules')
                ->fields(array('lsid', 'mtid', 'pid', 'data', 'creator'))
                ->values(array($section->lsid, $section->mtid, $pid, serialize($data), $user->id))
                ->exec();
        }

        $_SESSION['message'] = sprintf(lang('LANG_PAGE_CREATED'), $_REQUEST['title']);
        header("Location: /admin/pages/$pid/edit", true, 302);
        exit;
    } else {
        $tpl->setvar('ERRORS', lang('LANG_PAGE_ALREADY_EXISTS'));
        $tpl->setcondition('ERRORS');
    }
}

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
