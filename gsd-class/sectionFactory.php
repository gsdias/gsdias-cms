<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.5.1
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.2
 */
namespace GSD;

class sectionFactory
{
    public static function create($type)
    {
        if (class_exists('GSD\\Extended\\'.$type)) {
            $classname = 'GSD\\Extended\\'.$type;
            $newtype = substr($type, 8);
        } else {
            $classname = 'GSD\\'.$type;
            $newtype = $type;
        }

        return new $classname();

    }
}
