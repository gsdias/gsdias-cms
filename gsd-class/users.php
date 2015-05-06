<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.2
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

class users extends section implements isection {

    public function __construct ($id = null) {

        return 0; 
    }

    public function getlist ($options) {
        global $mysql, $tpl;

        $search = @$_REQUEST['search'] ? sprintf(' WHERE users.name like "%%%s%%" ', $_REQUEST['search']) : '';
        $fromsql = sprintf(' FROM users
        LEFT JOIN users AS u ON users.creator = u.uid %s
        ORDER BY users.uid ', $search);
        $paginator = new paginator($fromsql, @$options['numberPerPage'], @$_REQUEST['page']);
        $fields = empty($options['fields']) ? array() : $options['fields'];

        $_fields = 'users.*, users.creator AS creator_id, u.name AS creator_name';

        if (!empty($fields)) {
            foreach ($fields as $field) {
                $_fields .= sprintf(", %s", $field);
            }
        }

        $mysql->statement('SELECT ' . $_fields . $fromsql . $paginator->pageLimit());

        $result = parent::getlist(array(
            'results' => $mysql->result(),
            'fields' => array_merge(array('uid', 'name', 'creator_name', 'creator_id'), $fields),
            'paginator' => $paginator
        ));

        if (!empty($result['list'])) {
            foreach ($result['results'] as $index => $item) {
                $last_login = explode(' ', @$item->last_login);

                $result['list'][$index]['LAST_LOGIN'] = sizeof($last_login) ? ($last_login[0] ? timeago(dateDif($last_login[0], date('Y-m-d',time())), $last_login[1]) : lang('LANG_NEVER')) : '';
                $result['list'][$index]['DISABLED'] = $item->disabled ? '<br>({LANG_DISABLED})' : '';
            }

            $tpl->setarray('USERS', $result['list']);
        }

        return $result;
    }

    public function getcurrent ($id = 0) {
        global $tpl, $mysql, $languages;

        $mysql->statement('SELECT users.*, users.created, users.creator AS creator_id, u.name AS creator_name FROM users LEFT JOIN users AS u ON users.creator = u.uid WHERE users.uid = ?;', array($id));

        $result = parent::getcurrent($mysql->singleline());

        if (!empty($result['item'])) {

            $item = $result['item'];
            $created = explode(' ', $item->created);
            $fields = $result['fields'];

            $fields['CURRENT_USERS_DISABLED'] = $item->disabled ? 'checked="checked"': '';
            $fields['CURRENT_USERS_STATUS'] = !$item->disabled ? lang('LANG_ENABLED'): lang('LANG_DISABLED');

            $fields['PERMISSION'] = new select(array(
                'list' => array(
                    'admin' => 'admin',
                    'editor' => 'editor',
                    'user' => 'user'
                ),
                'label' => lang('LANG_PERMISSION'),
                'selected' => $item->level,
                'name' => 'level'
            ));

            $types = new select( array ( 'list' => $languages, 'id' => 'LANGUAGE', 'selected' => $item->locale ) );
            $types->object();

            $tpl->repvars($fields);
        }

        return $result['item'];
    }

    public function add ($defaultfields, $defaultsafter = array(), $defaultvalues = array(), $emailparams = array()) {
        global $site;

        $password = $_REQUEST['password'];
        $_REQUEST['password'] = md5($_REQUEST['password']);
        $result = parent::add($defaultfields, $defaultsafter, $defaultvalues);

        $email = new email();

        $email->setto(@$emailparams['email'] ? $emailparams['email'] : $_REQUEST['email']);
        $email->setfrom($site->email);
        $email->setreplyto($site->email);
        $email->setsubject(lang('LANG_REGISTER_SUBJECT'));
        $email->setvar('password', $password);

        if (sizeof(@$emailparams['fields'])) {
            foreach ($emailparams['fields'] as $key => $value) {
                $email->setvar(strtolower($key), $value);
            }
        }

        $template = is_file(CLIENTTPLPATH . '_emails/register' . TPLEXT) ? CLIENTTPLPATH . '_emails/register' . TPLEXT : TPLPATH . '_emails/register' . TPLEXT;

        $email->settemplate($template);
        $email->sendmail();

        return $result;
    }
}
