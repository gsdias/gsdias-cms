<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.3
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

        switch ($newtype) {
            case 'layouts':
                return new $classname(IS_ADMIN);
            break;
            case 'pages':
                return new $classname(IS_ADMIN || IS_EDITOR);
            break;
            case 'users':
                return new $classname(IS_ADMIN);
            break;
            case 'images':
                return new $classname(IS_ADMIN || IS_EDITOR);
            break;
            case 'documents':
                return new $classname(IS_ADMIN || IS_EDITOR);
            break;
            default:
                return new $classname();
            break;
        }
    }
}
