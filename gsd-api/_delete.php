<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.2
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

#### DELETE

class apiDelete {

    function layouts ($fields, $extra, $doc = false) {
        global $mysql, $api;
        $output = array('error' => 0, 'message' => lang('LANG_ERROR'));
        $requiredFields = array('list');
        $returnFields = array();

        if ($doc) {
            return $api->extended->outputDoc('images', array('iid' => 'Identification of the image'), $returnFields);
        }

        if (!$api->requiredFields($fields, $requiredFields)) {
            return array('error' => -3, 'message' => 'Missing required fields' );
        }

        $list = explode(',', $fields['list']);
        $deleted = array();

        foreach($list as $id) {
            $mysql->statement('DELETE FROM layouts WHERE lid = ?', array($id));
            if ($mysql->total) {
                $deleted[] = $id;
            }
        }

        return $deleted;
    }

    function pages ($fields, $extra, $doc = false) {
        global $mysql, $api;
        $output = array('error' => 0, 'message' => lang('LANG_ERROR'));
        $requiredFields = array('list');
        $returnFields = array();

        if ($doc) {
            return $api->extended->outputDoc('images', array('iid' => 'Identification of the image'), $returnFields);
        }

        if (!$api->requiredFields($fields, $requiredFields)) {
            return array('error' => -3, 'message' => 'Missing required fields' );
        }

        $list = explode(',', $fields['list']);
        $deleted = array();

        foreach($list as $id) {
            $mysql->statement('DELETE FROM pages WHERE pid = ?', array($id));
            if ($mysql->total) {
                $deleted[] = $id;
            }
        }

        return $deleted;
    }

    function users ($fields, $extra, $doc = false) {
        global $mysql, $api;
        $output = array('error' => 0, 'message' => lang('LANG_ERROR'));
        $requiredFields = array('list');
        $returnFields = array();

        if ($doc) {
            return $api->extended->outputDoc('images', array('iid' => 'Identification of the image'), $returnFields);
        }

        if (!$api->requiredFields($fields, $requiredFields)) {
            return array('error' => -3, 'message' => 'Missing required fields' );
        }

        $list = explode(',', $fields['list']);
        $deleted = array();

        foreach($list as $id) {
            $mysql->statement('DELETE FROM users WHERE uid = ?', array($id));
            if ($mysql->total) {
                $deleted[] = $id;
            }
        }

        return $deleted;
    }

    function images ($fields, $extra, $doc = false) {
        global $mysql, $api;
        $output = array('error' => 0, 'message' => lang('LANG_ERROR'));
        $requiredFields = array('list');
        $returnFields = array();

        if ($doc) {
            return $api->extended->outputDoc('images', array('iid' => 'Identification of the image'), $returnFields);
        }

        if (!$api->requiredFields($fields, $requiredFields)) {
            return array('error' => -3, 'message' => 'Missing required fields' );
        }

        $list = explode(',', $fields['list']);
        $deleted = array();

        foreach($list as $id) {
            $mysql->statement('DELETE FROM images WHERE iid = ?', array($id));
            if ($mysql->total) {
                $deleted[] = $id;
            }
        }

        return $deleted;
    }

    function documents ($fields, $extra, $doc = false) {
        global $mysql, $api;
        $output = array('error' => 0, 'message' => lang('LANG_ERROR'));
        $requiredFields = array('list');
        $returnFields = array();

        if ($doc) {
            return $api->extended->outputDoc('images', array('iid' => 'Identification of the image'), $returnFields);
        }

        if (!$api->requiredFields($fields, $requiredFields)) {
            return array('error' => -3, 'message' => 'Missing required fields' );
        }

        $list = explode(',', $fields['list']);
        $deleted = array();

        foreach($list as $id) {
            $mysql->statement('DELETE FROM documents WHERE did = ?', array($id));
            if ($mysql->total) {
                $deleted[] = $id;
            }
        }

        return $deleted;
    }
}
