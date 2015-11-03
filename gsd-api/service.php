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
$classname = 'GSD\\Api\\api'.ucwords(strtolower($_SERVER['REQUEST_METHOD']));
$classnameextended = 'GSD\\Api\\Extended\\apiExtended'.ucwords(strtolower($_SERVER['REQUEST_METHOD']));

$_extra = array(
    'method' => class_exists($classnameextended) ? new $classnameextended() : new $classname(),
    'other' => class_exists('GSD\\Api\\Extended\\apiExtendedOther') ? new GSD\Api\Extended\apiExtendedOther() : new GSD\Api\apiOther(),
);
