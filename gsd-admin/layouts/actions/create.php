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
    include_once ROOTPATH.'gsd-include/gsd-layouts'.PHPEXT;
    
    $content = file_get_contents(sprintf(CLIENTTPLPATH.'_layouts/%s', $site->p('file')));
    
    addLayout($site->p('file'), $site->p('name'), $site->p('ltid'), $csection);

    if (!$csection->showErrors(lang('LANG_LAYOUT_ALREADY_EXISTS'))) {
        $tpl->setarray('MESSAGES', array('MSG' => sprintf(lang('LANG_LAYOUT_CREATED'), $site->p('name'))));

        redirect('/admin/'.$site->a(1));
    }
}
