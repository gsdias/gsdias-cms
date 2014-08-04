<?php


if (is_file('gsd-install.php')) {
    define('IS_INSTALLED', 0);
} else {
    define('IS_INSTALLED', 1);
}

include_once('gsd-config.php');

$startpoint = 'index';
$main = '';

if (is_file('gsd-install' . PHPEXT)) {
    $main = 'STEP1';
    require_once('gsd-install' . PHPEXT);
    
} elseif ($path[0] == 'gsd-assets') {
    require_once('gsd-assets' . PHPEXT);
} else {
    
    require_once('gsd-credentials' . PHPEXT);

    if ($path[0] == 'admin') {
        require_once('gsd-admin/index.php');
    }
    if ($path[0] == 'logout') {
        $user->logout();
    }

    if (is_file('gsd-client/index.php') && $path[0] != 'admin') {
        $mysql->statement('SELECT * FROM pages WHERE url = :uri', array(':uri' => $uri));
        if (!$mysql->total) {
            $mysql->statement('SELECT destination FROM redirect WHERE `from` = :uri', array(':uri' => $uri));
            if ($mysql->total) {
                header("Location: " . $mysql->singleresult(), true, 301);
            }
            $startpoint = '404';
        } else {
            $page = $mysql->singleline();
            $tpl->setvars(array(
                'PAGE_TITLE' => $page['title'],
                'PAGE_DESCRIPTION' => $page['description'],
                'PAGE_KEYWORDS' => $page['keywords'],
                'PAGE_OG_TITLE' => $page['og_title'],
                'PAGE_OG_DESCRIPTION' => $page['og_description'],
                'PAGE_OG_IMAGE' => $page['og_image']
            ));
        }
        require_once('gsd-client/index.php');
    }
}

$tpl->includeFiles('MAIN', $main);
$tpl->setFile($startpoint);

echo $tpl;
