<?php $__env->startSection('title', 'Dashboard Admin'); ?>

<?php $__env->startSection('content'); ?>
<?php ($doneRate = $totalBerkas ? round(($berkasSelesai / $totalBerkas) * 100) : 0); ?>
<div class="container-fluid">
    <section class="dashboard-hero">
        <h1 class="dashboard-hero__title"><i class="bi bi-shield-check"></i>Dashboard Administrator</h1>
        <p class="dashboard-hero__copy">Pantau layanan berkas, SLA, pengguna aktif, dan transaksi administratif yang telah diposting Akuntan.</p>
        <span class="dashboard-hero__meta"><i class="bi bi-calendar3"></i><?php echo e(now()->translatedFormat('l, d F Y')); ?></span>
    </section>

    <section class="metric-grid">
        <article class="metric"><div class="metric__top"><span class="metric__label">User Aktif</span><span class="metric__icon"><i class="bi bi-people"></i></span></div><b class="metric__value"><?php echo e($totalUserAktif); ?></b><p class="metric__text"><?php echo e($csAktifHariIni); ?> akun CS aktif</p></article>
        <article class="metric"><div class="metric__top"><span class="metric__label">Total Berkas</span><span class="metric__icon"><i class="bi bi-folder2-open"></i></span></div><b class="metric__value"><?php echo e($totalBerkas); ?></b><p class="metric__text">Berkas seluruh sistem</p></article>
        <article class="metric metric--warn"><div class="metric__top"><span class="metric__label">Menunggu Verifikasi</span><span class="metric__icon"><i class="bi bi-patch-question"></i></span></div><b class="metric__value"><?php echo e($menungguVerifikasi); ?></b><p class="metric__text">Perlu proses CS atau Akuntan</p></article>
        <article class="metric metric--rose"><div class="metric__top"><span class="metric__label">SLA Terlambat</span><span class="metric__icon"><i class="bi bi-alarm"></i></span></div><b class="metric__value"><?php echo e($berkasTerlambat); ?></b><p class="metric__text">Perlu intervensi</p></article>
    </section>

    <section class="dashboard-grid">
        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.dashboard-panel','data' => ['title' => 'Monitoring SLA','icon' => 'bi-bell-fill','link' => route('admin.monitoring.berkas'),'linkText' => 'Buka monitoring']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('ui.dashboard-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Monitoring SLA','icon' => 'bi-bell-fill','link' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.monitoring.berkas')),'link-text' => 'Buka monitoring']); ?>
            <div class="sla-summary"><div class="sla-stat"><strong><?php echo e($slaHampir); ?></strong><span>Hampir jatuh tempo</span></div><div class="sla-stat sla-stat--urgent"><strong><?php echo e($berkasTerlambat); ?></strong><span>Melewati SLA</span></div></div>
            <div class="sla-list">
                <?php $__empty_1 = true; $__currentLoopData = $slaList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $berkas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php ($days = now()->startOfDay()->diffInDays($berkas->estimasi_selesai, false)); ?>
                    <div class="sla-item"><div><b class="sla-item__name"><?php echo e($berkas->nasabah?->nama_nasabah ?? '-'); ?></b><span class="sla-item__sub"><?php echo e($berkas->jenis_layanan); ?> · <?php echo e(optional($berkas->estimasi_selesai)->format('d M Y')); ?></span></div><span class="sla-item__status <?php echo e($days >= 0 ? 'is-soon' : ''); ?>"><?php echo e($days < 0 ? 'Terlambat' : ($days === 0 ? 'Hari ini' : $days . ' hari')); ?></span></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-muted small">Tidak ada SLA yang perlu diawasi saat ini.</p>
                <?php endif; ?>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.dashboard-panel','data' => ['title' => 'Distribusi Status Berkas','icon' => 'bi-pie-chart-fill']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('ui.dashboard-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Distribusi Status Berkas','icon' => 'bi-pie-chart-fill']); ?>
            <div class="chart-wrap">
                <div class="donut" style="--value: <?php echo e($doneRate); ?>"><span class="donut__text"><?php echo e($doneRate); ?>%</span></div>
                <div class="bar-list">
                    <?php $__currentLoopData = $statusSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php ($percent = $totalBerkas ? round(($value / $totalBerkas) * 100) : 0); ?>
                        <div class="bar-item"><span><?php echo e($label); ?></span><span class="bar-line"><span style="width: <?php echo e($percent); ?>%"></span></span><b><?php echo e($value); ?></b></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.dashboard-panel','data' => ['title' => 'Ringkasan Keuangan Bulan Ini','icon' => 'bi-wallet2','link' => route('admin.laporan.index'),'linkText' => 'Buka laporan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('ui.dashboard-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Ringkasan Keuangan Bulan Ini','icon' => 'bi-wallet2','link' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.laporan.index')),'link-text' => 'Buka laporan']); ?>
            <div class="financial-list">
                <div class="financial-list__row"><span><i class="bi bi-arrow-down-circle"></i>Pemasukan</span><b class="is-income">Rp <?php echo e(number_format($pemasukanBulan, 0, ',', '.')); ?></b></div>
                <div class="financial-list__row"><span><i class="bi bi-arrow-up-circle"></i>Pengeluaran</span><b class="is-expense">Rp <?php echo e(number_format($pengeluaranBulan, 0, ',', '.')); ?></b></div>
                <div class="financial-list__row financial-list__row--total"><span><i class="bi bi-calculator"></i>Saldo</span><b>Rp <?php echo e(number_format($pemasukanBulan - $pengeluaranBulan, 0, ',', '.')); ?></b></div>
            </div>
            <div class="mini-note"><i class="bi bi-shield-check"></i>Nilai hanya dihitung dari transaksi yang telah diposting Akuntan.</div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.dashboard-panel','data' => ['title' => 'Transaksi Terbaru','icon' => 'bi-cash-stack']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('ui.dashboard-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Transaksi Terbaru','icon' => 'bi-cash-stack']); ?>
            <div class="sla-list">
                <?php $__empty_1 = true; $__currentLoopData = $latestTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="sla-item"><div><b class="sla-item__name"><?php echo e($trx->kategori ?? 'Transaksi'); ?> · <?php echo e($trx->berkas?->nasabah?->nama_nasabah ?? '-'); ?></b><span class="sla-item__sub"><?php echo e($trx->jenis_transaksi); ?> · <?php echo e(optional($trx->tanggal_transaksi)->format('d M Y')); ?></span></div><b>Rp <?php echo e(number_format($trx->nominal, 0, ',', '.')); ?></b></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-muted small">Belum ada transaksi yang tercatat.</p>
                <?php endif; ?>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\punya_taupik\SEMESTER 6\BANK TA\bank_TA\resources\views/admin/dashboard/index.blade.php ENDPATH**/ ?>