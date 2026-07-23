<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title', 'Customer Service'); ?> | SIBERKAS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="app-body">
<div class="app-screen app-screen--workspace">
    <div class="app-sidebar-overlay" data-sidebar-overlay></div>
    <aside class="app-sidebar" data-sidebar>
        <a href="<?php echo e(route('cs.dashboard')); ?>" class="app-sidebar__brand">
            <span class="app-sidebar__logo"><i class="bi bi-files"></i></span>
            <span><b class="app-sidebar__title">SIBERKAS</b><small class="app-sidebar__caption">Customer Service</small></span>
        </a>
        <nav class="app-sidebar__nav">
            <span class="app-sidebar__label">Layanan nasabah</span>
            <a href="<?php echo e(route('cs.dashboard')); ?>" class="app-sidebar__link <?php echo e(request()->routeIs('cs.dashboard') ? 'is-active' : ''); ?>"><i class="bi bi-grid-1x2-fill"></i>Dashboard</a>
            <a href="<?php echo e(route('cs.nasabah.index')); ?>" class="app-sidebar__link <?php echo e(request()->routeIs('cs.nasabah.*') ? 'is-active' : ''); ?>"><i class="bi bi-people"></i>Nasabah</a>
            <a href="<?php echo e(route('cs.berkas.index')); ?>" class="app-sidebar__link <?php echo e(request()->routeIs('cs.berkas.*') ? 'is-active' : ''); ?>"><i class="bi bi-folder2-open"></i>Berkas</a>
            <a href="<?php echo e(route('cs.tracking.index')); ?>" class="app-sidebar__link <?php echo e(request()->routeIs('cs.tracking.*') ? 'is-active' : ''); ?>"><i class="bi bi-activity"></i>Tracking</a>
            <a href="<?php echo e(route('cs.transaksi.index')); ?>" class="app-sidebar__link <?php echo e(request()->routeIs('cs.transaksi.*') ? 'is-active' : ''); ?>"><i class="bi bi-cash-coin"></i>Transaksi</a>
            <a href="<?php echo e(route('cs.arsip.index')); ?>" class="app-sidebar__link <?php echo e(request()->routeIs('cs.arsip.*') ? 'is-active' : ''); ?>"><i class="bi bi-archive"></i>Arsip Digital</a>
            <a href="#" class="app-sidebar__link app-sidebar__link--logout" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="bi bi-box-arrow-right"></i>Logout</a>
        </nav>
        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="hidden"><?php echo csrf_field(); ?></form>
    </aside>

    <main class="app-main">
        <header class="app-topbar">
            <div class="app-topbar__left">
                <button type="button" class="app-icon-button" aria-label="Sembunyikan sidebar" data-sidebar-toggle><i class="bi bi-layout-sidebar-inset"></i></button>
                <div class="app-topbar__context"><b class="app-topbar__name"><?php echo e(auth()->user()->name ?? 'Customer Service'); ?></b><span class="app-topbar__role">Customer Service</span></div>
            </div>
            <div class="app-topbar__right">
                <?php echo $__env->make('components.ui.sla-notice', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <span class="status-badge role-badge cs">CS</span>
            </div>
        </header>
        <?php echo $__env->yieldContent('content'); ?>
    </main>
</div>
<?php echo $__env->make('components.ui.loading-indicator', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH E:\punya_taupik\SEMESTER 6\BANK TA\bank_TA\resources\views/layouts/cs.blade.php ENDPATH**/ ?>