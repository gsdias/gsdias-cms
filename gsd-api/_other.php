<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.1
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
