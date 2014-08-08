<?php

if (!IS_ADMIN) {
    header("Location: /admin/pages", true, 302);
}

$mysql->statement('DELETE FROM users WHERE uid = ?;', array($path[2]));

header("Location: /admin/users", true, 302);
