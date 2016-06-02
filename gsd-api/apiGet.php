<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

#### GET
namespace GSD\Api;

class apiGet
{
    public function images($fields, $extra, $doc = false)
    {
        global $mysql, $api;

        $api->checkCredentials();

        if (!(IS_ADMIN || IS_EDITOR)) {
            return lang('LANG_NOPERMISSION');
        }

        $output = array('error' => 0, 'message' => lang('LANG_NO_IMAGES'));
        $requiredFields = array();
        $returnFields = array('iid', 'name', 'width', 'height', 'extension');

        if ($doc) {
            return $api->extended->outputDoc('images', array('iid' => 'Identification of the image'), $returnFields);
        }

        if (!$api->requiredFields($fields, $requiredFields)) {
            return array('error' => -3, 'message' => 'Missing required fields');
        }

        $mysql->reset()
            ->select()
            ->from('images')
            ->where('deleted IS NULL');
        if (@$fields['search']) {
            $mysql->where(sprintf('AND tags like "%%%s%%"', $fields['search']));
        }

        $mysql->exec();

        if ($mysql->total) {
            $output['message'] = '';
            $output['data']['list'] = array();
            foreach ($mysql->result() as $row) {
                $array = array();
                foreach ($row as $visible => $value) {
                    if (in_array($visible, $returnFields)) {
                        $array[$visible] = $value;
                    }
                }
                array_push($output['data']['list'], $array);
            }

            return $output['data']['list'];
        }

        return $output;
    }

    public function pages($fields, $extra, $doc = false)
    {
        global $mysql, $api;

        $api->checkCredentials();

        if (!(IS_ADMIN || IS_EDITOR)) {
            return lang('LANG_NOPERMISSION');
        }

        $output = array('error' => 0, 'message' => lang('LANG_NO_IMAGES'));
        $requiredFields = array('page', 'type');
        $returnFields = array();
        $numberPerPage = 10;

        if ($doc) {
            return $api->extended->outputDoc('pages', array('pages' => 'Page number', 'type' => 'Type list'), $returnFields);
        }

        if (!$api->requiredFields($fields, $requiredFields)) {
            return array('error' => -3, 'message' => 'Missing required fields');
        }

        $functionname = 'paginator'.ucwords($fields['type']);

        $output = $api->extended->{$functionname}(array('page' => $fields['page'], 'numberPerPage' => $numberPerPage, 'output' => $output));

        return $output;
    }
}
