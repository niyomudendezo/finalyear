<?php

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function status_label(string $status): string
{
    $classes = ['Pending'=>'warning', 'Under Review'=>'info', 'Approved'=>'primary',
        'In Progress'=>'warning', 'Resolved'=>'success', 'Rejected'=>'danger'];
    return '<span class="label label-' . ($classes[$status] ?? 'default') . '">' . e($status) . '</span>';
}

function severity_label(string $severity): string
{
    return '<span class="label badge-' . strtolower($severity) . '">' . e($severity) . '</span>';
}

function can_manage_reports(): bool
{
    return in_array((int)($_SESSION['role_id'] ?? 0), [1, 2, 3], true);
}

function notify_user(PDO $db, int $userId, string $title, string $message, ?int $reportId = null): void
{
    $stmt = $db->prepare('INSERT INTO tbl_notifications (user_id,title,message,report_id) VALUES (?,?,?,?)');
    $stmt->execute([$userId, $title, $message, $reportId]);
}

function priority_score(string $severity, string $damageType): int
{
    $base = ['Low'=>25, 'Medium'=>50, 'High'=>75, 'Critical'=>95][$severity] ?? 50;
    $risk = ['Pothole'=>5, 'Rutting'=>4, 'Alligator Cracking'=>3, 'Surface Deterioration'=>1][$damageType] ?? 2;
    return min(100, $base + $risk);
}

function audit_log(PDO $db, string $action, string $details = ''): void
{
    try {
        $stmt = $db->prepare('INSERT INTO tbl_audit_logs (user_id,action,details,ip_address) VALUES (?,?,?,?)');
        $stmt->execute([$_SESSION['user_id'] ?? null, $action, substr($details, 0, 500), $_SERVER['REMOTE_ADDR'] ?? null]);
    } catch (Throwable $e) {
        // Logging must never interrupt the operational workflow.
    }
}
