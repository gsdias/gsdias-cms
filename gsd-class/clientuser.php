<?php

class clientuser extends user {
    public function login ($email, $password) {
        $fields = array ('uid as aid');
        print_r(parent::login($email, $password, $fields));
    }
}