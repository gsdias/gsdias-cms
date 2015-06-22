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
        switch ($type) {
            case 'layouts':
                return new layouts(IS_ADMIN);
            break;
            case 'pages':
                return new pages(IS_ADMIN || IS_EDITOR);
            break;
            case 'users':
                return new users(IS_ADMIN);
            break;
            case 'images':
                return new images(IS_ADMIN || IS_EDITOR);
            break;
            case 'documents':
                return new documents(IS_ADMIN || IS_EDITOR);
            break;
            default:
                return self::extended($type);
        }
    }

    public static function extended($type, $permission = IS_ADMIN)
    {
        if (class_exists('GSD\\Extended\\'.$type)) {
            $classname = 'GSD\\Extended\\'.$type;

            return new $classname($permission);
        }
    }
}
