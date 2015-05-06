<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.2
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

#### OTHER

function outputDoc ($table, $input, $returnFields) {
    global $mysql, $api;

    $output = array();
    $output['input'] = $input;
    $mysql->statement(sprintf('SHOW FULL COLUMNS FROM %s;', $table));

    foreach ($mysql->result() as $field) {
        if (in_array($field->Field, $returnFields))
            $output['output'][$field->Field] = $field->Comment;
    }
    return $output;
}

function paginatorPages($page, $numberPerPage, $output) {
    global $mysql, $api, $defaultValues;

    $returnFields = array('pid', 'title', 'beautify', 'created', 'creator_id', 'creator_name', 'index');

    $tags = @$_REQUEST['search'] ? sprintf(' WHERE p.title like "%%%s%%" ', $_REQUEST['search']) : '';

    $sql = ' FROM pages AS p
        LEFT JOIN users AS u ON p.creator = u.uid
        LEFT JOIN pages AS pp ON p.parent = pp.pid '
        . $tags .
        'ORDER BY p.`index` ';

    $paginator = new paginator($sql, $numberPerPage, $page);

    $mysql->statement('SELECT p.*, concat(if(pp.url = "/" OR pp.url IS NULL, "", pp.url), p.url) AS url, p.creator AS creator_id, u.name AS creator_name' . $sql . $paginator->pageLimit());

    if ($mysql->total) {
        $output['message'] = '';
        $output['data']['list'] = array();
        foreach ($mysql->result() as $row) {
            $array = $array = $defaultValues($row, $returnFields);

            $created = explode(' ', @$row->created);
            $array['created'] = '(' . timeago(dateDif(@$created[0], date('Y-m-d', time())), @$created[1]) . ')';
            $array['unpublished'] = $row->published ? '' : '<br>(' . lang('LANG_UNPUBLISHED') . ')';
            array_push($output['data']['list'], $array);
        }

        $output['data']['paginator'] = (string)$paginator;
    }

    return $output;
};

function paginatorImages($page, $numberPerPage, $output) {
    global $mysql, $api, $defaultValues;

    $returnFields = array('iid', 'description', 'creator_id', 'creator_name');

    $tags = @$_REQUEST['search'] ? sprintf(' WHERE tags like "%%%s%%" ', $_REQUEST['search']) : '';

    $sql = ' FROM images
        LEFT JOIN users AS u ON images.creator = u.uid '
        . $tags .
        'ORDER BY images.iid ';

    $paginator = new paginator($sql, $numberPerPage, $page);

    $mysql->statement('SELECT images.*, images.creator AS creator_id, u.name AS creator_name' . $sql . $paginator->pageLimit());

    if ($mysql->total) {
        $output['message'] = '';
        $output['data']['list'] = array();
        foreach ($mysql->result() as $row) {
            $array = $defaultValues($row, $returnFields);

            $created = explode(' ', @$row->created);
            $array['created'] = '(' . timeago(dateDif(@$created[0], date('Y-m-d', time())), @$created[1]) . ')';
            $array['asset'] = @$row->width ? (string)new image(array('iid' => $row->iid, 'max-height' => '100', 'height' => 'auto', 'width' => 'auto')) : '';
            $array['size'] = sprintf('<strong>%s x %s</strong><br>%s', $row->width, $row->height, $row->size);
            array_push($output['data']['list'], $array);
        }

        $output['data']['paginator'] = (string)$paginator;
    }

    return $output;
};

$defaultValues = function ($row, $returnFields) {
    $array = array();

    foreach ($row as $visible => $value) {
        if (in_array($visible, $returnFields)) {
            $array[$visible] = $value;
        }
    }

    return $array;
};
