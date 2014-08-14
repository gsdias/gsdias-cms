<?php

class pages extends section implements isection {
    
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
                $fields['CREATED'] = timeago(dateDif($created[0], date('Y-m-d',time())));
                
                $list[] = $fields;
            }
            $tpl->setarray('PAGES', $list);
            $pages = pageGenerator('FROM pages LEFT JOIN users AS u ON pages.creator = u.uid WHERE pages.disabled IS NULL ORDER BY pages.pid;');
            
            $tpl->setcondition('PAGINATOR', $pages['TOTAL'] > 1);
            
            $this->generatepaginator($pages);
        }
    }
    
    public function getcurrent ($id = 0) {
        global $mysql, $tpl;
    
        $mysql->statement('SELECT pages.*, pages.created FROM pages LEFT JOIN users AS u ON pages.creator = u.uid WHERE pages.pid = ?;', array($id));

        if ($mysql->total) {

            $item = $mysql->singleline();

            $this->item = $item;
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

            $image = new image(array('src' => sprintf('/gsd-assets/images/%s.%s', @$image['iid'], @$image['extension']), 'height' => '100', 'width' => 'auto', 'class' => 'preview'));

            $partial = new tpl();
            $partial->setvars(array(
                'LABEL' => 'Imagem',
                'NAME' => 'og_image',
                'VALUE' => $item['og_image'],
                'IMAGE' => $image
            ));
            $partial->setfile('_image');

            $fields['CURRENT_PAGE_OG_IMAGE'] = $partial;

            $tpl->setvars($fields);

        }
    }
}
