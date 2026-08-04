<?php
require_once 'inc/db.php'; require_once 'inc/auth.php';
$page_title = 'Road Damage Reports'; $breadcrumb = 'Reports';
$where=[]; $params=[];
if ((int)$_SESSION['role_id'] === 4 || isset($_GET['mine'])) { $where[]='r.reported_by=?'; $params[]=$_SESSION['user_id']; $page_title='My Reports'; }
foreach (['status'=>'r.status','severity'=>'r.severity','province'=>'r.province'] as $key=>$column) {
    if (!empty($_GET[$key])) { $where[]="$column=?"; $params[]=$_GET[$key]; }
}
if (!empty($_GET['q'])) { $where[]='(r.report_code LIKE ? OR r.location_name LIKE ? OR r.district LIKE ?)'; $term='%'.trim($_GET['q']).'%'; array_push($params,$term,$term,$term); }
$sql='SELECT r.*,dt.type_name,u.firstname,u.lastname FROM tbl_road_reports r JOIN tbl_damage_types dt ON dt.type_id=r.damage_type_id JOIN tbl_users u ON u.user_id=r.reported_by'.($where?' WHERE '.implode(' AND ',$where):'').' ORDER BY r.priority_score DESC,r.reported_at DESC';
$stmt=$db->prepare($sql); $stmt->execute($params); $reports=$stmt->fetchAll(PDO::FETCH_ASSOC);
include 'inc/links.php'; include 'inc/header.php';
?>
<div class="inner-wrapper"><?php include 'inc/sidebar.php'; ?><section role="main" class="content-body"><?php include 'inc/inner-header.php'; ?>
<section class="panel"><header class="panel-heading"><span class="panel-overline">Prioritized inspection queue</span><h2 class="panel-title"><i class="fa fa-road"></i> <?php echo e($page_title); ?></h2></header><div class="panel-body">
<div class="queue-summary"><span><strong><?php echo count($reports); ?></strong> matching reports</span><span><i class="fa fa-sort-amount-desc"></i> Ordered by maintenance priority</span><span><i class="fa fa-shield"></i> Role-filtered access</span></div>
<form method="get" class="row report-filters">
 <div class="col-sm-3"><input class="form-control" name="q" value="<?php echo e($_GET['q']??''); ?>" placeholder="Code, road, or district"></div>
 <div class="col-sm-2"><select class="form-control" name="status"><option value="">All statuses</option><?php foreach(['Pending','Under Review','Approved','In Progress','Resolved','Rejected'] as $v): ?><option <?php echo ($_GET['status']??'')===$v?'selected':''; ?>><?php echo e($v); ?></option><?php endforeach; ?></select></div>
 <div class="col-sm-2"><select class="form-control" name="severity"><option value="">All severities</option><?php foreach(['Low','Medium','High','Critical'] as $v): ?><option <?php echo ($_GET['severity']??'')===$v?'selected':''; ?>><?php echo e($v); ?></option><?php endforeach; ?></select></div>
 <div class="col-sm-3"><select class="form-control" name="province"><option value="">All provinces</option><?php foreach(['Kigali City','Northern Province','Southern Province','Eastern Province','Western Province'] as $v): ?><option <?php echo ($_GET['province']??'')===$v?'selected':''; ?>><?php echo e($v); ?></option><?php endforeach; ?></select></div>
 <div class="col-sm-2"><button class="btn btn-primary btn-block"><i class="fa fa-filter"></i> Filter</button></div>
</form><hr>
<div class="table-responsive"><table class="table table-striped table-hover reports-table"><thead><tr><th>Code</th><th>Damage</th><th>Severity</th><th>Location</th><th>Priority</th><th>Status</th><th>Reported</th><th></th></tr></thead><tbody>
<?php foreach($reports as $r): ?><tr><td><strong><?php echo e($r['report_code']); ?></strong></td><td><?php echo e($r['type_name']); ?></td><td><?php echo severity_label($r['severity']); ?></td><td><?php echo e($r['location_name']); ?><small class="text-muted"><br><?php echo e(trim(($r['district']??'').' '.($r['province']??''))); ?></small></td><td><strong><?php echo (int)$r['priority_score']; ?></strong>/100</td><td><?php echo status_label($r['status']); ?></td><td><?php echo date('d M Y',strtotime($r['reported_at'])); ?></td><td><a class="btn btn-xs btn-info" href="view_report.php?id=<?php echo (int)$r['report_id']; ?>"><i class="fa fa-eye"></i> View</a></td></tr><?php endforeach; ?>
<?php if(!$reports): ?><tr><td colspan="8" class="text-center text-muted">No reports match these filters.</td></tr><?php endif; ?></tbody></table></div></div></section>
</section></div><?php include 'inc/scripts.php'; ?>
