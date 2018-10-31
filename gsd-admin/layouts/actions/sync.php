<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.5.1
 */
defined('GVALID') or die;

include_once ROOTPATH.'gsd-include/gsd-layouts'.PHPEXT;
$name = syncLayout($site->a(2));

$tpl->setarray('MESSAGES', array('MSG' => sprintf(lang('LANG_LAYOUT_SYNCED'), $name)));

redirect('/admin/'.$site->a(1));
