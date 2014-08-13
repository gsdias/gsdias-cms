<?php

if (!IS_ADMIN) {
    header("Location: /admin/pages", true, 302);
    exit;
}

$mysql->statement('SELECT url FROM pages WHERE pid = ?;', array($site->arg(2)));

$currenturl = $mysql->singleresult();

$mysql->statement('DELETE FROM redirect WHERE `destination` = ?;', array($currenturl));

$mysql->statement('DELETE FROM pages WHERE pid = ?;', array($site->arg(2)));

header("Location: /admin/pages", true, 302);
exit;
