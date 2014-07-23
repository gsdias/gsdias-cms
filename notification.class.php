<?php
/*************************************
* File with user class information  *
*************************************/

class notification {
    public $total, $list, $read, $unread, $uid;
    public function __construct($uid, $prefix) { 
        $this->reset($uid, $prefix); return $this; 
        
    }
    public function reset($uid, $prefix) {
        global $mysql;
        
        $mysql->statement(sprintf('SELECT nota
        FROM %s.users WHERE uid = :uid;', $prefix), array(':uid' => $uid));
        
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
        
    public function mark () {
        $read = array();
        
        foreach($this->unread as $notification) {
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
    
    public function add ($message) {
        $notification = array('m' => $message, 's' => 0);
        
        array_push($this->unread, $notification);
        array_push($this->list, $notification);
        $this->total = sizeof($this->unread);
    }
    
    public function save () {
        global $user, $mysql;
        
        $notifications = json_encode($this->list);
        
        $mysql->statement("INSERT IGNORE INTO users (uid, nota) VALUES (:uid, :nota);UPDATE users SET nota = :nota WHERE uid = :uid", array(':nota' => $notifications, ':uid' => $this->uid));
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
