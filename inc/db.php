<?php
session_start();
date_default_timezone_set('Africa/Kigali');

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'rdds';

try {
    $socket = '/opt/lampp/var/mysql/mysql.sock';
    $dsn = is_readable($socket)
        ? "mysql:unix_socket=$socket;dbname=$db_name;charset=utf8mb4"
        : "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
    $db = new PDO($dsn, $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed. Please contact system support.');
}

$stmt = $db->query("SELECT * FROM tbl_system_settings LIMIT 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);
$sys_name = $settings['system_name'] ?? 'Road Damage Detection System';
$sys_org  = $settings['organization'] ?? 'RTDA';
$sys_logo = $settings['system_logo'] ?? 'logo.png';

function generate_uuid(): string {
    return bin2hex(random_bytes(8));
}

require_once __DIR__ . '/functions.php';
