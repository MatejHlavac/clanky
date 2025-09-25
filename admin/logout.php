<?php
// Admin odhlásenie
require_once '../includes/auth.php';

// Odhlás admina
logoutAdmin();

// Presmeruj na prihlasovaciu stránku
header('Location: login.php');
exit();
?>
