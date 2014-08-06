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
        WHERE users.disabled IS NULL ORDER BY users.uid ' . pageLimit(pageNumber(), $numberPerPage));

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
                $fields['LAST_LOGIN'] = sizeof($last_login) ? ($last_login[0] ? timeago(dateDif($last_login[0], date('Y-m-d',time()))) : 'Never') : '';
                $list[] = $fields;
            }
            $tpl->setarray('USERS', $list);
            $pages = pageGenerator('FROM users LEFT JOIN users AS u ON users.creator = u.uid WHERE users.disabled IS NULL ORDER BY users.uid;');
            
            $first_page = new anchor(array('text' => '&lt;&lt;', 'href' => '?page=1'));
            $prev_page = new anchor(array('text' => '&lt;', 'href' => '?page=' . $pages['PREV']));
            $next_page = new anchor(array('text' => '&gt;', 'href' => '?page=' . $pages['NEXT']));
            $last_page = new anchor(array('text' => '&gt;&gt;', 'href' => '?page=' . $pages['LAST']));
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
    }
}
