<?php

#### GET

$GETimages = function ($fields, $extra, $doc = false) {
    global $mysql, $api;
    $output = array('error' => 0, 'message' => 'Não existem imagens');
    $requiredFields = array();
    $returnFields = array('iid', 'name', 'width', 'height', 'extension');

    if ($doc) {
        return outputDoc('images', array('iid' => 'Identification of the image'), $returnFields);
    }

    if (!$api->requiredFields($fields, $requiredFields)) {
        return array('error' => -3, 'message' => 'Missing required fields' );
    }
    $mysql->statement('SELECT * from images;');

    if ($mysql->total) {
        $output['message'] = '';
        $output['data']['list'] = array();
        foreach ($mysql->result() as $row) {
            $array = array();
            foreach ($row as $visible => $value) {

                if (!is_numeric($visible) && in_array($visible, $returnFields)) {

                    $array[$visible] = $value;

                }
            }

            array_push($output['data']['list'], $array);

        }
        return $output['data']['list'];
    }

    return $output;

};
