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

interface isection {
    public function getlist ($numberPerPage);
    public function getcurrent ($id);
    public function generatefields ($id);
}
