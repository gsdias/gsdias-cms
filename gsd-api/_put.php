<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.2
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

#### PUT

class apiPut {

    function pageorder ($fields, $extra, $doc = false) {
        global $mysql, $api;

        $output = array('error' => 0, 'message' => 'Salva lista');
        $requiredFields = array('list');
        $returnFields = array();

        if ($doc) {
            return outputDoc('images', array('iid' => 'Identification of the image'), $returnFields);
        }

        if (!$api->requiredFields($fields, $requiredFields)) {
            return array('error' => -3, 'message' => 'Missing required fields' );
        }

        foreach (json_decode($fields['list']) as $item) {
            $mysql->reset()
                ->update('pages')
                ->fields('index')
                ->where('pid = ?')
                ->values(array($item->i, $item->pid))
                ->exec();
        }

        return $output;
    }
}
