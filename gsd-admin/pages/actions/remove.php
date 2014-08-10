<?php

if (!IS_ADMIN) {
    header("Location: /admin/pages", true, 302);
    exit;
}

$mysql->statement('DELETE FROM pages WHERE pid = ?;', array($site->path[2]));

header("Location: /admin/pages", true, 302);
exit;
