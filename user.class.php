<?php
/*************************************
* File with user class information  *
*************************************/

class user {
    public $level, $email, $name, $id, $code;
    public function __construct($id = 0) { 
        $this->reset(); 

        if ($id !== 0) {
            $this->getuser($id);
        }

        return 0; 
    }
    public function reset() {
        $this->level = -1;
        $this->email = $this->name = $this->firstName = $this->lastName = $this->photo = $this->id = null;
        $this->notifications = array();
        $this->code = md5(sprintf("%s%s", rand(), time()));
    }
    public function setemail($value) {
        $this->email = $value;
    }
    public function setlevel($value) {
        $this->level = $value;
    }
    public function setname($value) {
        $this->name = $value;
    }
    public function setforward($value) {
        $this->forward = $value;
    }
    
    public function islogged () { 
        global $mysql;
        $isLogged = $this->email != null;

        if ($isLogged) {
            $mysql->statement('SELECT sync FROM users WHERE uid = :uid'), array(':uid' => $this->id));

            if ($mysql->singleresult()) {
                $this->getuser($this->id);
                $mysql->statement('UPDATE users SET sync = 0 WHERE uid = :uid'), array(':uid' => $this->id));
            }
        }

        return $isLogged; 
    }

    public function login ($user, $password, $check = null) {
        global $mysql, $lang, $prefix2, $prefix;
        if (!filter_var($user, FILTER_VALIDATE_EMAIL)) return 0;
        $mysql->statement('SELECT code, level, name, uid, email
        FROM users
        WHERE email = :email AND password = :pass AND (disabled = 0 OR disabled IS NULL);', array(':email' => $user, ':pass' => md5($password)));
        if ($mysql->total === 1) {
            $this->code = md5($_SERVER['REMOTE_ADDR'] + '' + time());
            $userfound = $mysql->singleline();
            $firstName = explode(' ', $userfound['name']);
            $this->id = $userfound['uid'];
            $this->level = $userfound['level'];
            $this->name = $userfound['name'];
            $this->firstName = $firstName[0];
            $this->lastName = $firstName[sizeof($firstName) - 1];
            $this->email = $userfound['email'];
            $this->notifications['sve'] = new notification($userfound['uid'], 'sve');
            $this->notifications['ice'] = new notification($userfound['uid'], 'ice');
            $this->notifications['proatlantico'] = new notification($userfound['uid'], 'proatlantico');
            $this->apps['sve'] = new applications($userfound['uid'], 'sve');
            $this->apps['ice'] = new applications($userfound['uid'], 'ice');
            $_SESSION['user'] = $this;

            $mysql->statement('UPDATE users
                                  SET last_login = :time, code = :code
                                  WHERE uid = :uid;', 
                              array(':time' => time(),
                                    ':uid' => $this->id,
                                    ':code' => $this->code
                                   )
                             );
            $this->type = 1;
            return 1;
        }
        
        return 0;
    }
    public function logout(){
        unset($_SESSION);
        @session_destroy();
        @session_start();
        $this->reset();
    }
    public function setpassword ($password) {
        global $mysql;
    }
    public function getuser ($user) {
        global $mysql, $lang, $prefix;
        $mysql->statement(sprintf('SELECT code, level, name, uid, email
        FROM users WHERE uid = :uid;'), array(':uid' => $user));
        if ($mysql->total === 1) {
            $userfound = $mysql->singleline();
            $firstName = explode(' ', $userfound['name']);
            $this->id = $userfound['uid'];
            $this->level = $userfound['level'];
            $this->name = $userfound['name'];
            $this->firstName = $firstName[0];
            $this->lastName = $firstName[sizeof($firstName) - 1];
            $this->email = $userfound['email'];
            $this->notifications['sve'] = new notification($userfound['uid'], 'sve');
            $this->notifications['ice'] = new notification($userfound['uid'], 'ice');
            $this->notifications['proatlantico'] = new notification($userfound['uid'], 'proatlantico');
            $this->apps['sve'] = new applications($userfound['uid'], 'sve');
            $this->apps['ice'] = new applications($userfound['uid'], 'ice');
            $this->type = 1;
        }
    }
}
