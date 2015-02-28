<?php

#### OTHER

function outputDoc ($table, $input, $returnFields) {
    global $mysql, $api;

    $output = array();
    $output['input'] = $input;
    $mysql->statement(sprintf('SHOW FULL COLUMNS FROM %s;', $table));

    foreach ($mysql->result() as $field) {
        if (in_array($field['Field'], $returnFields))
            $output['output'][$field['Field']] = $field['Comment'];
    }
    return $output;
}

function syncReset($uid) {
    global $mysql;

    $mysql->statement(sprintf('UPDATE %s.users SET sync = 1 WHERE uid = :uid', DBSHARED), array(':uid' => $uid));
}
