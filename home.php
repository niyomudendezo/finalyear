<?php
require_once 'inc/db.php';
require_once 'inc/auth.php';
$page_title = 'Dashboard';
$breadcrumb = 'Overview';

// Road users see their submissions; operational roles see the full network.
$scope = (int)$_SESSION['role_id'] === 4 ? ' WHERE reported_by=' . (int)$_SESSION['user_id'] : '';
$and = $scope ? ' AND ' : ' WHERE ';
$total   = (int)$db->query("SELECT COUNT(*) FROM tbl_road_reports$scope")->fetchColumn();
$pending = (int)$db->query("SELECT COUNT(*) FROM tbl_road_reports$scope{$and}status='Pending'")->fetchColumn();
$critical= (int)$db->query("SELECT COUNT(*) FROM tbl_road_reports$scope{$and}severity='Critical'")->fetchColumn();
$resolved= (int)$db->query("SELECT COUNT(*) FROM tbl_road_reports$scope{$and}status='Resolved'")->fetchColumn();
$inprog  = (int)$db->query("SELECT COUNT(*) FROM tbl_road_reports$scope{$and}status='In Progress'")->fetchColumn();

// Recent reports
$recent = $db->query(
    "SELECT r.*, u.firstname, u.lastname, dt.type_name
     FROM tbl_road_reports r
     JOIN tbl_users u ON r.reported_by = u.user_id
     JOIN tbl_damage_types dt ON r.damage_type_id = dt.type_id" . ((int)$_SESSION['role_id'] === 4 ? " WHERE r.reported_by=" . (int)$_SESSION['user_id'] : "") . "
     ORDER BY r.reported_at DESC LIMIT 8"
)->fetchAll(PDO::FETCH_ASSOC);

// Damage by type for chart
$byType = $db->query(
    "SELECT dt.type_name, COUNT(*) as cnt
     FROM tbl_road_reports r
     JOIN tbl_damage_types dt ON r.damage_type_id = dt.type_id" . ((int)$_SESSION['role_id'] === 4 ? " WHERE r.reported_by=" . (int)$_SESSION['user_id'] : "") . "
     GROUP BY dt.type_id"
)->fetchAll(PDO::FETCH_ASSOC);

// Severity breakdown
$bySeverity = $db->query(
    "SELECT severity, COUNT(*) as cnt FROM tbl_road_reports$scope GROUP BY severity"
)->fetchAll(PDO::FETCH_ASSOC);

include 'inc/links.php';
include 'inc/header.php';
?>
<div class="inner-wrapper">
<?php include 'inc/sidebar.php'; ?>
<section role="main" class="content-body">
<?php include 'inc/inner-header.php'; ?>

<section class="dashboard-hero">
        <div class="row">
            <div class="col-md-8">
                <span class="hero-kicker"><i class="fa fa-circle"></i> Live road intelligence &middot; <?php echo date('d M Y'); ?></span>
                <h2>Good <?php echo date('H')<12?'morning':(date('H')<18?'afternoon':'evening'); ?>, <?php echo e($_SESSION['firstname']); ?>.</h2>
                <p>Here is the latest condition of Rwanda's reported road network and the maintenance work requiring attention.</p>
            </div>
            <div class="col-md-4 text-right hero-actions">
                <a href="map_view.php" class="btn btn-ghost"><i class="fa fa-map-marker"></i> Open map</a>
            </div>
        </div>
</section>

<div class="row kpi-grid">
    <div class="col-md-3 col-sm-6">
        <section class="panel stat-card green">
            <div class="panel-body">
                <span class="stat-icon"><i class="fa fa-road"></i></span><span class="stat-trend">Network</span>
                <h3><?php echo $total; ?></h3><p>Total reports</p><small>All recorded road issues</small>
            </div>
        </section>
    </div>
    <div class="col-md-3 col-sm-6">
        <section class="panel stat-card amber">
            <div class="panel-body">
                <span class="stat-icon"><i class="fa fa-clock-o"></i></span><span class="stat-trend">Needs action</span>
                <h3><?php echo $pending; ?></h3><p>Pending review</p><small>Awaiting engineer assessment</small>
            </div>
        </section>
    </div>
    <div class="col-md-3 col-sm-6">
        <section class="panel stat-card coral">
            <div class="panel-body">
                <span class="stat-icon"><i class="fa fa-exclamation-triangle"></i></span><span class="stat-trend">High risk</span>
                <h3><?php echo $critical; ?></h3><p>Critical issues</p><small>Immediate response required</small>
            </div>
        </section>
    </div>
    <div class="col-md-3 col-sm-6">
        <section class="panel stat-card blue">
            <div class="panel-body">
                <span class="stat-icon"><i class="fa fa-check"></i></span><span class="stat-trend">Completed</span>
                <h3><?php echo $resolved; ?></h3><p>Resolved</p><small><?php echo $inprog; ?> currently in progress</small>
            </div>
        </section>
    </div>
</div>

<div class="row">
    <!-- Recent Reports Table -->
    <div class="col-md-8">
        <section class="panel">
            <header class="panel-heading">
                <a href="all_reports.php" class="pull-right panel-link">View all <i class="fa fa-arrow-right"></i></a>
                <span class="panel-overline">Live queue</span><h2 class="panel-title">Recent road reports</h2>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-none">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Severity</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($recent as $r): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($r['report_code']); ?></strong></td>
                                <td><?php echo htmlspecialchars($r['type_name']); ?></td>
                                <td>
                                    <?php
                                    $sev = $r['severity'];
                                    $cls = strtolower($sev);
                                    echo "<span class='label badge-$cls'>$sev</span>";
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars(substr($r['location_name'] ?? 'N/A', 0, 30)); ?></td>
                                <td>
                                    <?php
                                    $st = $r['status'];
                                    $stcls = ['Pending'=>'warning','Under Review'=>'info','Approved'=>'primary','In Progress'=>'warning','Resolved'=>'success','Rejected'=>'danger'];
                                    $c = $stcls[$st] ?? 'default';
                                    echo "<span class='label label-$c'>$st</span>";
                                    ?>
                                </td>
                                <td><?php echo date('d M Y', strtotime($r['reported_at'])); ?></td>
                                <td><a href="view_report.php?id=<?php echo $r['report_id']; ?>" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <!-- Severity Breakdown -->
    <div class="col-md-4">
        <section class="panel">
            <header class="panel-heading">
                <span class="panel-overline">Risk profile</span><h2 class="panel-title">Severity breakdown</h2>
            </header>
            <div class="panel-body">
                <?php
                $sevColors = ['Low'=>'#27ae60','Medium'=>'#f39c12','High'=>'#e67e22','Critical'=>'#e74c3c'];
                foreach ($bySeverity as $s):
                    $pct = $total > 0 ? round($s['cnt']/$total*100) : 0;
                    $col = $sevColors[$s['severity']] ?? '#999';
                ?>
                <div style="margin-bottom:12px;">
                    <div class="clearfix">
                        <span style="color:<?php echo $col; ?>;font-weight:bold;"><?php echo $s['severity']; ?></span>
                        <span class="pull-right"><?php echo $s['cnt']; ?> (<?php echo $pct; ?>%)</span>
                    </div>
                    <div class="priority-bar">
                        <div class="priority-fill" style="width:<?php echo $pct; ?>%;background:<?php echo $col; ?>;"></div>
                    </div>
                </div>
                <?php endforeach; ?>

                <hr><h5 class="section-mini-title"><i class="fa fa-tag"></i> Damage classification</h5>
                <?php foreach ($byType as $bt): ?>
                <div class="clearfix" style="margin-bottom:6px;">
                    <span><?php echo htmlspecialchars($bt['type_name']); ?></span>
                    <span class="badge pull-right"><?php echo $bt['cnt']; ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="panel">
            <header class="panel-heading">
                <span class="panel-overline">Shortcuts</span><h2 class="panel-title">Quick actions</h2>
            </header>
            <div class="panel-body">
                <a href="report_damage.php" class="btn btn-danger btn-block mb-xs"><i class="fa fa-plus"></i> Report New Damage</a>
                <a href="map_view.php" class="btn btn-primary btn-block mb-xs"><i class="fa fa-map"></i> Open GIS Map</a>
                <a href="all_reports.php" class="btn btn-default btn-block"><i class="fa fa-list"></i> All Reports</a>
            </div>
        </section>
    </div>
</div>

</section>
</div>
<?php include 'inc/scripts.php'; ?>
