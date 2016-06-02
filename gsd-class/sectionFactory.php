<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.2
 */
namespace GSD;

class sectionFactory
{
    public static function create($type)
    {
        if (class_exists('GSD\\Extended\\extended'.$type)) {
            $classname = 'GSD\\Extended\\extended'.$type;
        } else {
            $classname = 'GSD\\'.$type;
        }

        return new $classname;

    }
}
