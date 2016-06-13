<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.7.2
 */
defined('GVALID') or die;

$notifications = array();
foreach($user->notifications->list as $notification) {
    $notifications[] = array(
        'MSG' => $notification['m'],
        'STATUS' => !$notification['s'] ? '(Unread)' : '',
        'CLASS' => !$notification['s'] ? 'highlighted' : '',
        'TIME' => $notification['t']
    );

}

$tpl->setarray('NOTIFICATIONS', array_reverse($notifications));

$user->notifications->mark();
$user->notifications->save();
