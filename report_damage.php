<?php
require_once 'inc/db.php';
require_once 'inc/auth.php';
$page_title = 'Report Road Damage';
$breadcrumb = 'Reports';

$damage_types = $db->query("SELECT * FROM tbl_damage_types ORDER BY type_name")->fetchAll(PDO::FETCH_ASSOC);

$msg = $msge = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $damage_type_id = (int)($_POST['damage_type_id'] ?? 0);
    $severity       = $_POST['severity'] ?? '';
    $description    = trim($_POST['description'] ?? '');
    $location_name  = trim($_POST['location_name'] ?? '');
    $latitude       = $_POST['latitude'] ?? null;
    $longitude      = $_POST['longitude'] ?? null;
    $province       = trim($_POST['province'] ?? '');
    $district       = trim($_POST['district'] ?? '');
    $sector         = trim($_POST['sector'] ?? '');

    $valid_severities = ['Low','Medium','High','Critical'];
    $type_stmt = $db->prepare('SELECT type_name FROM tbl_damage_types WHERE type_id=?');
    $type_stmt->execute([$damage_type_id]);
    $damage_type_name = $type_stmt->fetchColumn();
    if (!$damage_type_name || !in_array($severity, $valid_severities, true) || $location_name === '') {
        $msge = 'Please provide a valid damage type, severity, and location.';
    }

    $ai_confidence = null; // Populated only when a real model is integrated.
    $priority_score = priority_score($severity, (string)$damage_type_name);

    // Handle image upload
    $image_path = null;
    if (!$msge && !empty($_FILES['road_image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['road_image']['name'], PATHINFO_EXTENSION));
        $mime = mime_content_type($_FILES['road_image']['tmp_name']);
        $allowed = ['image/jpeg'=>['jpg','jpeg'], 'image/png'=>['png'], 'image/webp'=>['webp']];
        if ($_FILES['road_image']['size'] <= 5 * 1024 * 1024 && isset($allowed[$mime]) && in_array($ext, $allowed[$mime], true)) {
            $filename = 'road_' . time() . '_' . generate_uuid() . '.' . $ext;
            $dest = 'uploads/road_images/' . $filename;
            if (move_uploaded_file($_FILES['road_image']['tmp_name'], $dest)) {
                $image_path = $dest;
            }
        } else {
            $msge = 'The image must be a valid JPG, PNG, or WebP file no larger than 5 MB.';
        }
    }

    // Generate report code
    if (!$msge) {
    $report_code = 'RPT-' . date('Y') . '-' . strtoupper(substr(generate_uuid(), 0, 6));

    $stmt = $db->prepare(
        "INSERT INTO tbl_road_reports
         (report_code, reported_by, damage_type_id, severity, description, location_name,
          latitude, longitude, province, district, sector, image_path, ai_confidence, priority_score)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
    );
    $stmt->execute([
        $report_code, $_SESSION['user_id'], $damage_type_id, $severity, $description,
        $location_name, $latitude ?: null, $longitude ?: null,
        $province, $district, $sector, $image_path, $ai_confidence, $priority_score
    ]);
    $report_id = (int)$db->lastInsertId();
    audit_log($db, 'REPORT_CREATED', $report_code . ' submitted from ' . $location_name);
    $admins = $db->query('SELECT user_id FROM tbl_users WHERE role_id IN (1,2,3) AND is_active=1')->fetchAll(PDO::FETCH_COLUMN);
    foreach ($admins as $admin_id) {
        if ((int)$admin_id !== (int)$_SESSION['user_id']) notify_user($db, (int)$admin_id, 'New road damage report', "$report_code requires assessment.", $report_id);
    }
    $msg = 'Report <strong>' . e($report_code) . '</strong> submitted successfully. Priority score: <strong>' . $priority_score . '/100</strong>. An engineer will validate the classification.';
    }
}

include 'inc/links.php';
include 'inc/header.php';
?>
<div class="inner-wrapper">
<?php include 'inc/sidebar.php'; ?>
<section role="main" class="content-body">
<?php include 'inc/inner-header.php'; ?>

<?php if ($msg): ?>
<div class="alert alert-success alert-dismissible"><button class="close" data-dismiss="alert">&times;</button><?php echo $msg; ?></div>
<?php endif; ?>
<?php if ($msge): ?>
<div class="alert alert-danger alert-dismissible"><button class="close" data-dismiss="alert">&times;</button><?php echo $msge; ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <section class="panel">
            <header class="panel-heading">
                <span class="panel-overline">Field data collection</span><h2 class="panel-title">Submit road damage evidence</h2>
            </header>
            <div class="panel-body">
                <div class="report-intro"><i class="fa fa-mobile"></i><div><strong>Mobile-ready inspection form</strong><small>Capture a clear road image, attach the precise GPS position, and provide the initial field assessment.</small></div></div>
                <form method="POST" enctype="multipart/form-data">
                    <h4 class="form-section-title"><span>1</span> Damage assessment</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Damage Type <span class="text-danger">*</span></label>
                                <select name="damage_type_id" class="form-control" required>
                                    <option value="">-- Select Type --</option>
                                    <?php foreach ($damage_types as $dt): ?>
                                    <option value="<?php echo $dt['type_id']; ?>"><?php echo htmlspecialchars($dt['type_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Severity Level <span class="text-danger">*</span></label>
                                <select name="severity" class="form-control" required>
                                    <option value="">-- Select Severity --</option>
                                    <option value="Low">Low</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                    <option value="Critical">Critical</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Describe the road damage..."></textarea>
                    </div>

                    <h4 class="form-section-title"><span>2</span> Location and GIS coordinates</h4>
                    <div class="form-group">
                        <label>Location Name <span class="text-danger">*</span></label>
                        <input type="text" name="location_name" class="form-control" placeholder="e.g. KN 5 Rd, near Convention Centre" required>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Province</label>
                                <select name="province" class="form-control">
                                    <option value="">-- Province --</option>
                                    <option>Kigali City</option>
                                    <option>Northern Province</option>
                                    <option>Southern Province</option>
                                    <option>Eastern Province</option>
                                    <option>Western Province</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>District</label>
                                <input type="text" name="district" class="form-control" placeholder="District">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Sector</label>
                                <input type="text" name="sector" class="form-control" placeholder="Sector">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fa fa-map-marker"></i> Latitude</label>
                                <input type="text" name="latitude" id="latitude" class="form-control" placeholder="Auto-detected or enter manually">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fa fa-map-marker"></i> Longitude</label>
                                <input type="text" name="longitude" id="longitude" class="form-control" placeholder="Auto-detected or enter manually">
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-xs btn-default mb-sm" id="getLocation">
                        <i class="fa fa-crosshairs"></i> Detect My Location
                    </button>

                    <h4 class="form-section-title"><span>3</span> Photographic evidence</h4>
                    <div class="form-group">
                        <label>Road Image <span class="text-muted">(JPG, PNG, WebP, max 5MB)</span></label>
                        <input type="file" name="road_image" class="form-control" accept="image/jpeg,image/png,image/webp" capture="environment" id="imageInput" required>
                        <div id="imagePreview" style="margin-top:10px;display:none;">
                            <img id="previewImg" class="report-image" src="" alt="Preview">
                            <p class="text-muted text-center mt-xs"><span class="ai-badge"><i class="fa fa-search"></i> Image ready for automated analysis and engineer validation</span></p>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-danger btn-lg">
                        <i class="fa fa-paper-plane"></i> Submit Report
                    </button>
                    <a href="home.php" class="btn btn-default btn-lg">Cancel</a>
                </form>
            </div>
        </section>
    </div>

    <div class="col-md-4">
        <section class="panel guide-card">
            <header class="panel-heading">
                <span class="panel-overline">Quality checklist</span><h2 class="panel-title"><i class="fa fa-info-circle"></i> Reporting guide</h2>
            </header>
            <div class="panel-body">
                <ul class="list-unstyled">
                    <li class="mb-xs"><i class="fa fa-check text-success"></i> Take a clear photo of the damage</li>
                    <li class="mb-xs"><i class="fa fa-check text-success"></i> Enable GPS for accurate location</li>
                    <li class="mb-xs"><i class="fa fa-check text-success"></i> Select the correct damage type</li>
                    <li class="mb-xs"><i class="fa fa-check text-success"></i> Describe the severity accurately</li>
                    <li class="mb-xs"><i class="fa fa-check text-success"></i> Provide the road name or landmark</li>
                </ul>
                <hr>
                <h5>Severity Guide</h5>
                <p><span class="label badge-low">Low</span> Minor surface wear, no immediate risk</p>
                <p><span class="label badge-medium">Medium</span> Visible damage, caution needed</p>
                <p><span class="label badge-high">High</span> Significant damage, vehicle risk</p>
                <p><span class="label badge-critical">Critical</span> Dangerous, immediate action needed</p>
            </div>
        </section>
    </div>
</div>

</section>
</div>
<?php include 'inc/scripts.php'; ?>
<script>
document.getElementById('getLocation').addEventListener('click', function() {
    if (navigator.geolocation) {
        this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Detecting...';
        var btn = this;
        navigator.geolocation.getCurrentPosition(function(pos) {
            document.getElementById('latitude').value  = pos.coords.latitude.toFixed(6);
            document.getElementById('longitude').value = pos.coords.longitude.toFixed(6);
            btn.innerHTML = '<i class="fa fa-check text-success"></i> Location Detected';
        }, function() {
            btn.innerHTML = '<i class="fa fa-crosshairs"></i> Detect My Location';
            alert('Could not detect location. Please enter manually.');
        });
    }
});

document.getElementById('imageInput').addEventListener('change', function() {
    var file = this.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});
</script>
