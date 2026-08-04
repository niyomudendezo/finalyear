<header class="page-header">
    <div class="page-heading-copy">
        <span class="page-eyebrow"><?php echo e($breadcrumb ?? 'Road intelligence'); ?></span>
        <h2><?php echo e($page_title ?? 'Dashboard'); ?></h2>
    </div>
    <?php if (basename($_SERVER['PHP_SELF']) !== 'report_damage.php'): ?><a href="report_damage.php" class="page-primary-action"><i class="fa fa-camera"></i> New damage report</a><?php endif; ?>
</header>
