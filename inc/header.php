<?php
$role_stmt = $db->prepare("SELECT role_name FROM tbl_roles WHERE role_id = ?");
$role_stmt->execute([$_SESSION['role_id']]);
$role_row = $role_stmt->fetch();
$role_name = $role_row['role_name'] ?? 'User';

$notif_stmt = $db->prepare("SELECT COUNT(*) FROM tbl_notifications WHERE user_id = ? AND is_read = 0");
$notif_stmt->execute([$_SESSION['user_id']]);
$unread_notifs = (int)$notif_stmt->fetchColumn();
?>
<header class="header">
    <div class="logo-container">
        <a href="home.php" class="logo">
            <span class="brand-mark"><i class="fa fa-road"></i></span>
            <span class="brand-copy"><strong>RDDS</strong><small>RTDA Monitoring</small></span>
        </a>
        <div class="visible-xs toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened">
            <i class="fa fa-bars" aria-label="Toggle sidebar"></i>
        </div>
    </div>
    <div class="header-right">
        <a href="notifications.php" class="notification-button" aria-label="Notifications">
            <i class="fa fa-bell"></i>
            <?php if ($unread_notifs > 0): ?><span class="badge badge-danger"><?php echo $unread_notifs; ?></span><?php endif; ?>
        </a>
        <div id="userbox" class="userbox">
            <a href="#" data-toggle="dropdown">
                <figure class="profile-picture">
                    <span class="user-avatar"><?php echo e(strtoupper(substr($_SESSION['firstname'],0,1).substr($_SESSION['lastname'],0,1))); ?></span>
                </figure>
                <div class="profile-info">
                    <span class="name"><?php echo htmlspecialchars($_SESSION['firstname'] . ' ' . $_SESSION['lastname']); ?></span>
                    <span class="role"><?php echo htmlspecialchars($role_name); ?></span>
                </div>
                <i class="fa fa-angle-down custom-caret"></i>
            </a>
            <div class="dropdown-menu">
                <ul class="list-unstyled">
                    <li><a href="profile.php"><i class="fa fa-user"></i> My Profile</a></li>
                    <li><a href="logout.php"><i class="fa fa-power-off"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>
