<!doctype html>
<html class="fixed rdds-app">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($sys_name); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="theme-color" content="#12372a"/>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" href="assets/vendor/font-awesome/css/font-awesome.css"/>
    <link rel="stylesheet" href="assets/vendor/magnific-popup/magnific-popup.css"/>
    <link rel="stylesheet" href="assets/vendor/bootstrap-datepicker/css/datepicker3.css"/>
    <link rel="stylesheet" href="assets/vendor/select2/select2.css"/>
    <link rel="stylesheet" href="assets/vendor/jquery-ui/css/ui-lightness/jquery-ui-1.10.4.custom.css"/>
    <link rel="stylesheet" href="assets/vendor/morris/morris.css"/>
    <link rel="stylesheet" href="assets/vendor/jquery-datatables-bs3/assets/css/datatables.css"/>
    <link rel="stylesheet" href="assets/stylesheets/theme.css"/>
    <link rel="stylesheet" href="assets/stylesheets/skins/default.css"/>
    <link rel="stylesheet" href="assets/stylesheets/theme-custom.css"/>
    <link rel="stylesheet" href="assets/css/rdds.css?v=<?php echo filemtime(__DIR__ . '/../assets/css/rdds.css'); ?>"/>
    <?php if (basename($_SERVER['PHP_SELF']) === 'map_view.php'): ?><link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIINfQ3ynhHTNHDNDKavMFx0nZ2V4YI8S5Y=" crossorigin="anonymous"/><?php endif; ?>
    <script src="assets/vendor/modernizr/modernizr.js"></script>
    <script src="lib/jquery.js"></script>
</head>
<body class="rdds-body">
<section class="body">
