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
    
    public function getlist ($options) {
        global $mysql, $tpl;
        
        $numberPerPage = $options['numberPerPage'];
        $fields = empty($options['fields']) ? array() : $options['fields'];

        $mysql->statement('SELECT documents.*, documents.creator AS creator_id, u.name AS creator_name 
        FROM documents 
        LEFT JOIN users AS u ON documents.creator = u.uid 
        ORDER BY documents.did ' . pageLimit(pageNumber(), $numberPerPage));

        $result = parent::getlist(array(
            'results' => $mysql->result(),
            'sql' => 'FROM documents ORDER BY documents.did;',
            'numberPerPage' => $options['numberPerPage'],
            'fields' => array_merge(array('did', 'name', 'description', 'creator', 'creator_name', 'creator_id'), $fields)
        ));

        if (!empty($result['list'])) {
            foreach ($result['results'] as $index => $item) {
                $result['list'][$index]['ASSET'] = $item->name;
                $result['list'][$index]['SIZE'] = sprintf('%s', $item->size);
            }
            $tpl->setarray('DOCUMENTS', $result['list']);
        }

        return $result;
    }
    
    public function getcurrent ($id = 0) {
        global $mysql, $tpl;

        $mysql->statement('SELECT documents.*, documents.created
        FROM documents
        LEFT JOIN users AS u ON documents.creator = u.uid
        WHERE documents.did = ?;', array($id));

        $result = parent::getcurrent($mysql->singleline());

        return $result['item'];
    }
}
