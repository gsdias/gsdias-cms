<?php

class pages implements isection {
    
    public function __construct ($id = null) {
        
        return 0; 
    }
    
    public function getlist ($numberPerPage = 10) {
        global $mysql, $tpl;
        
        $mysql->statement('SELECT pages.*, pages.creator AS creator_id, u.name AS creator_name 
        FROM pages 
        LEFT JOIN users AS u ON pages.creator = u.uid 
        WHERE pages.disabled IS NULL ORDER BY pages.pid ' . pageLimit(pageNumber(), $numberPerPage));

        $list = array();

        $tpl->setcondition('PAGES_EXIST', $mysql->total > 0);
        
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
            $tpl->setarray('PAGES', $list);
            $pages = pageGenerator('FROM pages LEFT JOIN users AS u ON pages.creator = u.uid WHERE pages.disabled IS NULL ORDER BY pages.pid;');
            
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
