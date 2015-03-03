<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 * @version    1.0
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */

#### POST

$POSTapplication = function ($fields, $extra, $doc = false) {
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
    $fields = $fields['json'];

    $output = array('error' => 0, 'message' => 'Candidatura inserida com sucesso');
    $requiredFields = array('uid', 'vid');
    $returnFields = array('aid', 'vid', 'reference', 'datas', 'name', 'sent', 'state', 'contact', 'uid', 'observations', 'obs_user', 'state_changes', 'checkviagens');

    if (!$api->requiredFields($fields, $requiredFields))
        return array('error' => -3, 'message' => 'Missing required fields' );

    $mysql->statement('INSERT INTO applications (uid, vid, created, state) VALUES (:user, :vaga, UNIX_TIMESTAMP(), 1)',
                      array(':user' => $fields['uid'],
                            ':vaga' => $fields['vid'])
                     );
    $cand_id = $mysql->lastInserted();

    $mysql->statement('SELECT *, a.observations, a.aid AS cand, a.state AS state, FROM_UNIXTIME(a.created, "%d-%m-%Y") AS sent, checkviagens(a.aid) as checkviagens, date_format(state_changes, "%d-%m-%Y %H:%i:%s") as state_changes, concat(date_format(v.date_begin, "%d-%m-%Y"), " a ", date_format(v.date_end, "%d-%m-%Y")) as datas, if (mobile <> "", mobile, u.phone) AS contact, l.observations AS obs_user
                             FROM chaves_sve.applications AS a
                             JOIN chaves_proatlantico.users AS u ON u.uid = a.uid
                             JOIN users AS l ON l.uid = a.uid
                             JOIN chaves_sve.vacancies AS v ON a.vid = v.vid
                             WHERE a.aid = :id', array(':id' => $cand_id));

    if ($mysql->total) {
        $output = array();
        foreach ($mysql->result() as $row) {
            $array = array();
            foreach ($row as $visible => $value) {

                if (!is_numeric($visible) && in_array($visible, $returnFields)) {

                    $array[$visible] = $value;

                }
            }

            $array['aid'] = $cand_id;

            $output = $array;

        }
    }
    return $output;
};

$POSTfile = function ($fields, $extra, $doc = false) {
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
    $params = fileparam($_REQUEST['name']);

    $mysql->statement(sprintf('SELECT %s FROM %s WHERE %s = :id AND %s IS NOT NULL AND %s <> ""', $params['field'], $params['table'], $params['fieldid'], $params['field'], $params['field']), array(':id' => $_REQUEST['id']));

    if ($mysql->total) {
        $file = $mysql->singleresult();

        $filepath = sprintf('%s%s', $params['path'], $file);

        if(file_exists($filepath)) {
            unlink($filepath);
        }

    }

    return fileChange($_REQUEST['name'], $_FILES, $_REQUEST['id'], $params['table'], $params['field'], $params['fieldid'], $params['path'], $_REQUEST['notsendmail']);
};

$POSTattachment = function ($fields, $extra, $doc = false) {
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
    $file = savefile ($_FILES['attachment'], '../attachments/');
    $mysql->statement('SELECT attachment
                             FROM emails AS e
                             WHERE e.template = :template', array(':template' => $extra[0]));
    $result = $mysql->singleresult();
    $attachments = json_decode($result, true);
    array_push($attachments, $_FILES['attachment']['name']);

    $attachments = array_unique(array_filter($attachments));

    $mysql->statement('UPDATE emails AS e SET e.attachment = :files
                       WHERE e.template = :template', array(':template' => $extra[0], ':files' => json_encode($attachments, true)));
};
