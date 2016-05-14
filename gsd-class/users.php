<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
namespace GSD;

class users extends section implements isection
{
    public function __construct($permission = NULL)
    {
        global $tpl, $site;

        $permission = gettype($permission) === 'boolean' ? $permission : IS_ADMIN;
        $result = parent::__construct($permission);

        $tpl->setvar('SECTION_TYPE', lang('LANG_USER', 'LOWER'));
        if ($site->arg(2) === 'create') {
            $tpl->repvar('SECTION_ACTION', lang('LANG_NEW_MALE'));
        }

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
            ->on('users.creator = u.uid');

        if ($options['search']) {
            $mysql->where(sprintf('users.name like "%%%s%%"', $options['search']));
        }

        $mysql->order('users.uid');
        $paginator = new paginator($options['numberPerPage'], $options['page']);

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
            'fields' => array_merge(array('uid', 'name', 'creator_name', 'creator_id'), $fields),
            'paginator' => $paginator,
        ));

        if (!empty($result['list'])) {
            foreach ($result['results'] as $index => $item) {
                $last_login = explode(' ', @$item->last_login);

                $result['list'][$index]['LAST_LOGIN'] = sizeof($last_login) ? ($last_login[0] ? timeago(dateDif($last_login[0], date('Y-m-d', time())), $last_login[1]) : lang('LANG_NEVER')) : '';
                $result['list'][$index]['DISABLED'] = $item->disabled ? '<br>({LANG_DISABLED})' : '';
            }

            $tpl->setarray('USERS', $result['list']);
        }

        return $result;
    }

    public function getcurrent($id = 0)
    {
        global $tpl, $mysql, $languages, $permissions;

        $mysql->statement('SELECT users.*, users.created, users.creator AS creator_id, u.name AS creator_name FROM users LEFT JOIN users AS u ON users.creator = u.uid WHERE users.uid = ?;', array($id));

        $result = parent::getcurrent($mysql->singleline());

        if (!empty($this->item)) {
            $item = $this->item;
            $created = explode(' ', $item->created);
            $fields = $result;

            $fields['CURRENT_USERS_DISABLED'] = $item->disabled ? 'checked="checked"' : '';
            $fields['CURRENT_USERS_STATUS'] = !$item->disabled ? lang('LANG_ENABLED') : lang('LANG_DISABLED');
            $fields['PERMISSION'] = new select(array(
                'list' => $permissions,
                'label' => lang('LANG_PERMISSION'),
                'selected' => $item->level,
                'name' => 'level',
            ));

            $types = new select(array('list' => $languages, 'id' => 'LANGUAGE', 'selected' => $item->locale));
            $types->object();

            $tpl->repvars($fields);
        }

        return $result;
    }

    public function edit()
    {
        return parent::edit();
    }

    public function add($emailparams = array())
    {
        global $site, $config;

        $password = substr(str_shuffle(sha1(rand().time().'gsdias-cms')), 2, 10);

        $_REQUEST['password'] = $password;

        $result = parent::add();

        if (empty($result['errmsg'])) {
            $email = new email();

            $email->setto(@$emailparams['email'] ? $emailparams['email'] : $_REQUEST['email']);
            $email->setfrom($site->email);
            $email->setreplyto($site->email);
            $email->setsubject(lang('LANG_REGISTER_SUBJECT'));
            $email->setvar('sitename', $site->name);
            $email->setvar('siteurl', $config['url']);
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
        global $permissions, $languages;
        
        $fields = array();
        
        $fields[] = new field(array('name' => 'name', 'label' => lang('LANG_NAME'), 'validator' => array('isRequired', 'isString')));
        $fields[] = new field(array('name' => 'email', 'label' => lang('LANG_EMAIL'), 'validator' => array('isRequired', 'isEmail'), 'type' => 'email'));
        $fields[] = new field(array('name' => 'password', 'label' => lang('LANG_PASSWORD'), 'validator' => array('isPassword'), 'noValue' => true, 'type' => 'password'));
        $fields[] = new field(array('name' => 'level', 'label' => lang('LANG_PERMISSION'), 'validator' => array('isRequired', 'isString'), 'type' => 'select', 'values' => array_merge(array('' => lang('LANG_CHOOSE')), $permissions)));
        $fields[] = new field(array('name' => 'locale', 'label' => lang('LANG_LANGUAGE'), 'validator' => array('isString'), 'type' => 'select', 'values' => array_merge(array('' => lang('LANG_CHOOSE')), $languages)));
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
