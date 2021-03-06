<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
namespace GSD;
defined('GVALID') or die;

interface inotification
{
    public function reset($uid);
    public function mark();
    public function add($message);
    public function save();
}

class notification implements inotification
{
    public $total, $list, $read, $unread, $uid;

    public function __construct($uid = null)
    {
        $this->reset($uid);

        return $this;
    }
    public function reset($uid)
    {
        global $mysql, $tpl;

        $mysql->statement('SELECT notifications FROM users WHERE uid = ?;', array($uid));

         $notifications = json_decode($mysql->singleresult(), true);

        $this->read = $this->unread = array();
        $this->list = is_array($notifications) ? $notifications : array();
        $this->uid = $uid;

        if (sizeof($this->list)) {
            foreach ($this->list as $notification) {
                if ($notification['s'] == 1) {
                    $this->read[] = $notification;
                } else {
                    $this->unread[] = $notification;
                }
            }
        }

        $this->total = sizeof($this->unread);
    }

    public function mark()
    {
        $read = array();

        foreach ($this->unread as $notification) {
            $notification['s'] = 1;
            $read[] = $notification;
            $read[sizeof($read) - 1]['s'] = 1;
        }
        $merged = array_merge($this->read, $read);
        $this->read = $merged;
        $this->unread = array();

        $this->list = $merged;
        $this->total = 0;
    }

    public function add($message)
    {
        $notification = array('m' => $message, 's' => 0, 't' => date('Y-m-d', time()));

        array_push($this->unread, $notification);
        array_push($this->list, $notification);
        $this->total = sizeof($this->unread);
    }

    public function save()
    {
        global $mysql;

        $notifications = json_encode($this->list);

        $mysql->statement('UPDATE users SET sync = 1, notifications = ? WHERE uid = ?', array($notifications, $this->uid));
    }
}

/*
Array (
    [0] => Array (
        [m] => teste
        [s] => 0
    )
    [1] => Array (
        [m] => teste 1
        [s] => 1
    )
    [2] => Array (
        [m] => teste 2
        [s] => 0
    )
)
*/
