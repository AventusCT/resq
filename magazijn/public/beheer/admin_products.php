<?php
/**
 * Admin Products Management Page
 * Redirects to main inventory management
 */
require_once __DIR__ . '/../Includes/db.php';
require_once __DIR__ . '/../classes/User.php';

User::requireAdmin();

header("Location: ../inventarisbeheer.php");
exit();
