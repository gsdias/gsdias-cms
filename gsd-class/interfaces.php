<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.5
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
namespace GSD;

interface idatabase
{
    public function statement($query, $values);
    public function execute($values);
    public function result();
    public function singleresult();
    public function singleline();
    public function close();
    public function lastInserted();
}

interface iuser
{
    public function reset();
    public function islogged();
    public function login($user, $password);
    public function logout();
    public function getuser($uid);
}

interface isection
{
    public function getlist($options);
    public function getcurrent($id);
    public function generatefields($initial);
    public function add();
    public function edit();
    public function remove();
}
