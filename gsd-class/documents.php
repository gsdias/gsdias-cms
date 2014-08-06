<?php

class documents implements isection {
    
    public function __construct ($id = null) {
        
        return 0; 
    }
    
    public function getlist ($numberPerPage = 10) {
        global $mysql, $tpl;
        
        $mysql->statement('SELECT documents.*, documents.creator AS creator_id, u.name AS creator_name 
        FROM documents 
        LEFT JOIN users AS u ON documents.creator = u.uid 
        WHERE documents.disabled IS NULL ORDER BY documents.did ' . pageLimit(pageNumber(), $numberPerPage));

        $list = array();

        $tpl->setcondition('DOCUMENTS_EXIST', $mysql->total > 0);
        
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
            $tpl->setarray('DOCUMENTS', $list);
            $pages = pageGenerator('FROM documents LEFT JOIN users AS u ON documents.creator = u.uid WHERE documents.disabled IS NULL ORDER BY documents.did;');
            
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
        global $tpl, $mysql;
    
        $sectionextrafields = function_exists('documentsfields') ? documentsfields() : array();

        $mysql->statement('SELECT documents.*, documents.created FROM documents LEFT JOIN users AS u ON documents.creator = u.uid WHERE documents.did = ?', array($id));

        if ($mysql->total) {

            $item = $mysql->singleline();
            $created = explode(' ', $item['created']);

            $fields = array();
            foreach ($item as $field => $value) {
                if (is_numeric($field)) {
                    continue;
                }
                $fields['CURRENT_DOCUMENT_'. strtoupper($field)] = $value;
            }

            $fields['CURRENT_DOCUMENT_CREATED'] = timeago(dateDif($created[0], date('Y-m-d',time())));

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
