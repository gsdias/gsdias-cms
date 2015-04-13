<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.1
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

#### PUT

$PUTapplication = function ($fields, $extra, $doc = false) {
    global $mysql, $api;

    if ($doc) {
        $output['data'] = array(
            'input' => array(
                'districtId' => 'Identification of the district',
                'countyId' => 'Identification of the county'
            ),
            'output' => array(
                'list' => 'List of the parishes'
            )

        );
        return $output;
    }

    switch ($extra[0]) {
        case 'state':
        return $changeApplicationState($fields);
        break;
        case 'observations':
        return changeApplicationObservations($fields);
        break;
    }

};
