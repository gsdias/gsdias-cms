<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.2
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

class layouts extends section implements isection {

    public function __construct ($id = null) {

        return 0;
    }

    public function getlist ($options) {
        global $mysql, $tpl;

        $numberPerPage = $options['numberPerPage'];
        $fields = empty($options['fields']) ? array() : $options['fields'];

        $mysql->statement('SELECT layouts.*, u.name AS creator_name, u.uid AS creator_id
        FROM layouts
        LEFT JOIN users AS u ON layouts.creator = u.uid
        ORDER BY lid ' . pageLimit(pageNumber(), $numberPerPage));

        $result = parent::getlist(array(
            'results' => $mysql->result(),
            'sql' => 'FROM layouts ORDER BY lid;',
            'numberPerPage' => $options['numberPerPage'],
            'fields' => array_merge(array('lid', 'name', 'creator', 'creator_name', 'creator_id'), $fields)
        ));

        return $result;
    }

    public function getcurrent ($id = 0) {
        global $mysql, $tpl;

        $mysql->statement('SELECT *
        FROM layouts
        WHERE lid = ?;', array($id));

        $result = parent::getcurrent($mysql->singleline());

        return $result['item'];
    }
}
