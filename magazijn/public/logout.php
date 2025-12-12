<?php
/**
 * Logout Page for ResQ
 */
require_once __DIR__ . '/classes/User.php';

User::logout();
header("Location: login.php?logged_out=1");
exit();

