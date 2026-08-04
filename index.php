<?php
require_once 'inc/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: home.php'); exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username && $password) {
        $stmt = $db->prepare("SELECT u.*, r.role_name FROM tbl_users u JOIN tbl_roles r ON u.role_id = r.role_id WHERE u.username = ? AND u.is_active = 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']  = $user['user_id'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname']  = $user['lastname'];
            $_SESSION['role_id']   = $user['role_id'];
            $_SESSION['role_name'] = $user['role_name'];
            audit_log($db, 'LOGIN', 'Successful sign in for ' . $user['username']);
            header('Location: home.php'); exit();
        } else {
            $error = 'Invalid username or password.';
        }
    } else {
        $error = 'Please enter username and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login &mdash; Road Damage Detection System</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" href="assets/vendor/font-awesome/css/font-awesome.css"/>
    <link rel="stylesheet" href="assets/css/rdds.css?v=<?php echo filemtime(__DIR__ . '/assets/css/rdds.css'); ?>"/>
</head>
<body class="auth-page">
<div class="auth-shell">
    <section class="auth-story">
        <div class="auth-brand"><span><i class="fa fa-road"></i></span><div><strong>RDDS</strong><small>Rwanda Transport Development Agency</small></div></div>
        <div class="auth-story-copy"><span class="auth-kicker">Intelligent infrastructure monitoring</span><h1>Detect earlier.<br>Repair smarter.<br><em>Keep Rwanda moving.</em></h1><p>A unified platform for road users, engineers, and transport authorities to report, map, prioritize, and resolve road damage.</p><div class="auth-capabilities"><span><i class="fa fa-camera"></i> Mobile reporting</span><span><i class="fa fa-map-marker"></i> GIS mapping</span><span><i class="fa fa-line-chart"></i> Risk analytics</span></div></div>
        <div class="auth-road-lines"><i></i><i></i><i></i></div>
    </section>
    <section class="auth-form-side">
    <div class="login-box">
        <div class="logo-area">
            <span class="auth-lock"><i class="fa fa-shield"></i></span>
            <span class="auth-kicker">Secure workspace</span>
            <h3>Welcome back</h3>
            <p>Sign in to monitor Rwanda's road network</p>
        </div>
        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        <?php if (isset($_GET['registered'])): ?><div class="alert alert-success">Account created. You can now sign in.</div><?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Your username" required autofocus autocomplete="username">
                </div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Your password" required autocomplete="current-password">
                </div>
            </div>
            <button type="submit" class="btn auth-submit btn-block btn-lg">
                Sign in to workspace <i class="fa fa-arrow-right"></i>
            </button>
        </form>
        <p class="auth-register">Are you a road user? <a href="register.php">Create an account</a></p>
    </div>
    <p class="auth-footer">Protected RTDA information system &middot; <?php echo date('Y'); ?></p>
    </section>
</div>
<script src="assets/vendor/jquery/jquery.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
</body>
</html>
