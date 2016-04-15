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
if (!$csection->permission) {
    redirect('/admin/'.$site->arg(1));
}

if (@$_REQUEST['save']) {
    $fields = array(
        array('title', array('isRequired', 'isString')),
        array('description', array('isString')),
        array('keywords', array('isString')),
        array('tags', array('isString')),
        array('og_title', array('isString')),
        array('og_image', array('isNumber')),
        array('og_description', array('isString')),
        array('parent', array('isNumber')),
        array('show_menu', array('isCheckbox')),
        array('require_auth', array('isCheckbox')),
        array('published', array('isCheckbox'))
    );

    $result = $csection->edit($fields);

    if ($result['total']) {
        $modules = array();

        foreach ($_REQUEST as $module => $value) {
            if (substr($module, 0, 10) == 'value_pm_s') {
                $vals = $value;
                $value = array();
                $moduleid = explode('_', $module);
                foreach ($vals as $i => $val) {
                    $value[] = array(
                            'value' => @$_REQUEST[$module][$i],
                            'class' => @$_REQUEST['class_'.$module][$i],
                            'style' => @$_REQUEST['style_'.$module][$i],
                    );
                }
                if (!sizeof(@$modules[$moduleid[4]])) {
                    $modules[$moduleid[4]] = array(
                        'list' => array(),
                        'style' => @$_REQUEST['style_value_pm_'.$moduleid[4]],
                        'class' => @$_REQUEST['class_value_pm_'.$moduleid[4]],
                    );
                }
                $modules[$moduleid[4]]['list'][] = $value;
            } elseif (substr($module, 0, 9) == 'value_pm_') {
                $value = array(
                    'list' => array(
                                array(
                                    array(
                                        'value' => $value,
                                        'class' => @$_REQUEST['class_'.$module],
                                        'style' => @$_REQUEST['style_'.$module],
                                    ),
                                ),
                            ),
                    'class' => '',
                    'style' => '',
                );

                $mysql->reset()
                    ->update('pagemodules')
                    ->fields(array('data'))
                    ->where('pmid = ?')
                    ->values(array(serialize($value), substr($module, 9)))
                    ->exec();
            }
        }

        if (sizeof($modules)) {
            foreach ($modules as $id => $value) {
                $mysql->reset()
                    ->update('pagemodules')
                    ->fields(array('data'))
                    ->where('pmid = ?')
                    ->values(array(serialize($value), $id))
                    ->exec();
            }
        }
        if (!isset($api)) {
            $_SESSION['message'] = sprintf(lang('LANG_PAGE_SAVED'), $_REQUEST['title']);

            redirect('/admin/'.$site->arg(1));
        }
    } else {
        if (is_array($result['errmsg'])) {
            foreach($result['errmsg'] as $msg) {
                $tpl->setvar('ERRORS', $msg.'<br>');
            }
        } else {
            $tpl->setvar('ERRORS', '{LANG_PAGE_ERROR}');
        }
        $tpl->setcondition('ERRORS');
    }
}
