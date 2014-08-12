<?php

class section implements isection {

    public static $item = array();

    public function __construct ($id = null) {

        return 0;
    }

    public function getlist ($numberPerPage = 10) {}

    public function getcurrent ($id = 0) {}

    public function generatefields ($section) {
        global $tpl, $mysql;

        $func = $section . 'fields';
        $item = self::$item;

        $sectionextrafields = function_exists($func) ? $func() : array();
        if (sizeof($sectionextrafields)) {
            $extrafields = array();

            foreach ($sectionextrafields['list'] as $key => $extrafield) {

                $extraclass = '';

                switch ($sectionextrafields['types'][$key]) {
                    case 'image':
                    $mysql->statement('SELECT * FROM images WHERE iid = ?;', array(@$item[$extrafield]));
                    $image = $mysql->singleline();

                    $image = new image(array('path' => sprintf('/gsd-assets/images/%s/%s.%s', @$image['iid'], @$image['iid'], @$image['extension']), 'height' => '100', 'width' => 'auto', 'class' => 'preview'));

                    $partial = new tpl();
                    $partial->setvars(array(
                        'LABEL' => $sectionextrafields['labels'][$key],
                        'NAME' => $extrafield,
                        'IMAGE' => $image,
                        'VALUE' => @$item[$extrafield]
                    ));
                    $partial->setfile('_image');

                    $field = $partial;
                    $extraclass = 'image';
                    break;
                    case 'select':
                    $field = new select(array('id' => $extrafield, 'name' => $extrafield, 'list' => $sectionextrafields['values'], 'label' => $sectionextrafields['labels'][$key], 'selected' => @$item[$extrafield]));
                    break;
                    default:
                    $field = (string)new input(array('id' => $extrafield, 'name' => $extrafield, 'value' => @$item[$extrafield], 'label' => $sectionextrafields['labels'][$key]));
                    break;
                }

                $extrafields[] = array('FIELD' => $field, 'EXTRACLASS' => $extraclass);
            }

            $tpl->setarray('FIELD', $extrafields);
            $tpl->setcondition('EXTRAFIELDS');
        }
    }
}
