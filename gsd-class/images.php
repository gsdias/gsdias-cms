<?php

class images extends section implements isection {
    
    public function __construct ($id = null) {
        
        return 0; 
    }
    
    public function getlist ($numberPerPage = 10) {
        global $mysql, $tpl;
        
        $tags = @$_REQUEST['search'] ? sprintf('WHERE tags = "%%%s%%"', $_REQUEST['search']) : '';

        $tpl->setvar('SEARCH_VALUE', @$_REQUEST['search']);

        $mysql->statement('SELECT images.*, images.creator AS creator_id, u.name AS creator_name 
        FROM images 
        LEFT JOIN users AS u ON images.creator = u.uid '
        . $tags .
        'ORDER BY images.iid ' . pageLimit(pageNumber(), $numberPerPage));

        $list = array();

        $tpl->setcondition('IMAGES_EXIST', $mysql->total > 0);
        
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
                
                $fields['ASSET'] = @$item['width'] ? new image(array('src' => sprintf('/gsd-assets/images/%s.%s', $item['iid'], $item['extension']), 'max-height' => '100', 'height' => 'auto', 'width' => 'auto')) : '';
                $fields['SIZE'] = sprintf('<strong>%s x %s</strong><br>%s', $item['width'], $item['height'], $item['size']);
                $list[] = $fields;
            }
            $tpl->setarray('IMAGES', $list);
            $pages = pageGenerator('FROM images LEFT JOIN users AS u ON images.creator = u.uid ORDER BY images.iid;');
            
            $tpl->setcondition('PAGINATOR', $pages['TOTAL'] > 1);
            
            $this->generatepaginator($pages);
        }
    }
    
    public function getcurrent ($id = 0) {
        global $mysql, $tpl;

        $mysql->statement('SELECT images.*, images.created FROM images LEFT JOIN users AS u ON images.creator = u.uid WHERE images.iid = ?;', array($id));

        if ($mysql->total) {

            $item = $mysql->singleline();
            $created = explode(' ', $item['created']);

            $fields = array();
            foreach ($item as $field => $value) {
                if (is_numeric($field)) {
                    continue;
                }
                $fields['CURRENT_IMAGE_'. strtoupper($field)] = $value;
            }

            $fields['CURRENT_IMAGE_CREATED'] = timeago(dateDif($created[0], date('Y-m-d',time())));

            $tpl->setvars($fields);

        }
    }
}
