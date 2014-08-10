<?php

#### DELETE

$DELETEapplication = function ($fields, $extra, $doc = false) {
    global $mysql, $api;

    if ($doc) {
        return $output;
    }

    $field = is_numeric($extra[0]) ? '' : $extra[0];
    $id = is_numeric($extra[0]) ? $extra[0] : $extra[1];
    $user = $api->user;

    $isUser = $user->level == 1 ? sprintf(' uid = %d AND ', $user->id) : '';

    $mysql->statement(sprintf('UPDATE applications SET %s = NULL WHERE %s aid = :id;', $field, $isUser), array(':id' => $id));

    syncReset($user->id);

    return;
};
