<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.4
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

#### DELETE
namespace GSD\Api;

class apiDelete
{
    public function layouts($fields, $extra, $doc = false)
    {
        global $mysql, $api;

        if ($api->user->level == 'user') {
            return lang('LANG_NOPERMISSION');
        }

        $output = array('error' => 0, 'message' => lang('LANG_ERROR'));
        $requiredFields = array('list', 'type');
        $returnFields = array();

        if ($doc) {
            return $api->extended->outputDoc('images', array('iid' => 'Identification of the image'), $returnFields);
        }

        if (!$api->requiredFields($fields, $requiredFields)) {
            return array('error' => -3, 'message' => 'Missing required fields');
        }

        return $api->extended->removeElements($fields['type'], $fields['list']);
    }

    public function pages($fields, $extra, $doc = false)
    {
        global $mysql, $api;

        if ($api->user->level == 'user') {
            return lang('LANG_NOPERMISSION');
        }

        $output = array('error' => 0, 'message' => lang('LANG_ERROR'));
        $requiredFields = array('list');
        $returnFields = array();

        if ($doc) {
            return $api->extended->outputDoc('images', array('iid' => 'Identification of the image'), $returnFields);
        }

        if (!$api->requiredFields($fields, $requiredFields)) {
            return array('error' => -3, 'message' => 'Missing required fields');
        }

        return $api->extended->removeElements($fields['type'], $fields['list']);
    }

    public function users($fields, $extra, $doc = false)
    {
        global $mysql, $api;

        if ($api->user->level != 'admin') {
            return lang('LANG_NOPERMISSION');
        }

        $output = array('error' => 0, 'message' => lang('LANG_ERROR'));
        $requiredFields = array('list');
        $returnFields = array();

        if ($doc) {
            return $api->extended->outputDoc('images', array('iid' => 'Identification of the image'), $returnFields);
        }

        if (!$api->requiredFields($fields, $requiredFields)) {
            return array('error' => -3, 'message' => 'Missing required fields');
        }

        return $api->extended->removeElements($fields['type'], $fields['list']);
    }

    public function images($fields, $extra, $doc = false)
    {
        global $mysql, $api;

        if ($api->user->level == 'user') {
            return lang('LANG_NOPERMISSION');
        }

        $output = array('error' => 0, 'message' => lang('LANG_ERROR'));
        $requiredFields = array('list');
        $returnFields = array();

        if ($doc) {
            return $api->extended->outputDoc('images', array('iid' => 'Identification of the image'), $returnFields);
        }

        if (!$api->requiredFields($fields, $requiredFields)) {
            return array('error' => -3, 'message' => 'Missing required fields');
        }

        return $api->extended->removeElements($fields['type'], $fields['list']);
    }

    public function documents($fields, $extra, $doc = false)
    {
        global $mysql, $api;

        if ($api->user->level == 'user') {
            return lang('LANG_NOPERMISSION');
        }

        $output = array('error' => 0, 'message' => lang('LANG_ERROR'));
        $requiredFields = array('list');
        $returnFields = array();

        if ($doc) {
            return $api->extended->outputDoc('images', array('iid' => 'Identification of the image'), $returnFields);
        }

        if (!$api->requiredFields($fields, $requiredFields)) {
            return array('error' => -3, 'message' => 'Missing required fields');
        }

        return $api->extended->removeElements($fields['type'], $fields['list']);
    }
}
