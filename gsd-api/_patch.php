<?php

#### PATCH

$PATCHaa = function ($fields, $extra, $doc = false) {
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

    if (isset($fields['json'])) {

        foreach($fields['json'] as $key => $value) {
            $output = $value;
            $mysql->statement('UPDATE activity_agreement SET ' . $key . ' = :value WHERE aaid = :aaid', array(':value' => $value, ':aaid' => $extra[0]));
        }
    }

    return $output;

};
