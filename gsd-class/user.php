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

class user implements iuser
{
    public $level, $email, $name, $firstName, $lastName, $id, $notifications, $code, $locale;

    public function __construct($id = 0)
    {
        $this->reset();

        if ($id !== 0) {
            $this->getuser($id);
        }

        return 0;
    }

    public function reset()
    {
        $this->level = -1;
        $this->email = $this->name = $this->id = null;
        $this->notifications = array();
        $this->code = md5(sprintf('%s%s', rand(), time()));
    }

    public function islogged()
    {
        global $mysql, $tpl;

        $isLogged = $this->id != null;

        if ($isLogged) {
            $mysql->statement('SELECT sync FROM users WHERE uid = :uid', array(':uid' => $this->id));
            if ($mysql->singleresult()) {
                $this->getuser($this->id);
                $mysql->statement('UPDATE users SET sync = 0 WHERE uid = :uid', array(':uid' => $this->id));
            }
        }

        return $isLogged;
    }

    public function login($email, $password, $extrafields = array())
    {
        global $mysql, $site;

        $result = false;
        $fields = 'code, level, name, uid, email, locale';
        $user = array();

        if (!empty($extrafields)) {
            foreach ($extrafields as $field) {
                $fields .= sprintf(', %s', $field);
            }
        }

        $mysql->statement('SELECT '.$fields.'
        FROM users
        WHERE disabled IS NULL AND deleted IS NULL AND email = ? AND password = md5(?);', array($email, $password));

        $result = $mysql->total === 1;

        if ($result) {
            $this->code = md5($_SERVER['REMOTE_ADDR'] + '' + time());
            $user = $mysql->singleline();

            if ($user->level == 'user' && $site->a(0) == 'admin') {
                return 0;
            }

            $names = explode(' ', $user->name);
            $this->id = $user->uid;
            $this->level = $user->level;
            $this->locale = $user->locale;
            $this->name = $user->name;
            $this->firstName = array_shift($names);
            $this->lastName = array_pop($names);
            $this->email = $user->email;
            $this->notifications = new notification($user->uid);
            $_SESSION['user'] = $this;

            $mysql->statement('UPDATE users SET last_login = CURRENT_TIMESTAMP(), code = ? WHERE uid = ?;',
              array($this->code, $this->id)
             );
        }

        return !empty($extrafields) ? $user : $result;
    }

    public function logout()
    {
        unset($_SESSION);
        @session_destroy();
        redirect('/');
    }
    
    public function resetpassword($field)
    {
        global $mysql, $site, $config;

        $newpassword = substr(str_shuffle(sha1(rand().time().'gsdias-cms')), 2, 10);
        
        $mysql->reset()
                ->update('users')
                ->fields('password')
                ->where('email = ?')
                ->values(array(md5($newpassword), $field))
                ->exec();

        $result = $mysql->total === 1;

        if ($result) {
            $email = new email();

            $email->setto($field);
            $email->setfrom($site->email);
            $email->setreplyto($site->email);
            $email->setsubject(lang('LANG_RECOVER_SUBJECT'));
            $email->setvar('sitename', $site->name);
            $email->setvar('siteurl', $config['url']);
            $email->setvar('password', $newpassword);

            $template = is_file(CLIENTTPLPATH.'_emails/recover'.TPLEXT) ? CLIENTTPLPATH.'_emails/recover'.TPLEXT : TPLPATH.'_emails/recover'.TPLEXT;

            $email->settemplate($template);
            $email->sendmail();
        }
    }

    public function getuser($uid)
    {
        global $mysql;

        $mysql->statement(sprintf('SELECT code, level, name, uid, email FROM users WHERE uid = ?;'), array($uid));

        if ($mysql->total === 1) {
            $user = $mysql->singleline();
            $names = explode(' ', $user->name);

            $this->id = $user->uid;
            $this->level = $user->level;
            $this->name = $user->name;
            $this->firstName = array_shift($names);
            $this->lastName = array_pop($names);
            $this->email = $user->email;
            $this->notifications = new notification($this->id);
        }
    }
}
