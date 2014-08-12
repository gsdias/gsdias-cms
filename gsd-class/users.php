<?php

class users implements isection {

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

            $first_page = new anchor(array('text' => '&lt; Primeira', 'href' => '?page=1'));
            $prev_page = new anchor(array('text' => 'Anterior', 'href' => '?page=' . $pages['PREV']));
            $next_page = new anchor(array('text' => 'Seguinte', 'href' => '?page=' . $pages['NEXT']));
            $last_page = new anchor(array('text' => 'Ultima &gt;', 'href' => '?page=' . $pages['LAST']));
            $tpl->setvars(array(
                'FIRST_PAGE' => $first_page,
                'PREV_PAGE' => $prev_page,
                'NEXT_PAGE' => $next_page,
                'LAST_PAGE' => $last_page,
                'CURRENT_PAGE' => $pages['CURRENT'],
                'TOTAL_PAGES' => $pages['TOTAL']
            ));
        }
    }

    public function getcurrent ($id = 0) {
        global $tpl, $mysql;

        $sectionextrafields = function_exists('usersfields') ? usersfields() : array();

        $mysql->statement('SELECT users.*, users.created, users.creator AS creator_id, u.name AS creator_name FROM users LEFT JOIN users AS u ON users.creator = u.uid WHERE users.uid = ?;', array($id));

        if ($mysql->total) {

            $item = $mysql->singleline();
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

            if (sizeof($sectionextrafields)) {
                $extrafields = array();

                foreach ($sectionextrafields['list'] as $key => $extrafield) {
                    $extraclass = '';
                    
                    switch ($sectionextrafields['types'][$key]) {
                        case 'image':
                        $mysql->statement('SELECT * FROM images WHERE iid = ?;', array($item[$extrafield]));
                        $image = $mysql->singleline();
                        
                        $image = new image(array('path' => sprintf('/gsd-assets/images/%s/%s.%s', @$image['iid'], @$image['iid'], @$image['extension']), 'height' => '100', 'width' => 'auto', 'class' => 'preview'));

                        $partial = new tpl();
                        $partial->setvars(array(
                            'LABEL' => $sectionextrafields['labels'][$key],
                            'NAME' => $extrafield,
                            'IMAGE' => $image
                        ));
                        $partial->setfile('_image');

                        $field = $partial;
                        $extraclass = 'image';
                        break;
                        case 'select':
                        $field = new select(array('id' => $extrafield, 'name' => $extrafield, 'list' => $sectionextrafields['values'], 'label' => $sectionextrafields['labels'][$key], 'selected' => @$item[$extrafield]));
                        break;
                        default:
                        $field = (string)new input(array('id' => $extrafield, 'name' => $extrafield, 'value' => @$item[$extrafield], 'label' => $sectionextrafields['labels'][$key]));
                        break;
                    }

                    $extrafields[] = array('FIELD' => $field, 'EXTRACLASS' => $extraclass);
                }

                $tpl->setarray('FIELD', $extrafields); 
                $tpl->setcondition('EXTRAFIELDS'); 
            }
        }
    }

    public function generatefields ($id = 0) {}
}
