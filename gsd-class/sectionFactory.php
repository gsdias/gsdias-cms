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

        $permission = extendedpermission($newtype);

        switch ($newtype) {
            case 'layouts':
                $permission = gettype($permission) === 'boolean' ? $permission : IS_ADMIN;
                return new $classname($permission);
            break;
            case 'pages':
                $permission = gettype($permission) === 'boolean' ? $permission : IS_ADMIN || IS_EDITOR;
                return new $classname($permission);
            break;
            case 'users':
                $permission = gettype($permission) === 'boolean' ? $permission : IS_ADMIN;
                return new $classname($permission);
            break;
            case 'images':
                $permission = gettype($permission) === 'boolean' ? $permission : IS_ADMIN || IS_EDITOR;
                return new $classname($permission);
            break;
            case 'documents':
                $permission = gettype($permission) === 'boolean' ? $permission : IS_ADMIN || IS_EDITOR;
                return new $classname($permission);
            break;
            default:
                return new $classname($permission);
            break;
        }
    }
}
