<?php

if (!IS_LOGGED) {
    $startpoint = 'admin/login';
} else {
    $startpoint = 'admin/index';
}