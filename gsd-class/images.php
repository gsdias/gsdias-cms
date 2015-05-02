<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.2
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

class images extends section implements isection {
    
    public function __construct ($id = null) {
        
        return 0; 
    }
    
    public function getlist ($options) {
        global $mysql, $tpl;

        $numberPerPage = $options['numberPerPage'];
        $fields = empty($options['fields']) ? array() : $options['fields'];
        
        $tags = @$_REQUEST['search'] ? sprintf(' WHERE tags like "%%%s%%" ', $_REQUEST['search']) : '';

        $tpl->setvar('SEARCH_VALUE', @$_REQUEST['search']);

        $mysql->statement('SELECT images.*, images.creator AS creator_id, u.name AS creator_name 
        FROM images 
        LEFT JOIN users AS u ON images.creator = u.uid '
        . $tags .
        'ORDER BY images.iid ' . pageLimit(pageNumber(), $numberPerPage));

        $result = parent::getlist(array(
            'results' => $mysql->result(),
            'sql' => 'FROM images ' . $tags . 'ORDER BY images.iid;',
            'numberPerPage' => $options['numberPerPage'],
            'fields' => array_merge(array('iid', 'name', 'description', 'creator', 'creator_name', 'creator_id'), $fields)
        ));

        if (!empty($result['list'])) {
            foreach ($result['results'] as $index => $item) {
                $result['list'][$index]['ASSET'] = @$item->width ? new image(array('iid' => $item->iid, 'max-height' => '100', 'height' => 'auto', 'width' => 'auto')) : '';
                $result['list'][$index]['SIZE'] = sprintf('<strong>%s x %s</strong><br>%s', $item->width, $item->height, $item->size);
            }
            
            $tpl->setarray('IMAGES', $result['list']);
        }

        return $result;
    }
    
    public function getcurrent ($id = 0) {
        global $mysql, $tpl;

        $mysql->statement('SELECT images.*, images.created
        FROM images
        LEFT JOIN users AS u ON images.creator = u.uid
        WHERE images.iid = ?;', array($id));

        $result = parent::getcurrent($mysql->singleline());

        if (!empty($result['item'])) {

            $fields = $result['fields'];

            $tpl->setvars($fields);

        }
    }
}
