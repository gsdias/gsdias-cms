<?php

if (!IS_ADMIN || $site->arg(2) == 1) {
    $_SESSION['error'] = 'You can\'t remove the default user.';
    header("Location: /admin/users", true, 302);
    exit;
}

$mysql->statement('DELETE FROM users WHERE uid = ?;', array($site->arg(2)));

header("Location: /admin/users", true, 302);
exit;
