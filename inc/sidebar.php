<aside id="sidebar-left" class="sidebar-left">
    <div class="sidebar-header">
        <div class="sidebar-title">Workspace</div>
        <div class="sidebar-toggle hidden-xs" data-toggle-class="sidebar-left-collapsed" data-target="html" data-fire-event="sidebar-left-toggle">
            <i class="fa fa-bars" aria-label="Toggle sidebar"></i>
        </div>
    </div>
    <div class="nano">
        <div class="nano-content">
            <div class="sidebar-profile">
                <span class="sidebar-avatar"><?php echo e(strtoupper(substr($_SESSION['firstname'],0,1).substr($_SESSION['lastname'],0,1))); ?></span>
                <div><strong><?php echo e($_SESSION['firstname'].' '.$_SESSION['lastname']); ?></strong><small><?php echo e($_SESSION['role_name'] ?? 'User'); ?></small></div>
            </div>
            <nav id="menu" class="nav-main" role="navigation">
                <ul class="nav nav-main">

                    <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'home.php' ? 'nav-active' : ''; ?>">
                        <a href="home.php">
                            <i class="fa fa-dashboard" aria-hidden="true"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <?php if ($_SESSION['role_id'] == 4): // Road User ?>
                    <li class="nav-parent">
                        <a><i class="fa fa-camera"></i><span>Report Damage</span></a>
                        <ul class="nav nav-children">
                            <li><a href="report_damage.php"><i class="fa fa-plus fa-fw"></i> New Report</a></li>
                            <li><a href="my_reports.php"><i class="fa fa-list fa-fw"></i> My Reports</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="map_view.php">
                            <i class="fa fa-map-marker"></i>
                            <span>View Map</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if ($_SESSION['role_id'] == 3): // Engineer ?>
                    <li class="nav-parent">
                        <a><i class="fa fa-road"></i><span>Road Reports</span></a>
                        <ul class="nav nav-children">
                            <li><a href="all_reports.php"><i class="fa fa-list fa-fw"></i> All Reports</a></li>
                            <li><a href="report_damage.php"><i class="fa fa-plus fa-fw"></i> Submit Report</a></li>
                            <li><a href="pending_reports.php"><i class="fa fa-clock-o fa-fw"></i> Pending Review</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="map_view.php">
                            <i class="fa fa-map-marker"></i>
                            <span>GIS Map View</span>
                        </a>
                    </li>
                    <li>
                        <a href="maintenance_updates.php">
                            <i class="fa fa-wrench"></i>
                            <span>Maintenance Updates</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if ($_SESSION['role_id'] == 2): // Transport Authority ?>
                    <li class="nav-parent">
                        <a><i class="fa fa-road"></i><span>Road Reports</span></a>
                        <ul class="nav nav-children">
                            <li><a href="all_reports.php"><i class="fa fa-list fa-fw"></i> All Reports</a></li>
                            <li><a href="pending_reports.php"><i class="fa fa-clock-o fa-fw"></i> Pending Approval</a></li>
                            <li><a href="critical_reports.php"><i class="fa fa-exclamation-triangle fa-fw"></i> Critical Issues</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="map_view.php">
                            <i class="fa fa-map-marker"></i>
                            <span>GIS Map View</span>
                        </a>
                    </li>
                    <li class="nav-parent">
                        <a><i class="fa fa-bar-chart"></i><span>Analytics</span></a>
                        <ul class="nav nav-children">
                            <li><a href="analytics.php"><i class="fa fa-line-chart fa-fw"></i> Overview</a></li>
                            <li><a href="reports_by_region.php"><i class="fa fa-map fa-fw"></i> By Region</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <?php if ($_SESSION['role_id'] == 1): // Admin ?>
                    <li class="nav-parent">
                        <a><i class="fa fa-road"></i><span>Road Reports</span></a>
                        <ul class="nav nav-children">
                            <li><a href="all_reports.php"><i class="fa fa-list fa-fw"></i> All Reports</a></li>
                            <li><a href="pending_reports.php"><i class="fa fa-clock-o fa-fw"></i> Pending</a></li>
                            <li><a href="critical_reports.php"><i class="fa fa-exclamation-triangle fa-fw"></i> Critical</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="map_view.php">
                            <i class="fa fa-map-marker"></i>
                            <span>GIS Map View</span>
                        </a>
                    </li>
                    <li class="nav-parent">
                        <a><i class="fa fa-bar-chart"></i><span>Analytics</span></a>
                        <ul class="nav nav-children">
                            <li><a href="analytics.php"><i class="fa fa-line-chart fa-fw"></i> Overview</a></li>
                            <li><a href="reports_by_region.php"><i class="fa fa-map fa-fw"></i> By Region</a></li>
                        </ul>
                    </li>
                    <li class="nav-parent">
                        <a><i class="fa fa-cogs"></i><span>Settings</span></a>
                        <ul class="nav nav-children">
                            <li><a href="manage_users.php"><i class="fa fa-users fa-fw"></i> Manage Users</a></li>
                            <li><a href="damage_types.php"><i class="fa fa-tags fa-fw"></i> Damage Types</a></li>
                            <li><a href="system_settings.php"><i class="fa fa-sliders fa-fw"></i> System Settings</a></li>
                            <li><a href="audit_logs.php"><i class="fa fa-shield fa-fw"></i> Activity Logs</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <li><a href="notifications.php"><i class="fa fa-bell"></i><span>Notifications</span></a></li>

                </ul>
            </nav>
            <div class="sidebar-help"><i class="fa fa-shield"></i><strong>Safer roads, faster action</strong><small>AI-assisted infrastructure monitoring for Rwanda.</small></div>
        </div>
    </div>
</aside>
