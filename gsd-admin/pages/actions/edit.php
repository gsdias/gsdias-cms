<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
defined('GVALID') or die;
if ($site->p('save')) {
    $result = $csection->edit();

    if ($result['total']) {
        $modules = array();

        foreach ($site->p() as $module => $value) {
            if (substr($module, 0, 10) == 'value_pm_s') {
                $vals = $value;
                $value = array();
                $moduleid = explode('_', $module);
                foreach ($vals as $i => $val) {
                    $value[] = array(
                            'value' => @$site->p($module)[$i],
                            'class' => @$site->p('class_'.$module)[$i],
                            'style' => @$site->p('style_'.$module)[$i],
                    );
                }
                if (!sizeof(@$modules[$moduleid[4]])) {
                    $modules[$moduleid[4]] = array(
                        'list' => array(),
                        'style' => @$site->p('style_value_pm_'.$moduleid[4]),
                        'class' => @$site->p('class_value_pm_'.$moduleid[4]),
                    );
                }
                $modules[$moduleid[4]]['list'][] = $value;
            } elseif (substr($module, 0, 9) == 'value_pm_') {
                $value = array(
                    'list' => array(
                                array(
                                    array(
                                        'value' => $value,
                                        'class' => $site->p('class_'.$module),
                                        'style' => $site->p('style_'.$module),
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
            $tpl->setarray('MESSAGES', array('MSG' => sprintf(lang('LANG_PAGE_SAVED'), $site->p('title'))));
            redirect('/admin/'.$site->arg(1));
        }
    } else {
        $csection->showErrors(lang('LANG_PAGE_ERROR'));
    }
}
