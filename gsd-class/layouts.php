<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.2
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

namespace GSD;

class layouts extends section implements isection {

    public function __construct ($id = null) {

        return 0;
    }

    public function getlist ($options) {
        global $mysql, $tpl;

        $paginator = new paginator('FROM layouts ORDER BY lid;', @$options['numberPerPage'], @$_REQUEST['page']);
        $fields = empty($options['fields']) ? array() : $options['fields'];

        $mysql->statement('SELECT layouts.*, u.name AS creator_name, u.uid AS creator_id
        FROM layouts
        LEFT JOIN users AS u ON layouts.creator = u.uid
        ORDER BY lid ' . $paginator->pageLimit());

        $result = parent::getlist(array(
            'results' => $mysql->result(),
            'fields' => array_merge(array('lid', 'name', 'creator', 'creator_name', 'creator_id'), $fields),
            'paginator' => $paginator
        ));

        return $result;
    }

    public function getcurrent ($id = 0) {
        global $mysql, $tpl;

        $mysql->reset()
            ->select()
            ->from('layouts')
            ->where('lid = ?')
            ->values($id)
            ->exec();

        $result = parent::getcurrent($mysql->singleline());

        return $result['item'];
    }
}
