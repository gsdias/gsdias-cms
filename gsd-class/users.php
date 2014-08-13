<?php

class users extends section implements isection {

    public function __construct ($id = null) {

        return 0; 
    }

    public function getlist ($numberPerPage = 10) {
        global $mysql, $tpl;

        $mysql->statement('SELECT users.*, users.creator AS creator_id, u.name AS creator_name 
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
                $fields['CREATED'] = timeago(dateDif($created[0], date('Y-m-d',time())));
                $fields['LAST_LOGIN'] = sizeof($last_login) ? ($last_login[0] ? timeago(dateDif($last_login[0], date('Y-m-d',time()))) : 'Nunca') : '';
                $fields['DISABLED'] = $item['disabled'] ? '<br>(Desativo)' : '';
                $list[] = $fields;
            }
            $tpl->setarray('USERS', $list);
            $pages = pageGenerator('FROM users LEFT JOIN users AS u ON users.creator = u.uid ORDER BY users.uid;');

            $tpl->setcondition('PAGINATOR', $pages['TOTAL'] > 1);

            $this->generatepaginator($pages);
        }
    }

    public function getcurrent ($id = 0) {
        global $tpl, $mysql;

        $mysql->statement('SELECT users.*, users.created, users.creator AS creator_id, u.name AS creator_name FROM users LEFT JOIN users AS u ON users.creator = u.uid WHERE users.uid = ?;', array($id));

        if ($mysql->total) {

            $item = $mysql->singleline();

            parent::$item = $item;
            $created = explode(' ', $item['created']);

            $fields = array();
            foreach ($item as $field => $value) {
                if (is_numeric($field)) {
                    continue;
                }
                $fields['CURRENT_USER_'. strtoupper($field)] = $value;
            }

            $fields['CURRENT_USER_DISABLED'] = $item['disabled'] ? 'checked="checked"': '';
            $fields['CURRENT_USER_STATUS'] = !$item['disabled'] ? 'Ativo': 'Desativo';

            $fields['PERMISSION'] = new select(array(
                'list' => array(
                    'admin' => 'admin',
                    'editor' => 'editor',
                    'user' => 'user'
                ),
                'label' => 'Permissão',
                'selected' => $item['level'],
                'name' => 'level'
            ));

            $tpl->setvars($fields);

        }
    }
}
