<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.2
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

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
                    $fields[strtoupper($field)] = $value;
                }
                $created = explode(' ', $item->created);
                $fields['CREATED'] = timeago(dateDif($created[0], date('Y-m-d',time())), $created[1]);

                $fields['ASSET'] = $item->name;
                $fields['SIZE'] = sprintf('%s', $item->size);
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

        $mysql->statement('SELECT documents.*, documents.created
        FROM documents
        LEFT JOIN users AS u ON documents.creator = u.uid
        WHERE documents.did = ?;', array($id));

        $result = parent::getcurrent($mysql->singleline());

        if (!empty($result['item'])) {

            $fields = $result['fields'];

            $tpl->setvars($fields);

        }
    }
}
