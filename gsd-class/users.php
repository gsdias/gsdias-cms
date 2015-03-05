<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.0
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

class users extends section implements isection {

    public function __construct ($id = null) {

        return 0; 
    }

    public function add ($defaultfields, $defaultsafter = array(), $defaultvalues = array(), $emailparams = array()) {
        global $site;

        $password = $_REQUEST['password'];
        $_REQUEST['password'] = md5($_REQUEST['password']);
        $result = parent::add($defaultfields, $defaultsafter, $defaultvalues);

        $email = new email();

        $email->setto($_REQUEST['email']);
        $email->setfrom($site->email);
        $email->setreplyto($site->email);
        $email->setsubject(dcgettext('client', 'LANG_REGISTER_SUBJECT', LC_MESSAGES) != 'LANG_REGISTER_SUBJECT' ? dcgettext('client', 'LANG_REGISTER_SUBJECT', LC_MESSAGES) : _('LANG_REGISTER_SUBJECT'));
        $email->setvar('password', $password);
        if (sizeof($emailparams)) {
            foreach ($emailparams as $key => $value) {
                $email->setvar(strtolower($key), $value);
            }
        }

        $template = is_file(CLIENTTPLPATH . '_emails/register' . TPLEXT) ? CLIENTTPLPATH . '_emails/register' . TPLEXT : TPLPATH . '_emails/register' . TPLEXT;

        $email->settemplate($template);
        $email->sendmail();

        return $result;
    }

    public function getlist ($numberPerPage = 10, $extrafields = array()) {
        global $mysql, $tpl;

        $result = false;
        $fields = 'users.uid, users.name, users.last_login, users.created, users.disabled, users.creator AS creator_id, u.name AS creator_name';

        if (!empty($extrafields)) {
            foreach ($extrafields as $field) {
                $fields .= sprintf(", %s", $field);
            }
        }

        $mysql->statement('SELECT ' . $fields . '
        FROM users 
        LEFT JOIN users AS u ON users.creator = u.uid 
        ORDER BY users.uid ' . pageLimit(pageNumber(), $numberPerPage));

        $list = array();

        $tpl->setcondition('USERS_EXIST', $mysql->total > 0);

        if ($mysql->total) {

            foreach ($mysql->result() as $item) {
                $fields = array();
                foreach ($item as $field => $value) {
                    if (is_numeric($field)) {
                        continue;
                    }
                    $fields[strtoupper($field)] = $value;
                }
                $created = explode(' ', $item['created']);
                $last_login = explode(' ', @$item['last_login']);
                $fields['CREATED'] = timeago(dateDif($created[0], date('Y-m-d',time())), $created[1]);
                $fields['LAST_LOGIN'] = sizeof($last_login) ? ($last_login[0] ? timeago(dateDif($last_login[0], date('Y-m-d',time())), $created[1]) : '{LANG_NEVER}') : '';
                $fields['DISABLED'] = $item['disabled'] ? '<br>({LANG_DISABLED})' : '';
                $list[] = $fields;
            }
            if (!sizeof($extrafields)) {
                $tpl->setarray('USERS', $list);
            }
            $pages = pageGenerator('FROM users LEFT JOIN users AS u ON users.creator = u.uid ORDER BY users.uid;');

            $tpl->setcondition('PAGINATOR', $pages['TOTAL'] > 1);

            $this->generatepaginator($pages);
        }

        return $list;
    }

    public function getcurrent ($id = 0) {
        global $tpl, $mysql;

        $mysql->statement('SELECT users.*, users.created, users.creator AS creator_id, u.name AS creator_name FROM users LEFT JOIN users AS u ON users.creator = u.uid WHERE users.uid = ?;', array($id));

        if ($mysql->total) {

            $item = $mysql->singleline();

            $this->item = $item;
            $created = explode(' ', $item['created']);

            $fields = array();
            foreach ($item as $field => $value) {
                if (is_numeric($field)) {
                    continue;
                }
                $fields['CURRENT_USER_'. strtoupper($field)] = $value;
            }

            $fields['CURRENT_USER_DISABLED'] = $item['disabled'] ? 'checked="checked"': '';
            $fields['CURRENT_USER_STATUS'] = !$item['disabled'] ? '{LANG_ENABLED}': '{LANG_DISABLED}';

            $fields['PERMISSION'] = new select(array(
                'list' => array(
                    'admin' => 'admin',
                    'editor' => 'editor',
                    'user' => 'user'
                ),
                'label' => '{LANG_PERMISSION}',
                'selected' => $item['level'],
                'name' => 'level'
            ));

            $types = new select( array ( 'list' => array('pt_PT' => 'Portugues', 'en_GB' => 'Ingles'), 'id' => 'LANGUAGE', 'selected' => $item['locale'] ) );
            $types->object();

            $tpl->setvars($fields);

        }
    }
}
