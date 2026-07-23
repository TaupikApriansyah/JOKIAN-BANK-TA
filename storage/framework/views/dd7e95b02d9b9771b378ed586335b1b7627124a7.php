<?php $__env->startSection('title', 'Dashboard CS'); ?>

<?php $__env->startSection('content'); ?>
<?php ($doneRate = $totalBerkas ? round(($berkasSelesai / $totalBerkas) * 100) : 0); ?>
<div class="container-fluid">
    <section class="dashboard-hero">
        <h1 class="dashboard-hero__title"><i class="bi bi-person-workspace"></i>Dashboard Customer Service</h1>
        <p class="dashboard-hero__copy">Ringkasan berkas, transaksi, dan peringatan SLA dari data yang kamu kelola.</p>
        <span class="dashboard-hero__meta"><i class="bi bi-calendar3"></i><?php echo e(now()->translatedFormat('l, d F Y')); ?></span>
    </section>

    <section class="metric-grid">
        <article class="metric"><div class="metric__top"><span class="metric__label">Nasabah</span><span class="metric__icon"><i class="bi bi-people"></i></span></div><b class="metric__value"><?php echo e($totalNasabah); ?></b><p class="metric__text">Data nasabah milikmu</p></article>
        <article class="metric"><div class="metric__top"><span class="metric__label">Berkas Aktif</span><span class="metric__icon"><i class="bi bi-folder2-open"></i></span></div><b class="metric__value"><?php echo e($totalBerkas); ?></b><p class="metric__text">Semua berkas tercatat</p></article>
        <article class="metric metric--warn"><div class="metric__top"><span class="metric__label">Diproses</span><span class="metric__icon"><i class="bi bi-arrow-repeat"></i></span></div><b class="metric__value"><?php echo e($berkasDiproses); ?></b><p class="metric__text">Masih dalam pengerjaan</p></article>
        <article class="metric metric--rose"><div class="metric__top"><span class="metric__label">SLA Terlambat</span><span class="metric__icon"><i class="bi bi-alarm"></i></span></div><b class="metric__value"><?php echo e($berkasTerlambat); ?></b><p class="metric__text">Perlu ditindaklanjuti</p></article>
    </section>

    <section class="dashboard-grid">
        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.dashboard-panel','data' => ['title' => 'Peringatan SLA','icon' => 'bi-bell-fill','link' => route('cs.berkas.index'),'linkText' => 'Buka berkas']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('ui.dashboard-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Peringatan SLA','icon' => 'bi-bell-fill','link' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('cs.berkas.index')),'link-text' => 'Buka berkas']); ?>
            <div class="sla-summary"><div class="sla-stat"><strong><?php echo e($slaHampir); ?></strong><span>Hampir jatuh tempo</span></div><div class="sla-stat sla-stat--urgent"><strong><?php echo e($berkasTerlambat); ?></strong><span>Sudah melewati SLA</span></div></div>
            <div class="sla-list">
                <?php $__empty_1 = true; $__currentLoopData = $slaList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $berkas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php ($days = now()->startOfDay()->diffInDays($berkas->estimasi_selesai, false)); ?>
                    <div class="sla-item"><div><b class="sla-item__name"><?php echo e($berkas->nasabah?->nama_nasabah ?? '-'); ?></b><span class="sla-item__sub"><?php echo e($berkas->jenis_layanan); ?> · <?php echo e(\Carbon\Carbon::parse($berkas->estimasi_selesai)->format('d M Y')); ?></span></div><span class="sla-item__status <?php echo e($days >= 0 ? 'is-soon' : ''); ?>"><?php echo e($days < 0 ? 'Terlambat' : ($days === 0 ? 'Hari ini' : $days . ' hari')); ?></span></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-muted small">Tidak ada berkas yang mendekati batas SLA.</p>
                <?php endif; ?>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.dashboard-panel','data' => ['title' => 'Status Berkas','icon' => 'bi-pie-chart-fill']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('ui.dashboard-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Status Berkas','icon' => 'bi-pie-chart-fill']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.dashboard-panel','data' => ['title' => 'Transaksi Terbaru','icon' => 'bi-receipt-cutoff','link' => route('cs.transaksi.index'),'linkText' => 'Kelola transaksi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('ui.dashboard-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Transaksi Terbaru','icon' => 'bi-receipt-cutoff','link' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('cs.transaksi.index')),'link-text' => 'Kelola transaksi']); ?>
            <div class="info-box"><i class="bi bi-wallet2"></i><span>Total nominal: <b>Rp <?php echo e(number_format($totalTransaksi, 0, ',', '.')); ?></b></span></div>
            <div class="sla-list">
                <?php $__empty_1 = true; $__currentLoopData = $latestTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="sla-item"><div><b class="sla-item__name"><?php echo e($trx->kategori ?? 'Transaksi'); ?> · <?php echo e($trx->berkas?->nasabah?->nama_nasabah ?? '-'); ?></b><span class="sla-item__sub"><?php echo e($trx->jenis_transaksi); ?> · <?php echo e(\Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d M Y')); ?></span></div><b>Rp <?php echo e(number_format($trx->nominal,0,',','.')); ?></b></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-muted small">Belum ada transaksi yang dicatat.</p>
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

<?php echo $__env->make('layouts.cs', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\punya_taupik\SEMESTER 6\BANK TA\bank_TA\resources\views/cs/dashboard/index.blade.php ENDPATH**/ ?>