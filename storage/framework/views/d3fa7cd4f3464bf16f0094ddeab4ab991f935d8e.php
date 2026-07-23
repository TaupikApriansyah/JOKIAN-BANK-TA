<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title', 'Akuntansi'); ?> | SIBERKAS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="app-body" <?php if($errors->any() && old('_modal')): ?> data-open-modal="<?php echo e(old('_modal')); ?>" <?php endif; ?>>
<div class="app-screen app-screen--workspace">
    <div class="app-sidebar-overlay" data-sidebar-overlay></div>
    <aside class="app-sidebar" data-sidebar>
        <a href="<?php echo e(route('akuntan.dashboard')); ?>" class="app-sidebar__brand">
            <span class="app-sidebar__logo"><i class="bi bi-calculator-fill"></i></span>
            <span><b class="app-sidebar__title">SIBERKAS</b><small class="app-sidebar__caption">Akuntansi Administratif</small></span>
        </a>
        <nav class="app-sidebar__nav">
            <span class="app-sidebar__label">Keuangan layanan</span>
            <a href="<?php echo e(route('akuntan.dashboard')); ?>" class="app-sidebar__link <?php echo e(request()->routeIs('akuntan.dashboard') ? 'is-active' : ''); ?>"><i class="bi bi-grid-1x2-fill"></i>Dashboard</a>
            <a href="<?php echo e(route('akuntan.transaksi.index')); ?>" class="app-sidebar__link <?php echo e(request()->routeIs('akuntan.transaksi.*') ? 'is-active' : ''); ?>"><i class="bi bi-patch-check"></i>Verifikasi Transaksi</a>
            <a href="<?php echo e(route('akuntan.akun.index')); ?>" class="app-sidebar__link <?php echo e(request()->routeIs('akuntan.akun.*') ? 'is-active' : ''); ?>"><i class="bi bi-journal-bookmark"></i>Daftar Akun</a>
            <a href="<?php echo e(route('akuntan.kas-kecil.index')); ?>" class="app-sidebar__link <?php echo e(request()->routeIs('akuntan.kas-kecil.*') ? 'is-active' : ''); ?>"><i class="bi bi-wallet2"></i>Petty Cash</a>
            <a href="<?php echo e(route('akuntan.jurnal.index')); ?>" class="app-sidebar__link <?php echo e(request()->routeIs('akuntan.jurnal.index') ? 'is-active' : ''); ?>"><i class="bi bi-journal-text"></i>Jurnal Umum</a>
            <a href="<?php echo e(route('akuntan.jurnal.ledger')); ?>" class="app-sidebar__link <?php echo e(request()->routeIs('akuntan.jurnal.ledger') ? 'is-active' : ''); ?>"><i class="bi bi-table"></i>Buku Besar</a>
            <a href="<?php echo e(route('akuntan.laporan.index')); ?>" class="app-sidebar__link <?php echo e(request()->routeIs('akuntan.laporan.*') ? 'is-active' : ''); ?>"><i class="bi bi-file-earmark-bar-graph"></i>Laporan Akuntansi</a>
            <a href="#" class="app-sidebar__link app-sidebar__link--logout" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="bi bi-box-arrow-right"></i>Logout</a>
        </nav>
        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="hidden"><?php echo csrf_field(); ?></form>
    </aside>

    <main class="app-main">
        <header class="app-topbar">
            <div class="app-topbar__left">
                <button type="button" class="app-icon-button" aria-label="Sembunyikan sidebar" data-sidebar-toggle><i class="bi bi-layout-sidebar-inset"></i></button>
                <div class="app-topbar__context"><b class="app-topbar__name"><?php echo e(auth()->user()->name ?? 'Petugas Akuntansi'); ?></b><span class="app-topbar__role">Akuntansi Administratif</span></div>
            </div>
            <div class="app-topbar__right">
                <?php echo $__env->make('components.ui.accounting-notice', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <span class="status-badge role-badge akuntan">AKUNTAN</span>
            </div>
        </header>
        <?php echo $__env->yieldContent('content'); ?>
    </main>
</div>
<?php echo $__env->make('components.ui.loading-indicator', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH E:\punya_taupik\SEMESTER 6\BANK TA\bank_TA\resources\views/layouts/akuntan.blade.php ENDPATH**/ ?>