<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @version    1.2
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.2
 */
namespace GSD;

class sectionFactory
{
    public static function create($type)
    {
        switch ($type) {
            case 'layouts':
                return new layouts();
            break;
            case 'pages':
                return new pages();
            break;
            case 'users':
                return new users();
            break;
            case 'images':
                return new images();
            break;
            case 'documents':
                return new documents();
            break;
            default:
                return self::extended($type);
        }
    }

    public static function extended($type)
    {
        if (class_exists('GSD\\Extended\\'.$type)) {
            $classname = 'GSD\\Extended\\'.$type;

            return new $classname();
        }
    }
}
