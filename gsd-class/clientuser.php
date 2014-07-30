<?php

class clientuser extends user {

    public function login ($email, $password) {
        $fields = array ('uid as aid');

        $result = parent::login($email, $password, $fields);

        return !empty($result);
    }
}
