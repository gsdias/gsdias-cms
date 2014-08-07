<?php

class images implements isection {
    
    public function __construct ($id = null) {
        
        return 0; 
    }
    
    public function getlist ($numberPerPage = 10) {
        global $mysql, $tpl;
        
        $mysql->statement('SELECT images.*, images.creator AS creator_id, u.name AS creator_name 
        FROM images 
        LEFT JOIN users AS u ON images.creator = u.uid 
        ORDER BY images.iid ' . pageLimit(pageNumber(), $numberPerPage));

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
                
                $fields['ASSET'] = @$item['width'] ? new image(array('path' => sprintf('/gsd-assets/images/%s/%s.%s', $item['iid'], $item['iid'], $item['extension']), 'height' => '100', 'width' => 'auto')) : '';
                $fields['SIZE'] = sprintf('<strong>%s x %s</strong><br>%s', $item['width'], $item['height'], $item['size']);
                $list[] = $fields;
            }
            $tpl->setarray('IMAGES', $list);
            $pages = pageGenerator('FROM images LEFT JOIN users AS u ON images.creator = u.uid ORDER BY images.iid;');
            
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
        global $mysql, $tpl;
        
        $sectionextrafields = function_exists('imagesfields') ? imagesfields() : array();

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
}
