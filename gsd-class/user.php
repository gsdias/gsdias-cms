<?php
/*************************************
* File with user class information  *
*************************************/

interface iuser {
    public function reset ();
    public function islogged ();
    public function login ($user, $password);
    public function logout ();
    public function getuser ($uid);
}

class user implements iuser {
    
    public $level, $email, $name, $firstName, $lastName, $id, $notifications, $code;
    
    public function __construct ($id = 0) {
        
        $this->reset(); 

        if ($id !== 0) {
            $this->getuser($id);
        }

        return 0; 
    }
    
    public function reset () {
        
        $this->level = -1;
        $this->email = $this->name = $this->id = null;
        $this->notifications = array();
        $this->code = md5(sprintf("%s%s", rand(), time()));
    }

    public function islogged () { 
        global $mysql;

        $isLogged = $this->id != null;

        if ($isLogged) {
            $mysql->statement('SELECT sync FROM users WHERE uid = :uid', array(':uid' => $this->id));

            if ($mysql->singleresult()) {
                $mysql->statement('UPDATE users SET sync = 0 WHERE uid = :uid', array(':uid' => $this->id));
            }
        }

        return $isLogged; 
    }

    public function login ($email, $password, $extrafields = array()) {
        global $mysql;

        $result = false;
        $fields = 'code, level, name, uid, email';
        
        if (!empty($extrafields)) {
            foreach ($extrafields as $field) {
                $fields .= sprintf(", %s", $field);
            }
        }

        $mysql->statement('SELECT ' . $fields . '
        FROM users
        WHERE disabled IS NULL AND email = :email AND password = md5(:pass);', array(':email' => $email, ':pass' => $password));
        
        $result = $mysql->total === 1;
        
        if ($result) {
            $this->code = md5($_SERVER['REMOTE_ADDR'] + '' + time());
            $user = $mysql->singleline();
            $names = explode(' ', $user['name']);
            $this->id = $user['uid'];
            $this->level = $user['level'];
            $this->name = $user['name'];
            $this->firstName = array_shift($names);
            $this->lastName = array_pop($names);
            $this->email = $user['email'];
            $this->notifications = new notification($this->id);
            $_SESSION['user'] = $this;

            $mysql->statement('UPDATE users SET last_login = CURRENT_TIMESTAMP(), code = :code WHERE uid = :uid;',
              array(':uid' => $this->id, ':code' => $this->code)
             );
        }
        
        return !empty($extrafields) ? $user : $result;
    }
    
    public function logout(){
        unset($_SESSION);
        @session_destroy();
        header('location: /');
    }
    
    public function getuser ($uid) {
        global $mysql;

        $mysql->statement(sprintf('SELECT code, level, name, uid, email FROM users WHERE uid = :uid;'), array(':uid' => $uuid));

        if ($mysql->total === 1) {
            $user = $mysql->singleline();
            $names = explode(' ', $user['name']);

            $this->id = $user['uid'];
            $this->level = $user['level'];
            $this->name = $user['name'];
            $this->firstName = array_shift($names);
            $this->lastName = array_pop($names);
            $this->email = $user['email'];
            $this->notifications = new notification($this->id);
        }
    }
}
