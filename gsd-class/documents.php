<?php

class documents extends section implements isection {
    
    public function __construct ($id = null) {
        
        return 0; 
    }
    
    public function getlist ($numberPerPage = 10) {
        global $mysql, $tpl;
        
        $mysql->statement('SELECT documents.*, documents.creator AS creator_id, u.name AS creator_name 
        FROM documents 
        LEFT JOIN users AS u ON documents.creator = u.uid 
        ORDER BY documents.did ' . pageLimit(pageNumber(), $numberPerPage));

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
                $fields['CREATED'] = timeago(dateDif($created[0], date('Y-m-d',time())));

                $fields['ASSET'] = $item['name'];
                $fields['SIZE'] = sprintf('%s', $item['size']);
                $list[] = $fields;
            }
            $tpl->setarray('DOCUMENTS', $list);
            $pages = pageGenerator('FROM documents LEFT JOIN users AS u ON documents.creator = u.uid ORDER BY documents.did;');
            
            $tpl->setcondition('PAGINATOR', $pages['TOTAL'] > 1);
            
            $this->generatepaginator($pages);
        }
    }
    
    public function getcurrent ($id = 0) {
        global $mysql, $tpl;

        $mysql->statement('SELECT documents.*, documents.created FROM documents LEFT JOIN users AS u ON documents.creator = u.uid WHERE documents.did = ?;', array($id));

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
