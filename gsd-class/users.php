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

class users extends section implements isection
{
    public function __construct($permission = null)
    {
        global $tpl, $site, $user;

        $permission = gettype($permission) === 'boolean' ? $permission : IS_ADMIN || $site->a(2) === $user->id;
        $result = parent::__construct($permission);

        $tpl->setvar('SECTION_TYPE', lang('LANG_USER', 'LOWER'));
        if ($site->a(2) === 'create') {
            $tpl->repvar('SECTION_ACTION', lang('LANG_NEW_MALE'));
        }
        
        $this->labels = array(
            'singular' => 'LANG_USER',
            'plural' => 'LANG_USERS'
        );

        return $result;
    }
    
    public function getlist($options)
    {
        global $mysql, $tpl;

        $_fields = 'users.*, users.creator AS creator_id, u.name AS creator_name';
        $fields = empty($options['fields']) ? array() : $options['fields'];

        $mysql->reset()
            ->from('users')
            ->join('users AS u', 'LEFT')
            ->on('users.creator = u.uid')
            ->where('users.deleted IS NULL');

        if ($options['search']) {
            $mysql->where(sprintf('users.name like "%%%s%%"', $options['search']));
        }

        $mysql->order('users.uid');
        $paginator = new paginator($options['numberPerPage'], $options['page'], $this->labels);

        if (!empty($fields)) {
            foreach ($fields as $field) {
                $_fields .= sprintf(', %s', $field);
            }
        }

        $mysql->select($_fields)
            ->limit($paginator->pageLimit(), $options['numberPerPage'])
            ->exec();

        $result = parent::getlist(array(
            'search' => $options['search'],
            'results' => $mysql->result(),
            'fields' => array_merge(array('uid', 'name', 'level', 'creator_name', 'creator_id'), $fields),
            'paginator' => $paginator,
        ));

        if (!empty($result['list'])) {
            foreach ($result['results'] as $index => $item) {
                $last_login = explode(' ', @$item->last_login);

                $result['list'][$index]['LAST_LOGIN'] = sizeof($last_login) ? ($last_login[0] ? timeago(dateDif($last_login[0], date('Y-m-d', time())), $last_login[1]) : lang('LANG_NEVER')) : '';
                $result['list'][$index]['DISABLED'] = $item->disabled ? '<br>({LANG_DISABLED})' : '';
            }

            $tpl->setarray('USERS', $result['list'], 0);
        }

        return $result;
    }

    public function getcurrent($id = 0)
    {
        global $tpl, $mysql;

        $mysql->reset()
            ->select('users.*, users.created, users.creator AS creator_id, u.name AS creator_name')
            ->from('users')
            ->join('users AS u', 'left')
            ->on('users.creator = u.uid')
            ->where('users.uid = ?')
            ->values($id);

        $result = parent::getcurrent();

        if (!empty($this->item)) {
            $item = $this->item;
            $created = explode(' ', $item->created);

            $result['CURRENT_USERS_STATUS'] = !$item->disabled ? lang('LANG_ENABLED') : lang('LANG_DISABLED');

            $tpl->repvars($result);
        }

        return $result;
    }

    public function edit()
    {
        return parent::edit();
    }

    public function add($emailparams = array())
    {
        global $site, $GSDConfig;

        $password = substr(str_shuffle(sha1(rand().time().'gsdias-cms')), 2, 10);

        $_REQUEST['password'] = $password;

        $result = parent::add();

        if (empty($result['errmsg'])) {
            $email = new email();

            $email->setto(@$emailparams['email'] ? $emailparams['email'] : $site->p('email'));
            $email->setfrom($site->email);
            $email->setreplyto($site->email);
            $email->setsubject(lang('LANG_REGISTER_SUBJECT'));
            $email->setvar('sitename', $site->name);
            $email->setvar('siteurl', $GSDConfig->url);
            $email->setvar('password', $password);

            if (sizeof(@$emailparams['fields'])) {
                foreach ($emailparams['fields'] as $key => $value) {
                    $email->setvar(strtolower($key), $value);
                }
            }

            $template = is_file(CLIENTTPLPATH.'_emails/register'.TPLEXT) ? CLIENTTPLPATH.'_emails/register'.TPLEXT : TPLPATH.'_emails/register'.TPLEXT;

            $email->settemplate($template);
            $email->sendmail();
        }
        return $result;
    }
    
    protected function fields($update = false)
    {
        global $GSDConfig;
        
        $fields = array();
        
        $fields[] = new field(array('name' => 'name', 'label' => lang('LANG_NAME'), 'validator' => array('isRequired', 'isString')));
        $fields[] = new field(array('name' => 'email', 'label' => lang('LANG_EMAIL'), 'validator' => array('isRequired', 'isEmail'), 'type' => 'email'));
        $fields[] = new field(array('name' => 'password', 'label' => lang('LANG_PASSWORD'), 'validator' => array('isPassword'), 'noValue' => true, 'type' => 'password'));
        $fields[] = new field(array('name' => 'level', 'label' => lang('LANG_PERMISSION'), 'validator' => array('isRequired', 'isString'), 'type' => 'select', 'values' => array_merge(array('' => lang('LANG_CHOOSE')), $GSDConfig->permissions)));
        $fields[] = new field(array('name' => 'locale', 'label' => lang('LANG_LANGUAGE'), 'validator' => array('isString'), 'type' => 'select', 'values' => array_merge(array('' => lang('LANG_CHOOSE')), $GSDConfig->languages)));
        if (!$update) {
            $fields[] = new field(array('name' => 'creator', 'validator' => array('isNumber'), 'notRender' => true));
        } else {
            $fields[] = new field(array('name' => 'disabled', 'label' => lang('LANG_DISABLED'), 'validator' => array('isCheckbox'), 'type' => 'checkbox'));
        }
        
        if (!$update || ($update && @$_REQUEST['password'] === '')) {
            unset($fields[2]);
        }
        
        return array_merge(parent::fields($update), $fields);
    }
}
