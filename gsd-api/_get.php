<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.2
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

#### GET

$GETimages = function ($fields, $extra, $doc = false) {
    global $mysql, $api;
    $output = array('error' => 0, 'message' => lang('LANG_NO_IMAGES'));
    $requiredFields = array();
    $returnFields = array('iid', 'name', 'width', 'height', 'extension');

    if ($doc) {
        return outputDoc('images', array('iid' => 'Identification of the image'), $returnFields);
    }

    if (!$api->requiredFields($fields, $requiredFields)) {
        return array('error' => -3, 'message' => 'Missing required fields' );
    }

    $tags = @$fields['search'] ? sprintf('WHERE tags like "%%%s%%"', $fields['search']) : '';

    $mysql->statement('SELECT * FROM images ' . $tags . ';');

    if ($mysql->total) {
        $output['message'] = '';
        $output['data']['list'] = array();
        foreach ($mysql->result() as $row) {
            $array = array();
            foreach ($row as $visible => $value) {
                if (in_array($visible, $returnFields)) {
                    $array[$visible] = $value;
                }
            }
            array_push($output['data']['list'], $array);
        }
        return $output['data']['list'];
    }
    return $output;
};

$GETpages = function ($fields, $extra, $doc = false) {
    global $mysql, $api;
    $output = array('error' => 0, 'message' => lang('LANG_NO_IMAGES'));
    $requiredFields = array('page');
    $returnFields = array('pid', 'title', 'beautify', 'created', 'creator_id', 'creator_name', 'index');

    if ($doc) {
        return outputDoc('pages', array('pages' => 'Page number'), $returnFields);
    }

    if (!$api->requiredFields($fields, $requiredFields)) {
        return array('error' => -3, 'message' => 'Missing required fields' );
    }

    $sql = ' FROM pages AS p
        LEFT JOIN users AS u ON p.creator = u.uid
        LEFT JOIN pages AS pp ON p.parent = pp.pid
        ORDER BY p.`index` ';

    $mysql->statement('SELECT p.*, concat(if(pp.url = "/" OR pp.url IS NULL, "", pp.url), p.url) AS url, p.creator AS creator_id, u.name AS creator_name' . $sql . pageLimit($fields['page'], 10));

    if ($mysql->total) {
        $output['message'] = '';
        $output['data']['list'] = array();
        foreach ($mysql->result() as $row) {
            $array = array();
            foreach ($row as $visible => $value) {
                if (in_array($visible, $returnFields)) {
                    $array[$visible] = $value;
                }
            }
            $created = explode(' ', @$row->created);
            $array['created'] = '(' . timeago(dateDif(@$created[0], date('Y-m-d', time())), @$created[1]) . ')';
            $array['unpublished'] = $row->published ? '' : '<br>(' . lang('LANG_UNPUBLISHED') . ')';
            array_push($output['data']['list'], $array);
        }

        $tpl = new tpl();
        $tpl->setcondition('PAGINATOR');
        $pages = pageGenerator($sql, 10);

        $first_page = new anchor(array('text' => '&lt; {LANG_FIRST}', 'href' => '?page=1'));
        $prev_page = new anchor(array('text' => lang('LANG_PREVIOUS'), 'href' => '?page=' . $pages['PREV']));
        $next_page = new anchor(array('text' => lang('LANG_NEXT'), 'href' => '?page=' . $pages['NEXT']));
        $last_page = new anchor(array('text' => '{LANG_LAST} &gt;', 'href' => '?page=' . $pages['LAST']));
        $tpl->setvars(array(
            'FIRST_PAGE' => $first_page,
            'PREV_PAGE' => $prev_page,
            'NEXT_PAGE' => $next_page,
            'LAST_PAGE' => $last_page,
            'CURRENT_PAGE' => $pages['CURRENT'],
            'TOTAL_PAGES' => $pages['TOTAL']
        ));
        $tpl->includeFiles('MAIN', '_paginator');
        $tpl->setFile('_paginator');

//        $output['data']['paginator'] = pageTotal($sql, 10);
        $output['data']['paginator'] = sprintf('%s', $tpl);
    }
    return $output;
};
