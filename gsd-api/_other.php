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

function fileChange ($name, $file, $id, $table, $field, $fieldid, $path, $notsendmail) {
    global $mysql, $api, $prefix;

    if (isset($file)) {
        $rename = $field == 'quota' ? md5($_REQUEST['id']) . '-' . date('Y', time()) : $id;
        $filename = savefile ($file[$name], $path, null, null, $rename);

        $mysql->statement(sprintf('UPDATE %s SET %s = :value WHERE %s = "%s"', $table, $field, $fieldid, $id), array(':value' => $filename));

        if ($notsendmail != 'true') {
            if ($field == 'pdf_seguro') {
                sendInsuranceEmail($id, $filename);
            }
            if ($field == 'pdf_pro') {
                sendAAEmail($id, $filename);
            }
            if ($field == 'word') {
                sendWordEmail($id, $filename);
            }
        }
        if ($field == 'comprovativo1' || $field == 'comprovativo2' || $field == 'comprovativo3') {
            $user = $api->user;

            foreach ($api->user->apps['ice']->stack as $app) {
                if ($app->aid === $id) {
                    $user->notifications[$prefix]->add(sprintf('Inseriu no sistema o comprovativo da %d&ordf; tranche relativo ao intercâmbio %s, %s a %s', substr($field, -1), $app->country, $app->title, date('d/m/Y', time())));
                    $user->notifications[$prefix]->save();
                    syncReset($user->id);
                }
            }
        }
    } else {
        return 0;
    }

    return $filename;
}

function syncReset($uid) {
    global $mysql;

    $mysql->statement(sprintf('UPDATE %s.users SET sync = 1 WHERE uid = :uid', DBSHARED), array(':uid' => $uid));
}
