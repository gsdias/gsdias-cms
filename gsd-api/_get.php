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
    $requiredFields = array('page', 'type');
    $returnFields = array();
    $numberPerPage = 10;

    if ($doc) {
        return outputDoc('pages', array('pages' => 'Page number', 'type' => 'Type list'), $returnFields);
    }

    if (!$api->requiredFields($fields, $requiredFields)) {
        return array('error' => -3, 'message' => 'Missing required fields' );
    }

    $functionname = 'paginator' . ucwords($fields['type']);

    $output = $functionname($fields['page'], $numberPerPage, $output);

    return $output;
};
