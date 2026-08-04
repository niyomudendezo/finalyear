<?php
require_once 'inc/db.php';
audit_log($db, 'LOGOUT', 'User signed out');
session_destroy();
header('Location: index.php');
exit();
