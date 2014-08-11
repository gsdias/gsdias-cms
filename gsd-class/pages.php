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
        global $mysql, $tpl;
    
        $sectionextrafields = function_exists('pagesfields') ? pagesfields() : array();

        $mysql->statement('SELECT pages.*, pages.created FROM pages LEFT JOIN users AS u ON pages.creator = u.uid WHERE pages.pid = ?;', array($id));

        if ($mysql->total) {

            $item = $mysql->singleline();
            $created = explode(' ', $item['created']);

            $fields = array();
            foreach ($item as $field => $value) {
                if (is_numeric($field)) {
                    continue;
                }
                $fields['CURRENT_PAGE_'. strtoupper($field)] = $value;
            }

            $fields['CURRENT_PAGE_CREATED'] = timeago(dateDif($created[0], date('Y-m-d',time())));

            $fields['MENU_CHECKED'] = @$item['show_menu'] ? 'checked="checked"' : '';
            $fields['AUTH_CHECKED'] = @$item['require_auth'] ? 'checked="checked"' : '';
            
            $mysql->statement('SELECT * FROM images WHERE iid = ?;', array($item['og_image']));
            $image = $mysql->singleline();
            $fields['CURRENT_PAGE_OG_IMAGE'] = new image(array('path' => sprintf('/gsd-assets/images/%s/%s.%s', @$image['iid'], @$image['iid'], @$image['extension']), 'height' => '100', 'width' => 'auto', 'class' => 'preview'));

            $tpl->setvars($fields);

            if (sizeof($sectionextrafields)) {
                $extrafields = array();

                foreach ($sectionextrafields['list'] as $key => $extrafield) {

                    if (sizeof(@$sectionextrafields['values'])) {
                        $field = new select(array('id' => $extrafield, 'name' => $extrafield, 'list' => $sectionextrafields['values'], 'label' => $sectionextrafields['labels'][$key], 'selected' => @$item[$extrafield]));
                    } else {
                        $field = new input(array('id' => $extrafield, 'name' => $extrafield, 'value' => @$item[$extrafield], 'label' => $sectionextrafields['labels'][$key]));
                    }
                    $extrafields[] = array('FIELD' => $field);
                }

                $tpl->setarray('FIELD', $extrafields); 
                $tpl->setcondition('EXTRAFIELDS'); 
            }
        }
    }
    
    public function generatefields ($id = 0) {}
}
