<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.0
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

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
