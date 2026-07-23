<?php $__env->startSection('title', 'Dashboard Akuntansi'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <section class="dashboard-hero dashboard-hero--accounting">
        <h1 class="dashboard-hero__title"><i class="bi bi-calculator"></i>Dashboard Akuntansi</h1>
        <p class="dashboard-hero__copy">Verifikasi transaksi layanan yang dibuat CS, lalu sistem membentuk jurnal umum secara otomatis.</p>
        <span class="dashboard-hero__meta"><i class="bi bi-calendar3"></i><?php echo e(now()->translatedFormat('l, d F Y')); ?></span>
    </section>

    <section class="metric-grid">
        <article class="metric metric--warn"><div class="metric__top"><span class="metric__label">Perlu Verifikasi</span><span class="metric__icon"><i class="bi bi-patch-question"></i></span></div><b class="metric__value"><?php echo e($pendingCount); ?></b><p class="metric__text">Transaksi dari CS</p></article>
        <article class="metric"><div class="metric__top"><span class="metric__label">Diposting Hari Ini</span><span class="metric__icon"><i class="bi bi-journal-check"></i></span></div><b class="metric__value"><?php echo e($postedToday); ?></b><p class="metric__text">Jurnal berhasil dibentuk</p></article>
        <article class="metric"><div class="metric__top"><span class="metric__label">Pemasukan Bulan Ini</span><span class="metric__icon"><i class="bi bi-arrow-down-circle"></i></span></div><b class="metric__value metric__money">Rp <?php echo e(number_format($pemasukanBulan, 0, ',', '.')); ?></b><p class="metric__text">Transaksi sudah diposting</p></article>
        <article class="metric metric--rose"><div class="metric__top"><span class="metric__label">Pengeluaran Bulan Ini</span><span class="metric__icon"><i class="bi bi-arrow-up-circle"></i></span></div><b class="metric__value metric__money">Rp <?php echo e(number_format($pengeluaranBulan, 0, ',', '.')); ?></b><p class="metric__text">Transaksi sudah diposting</p></article>
        <article class="metric"><div class="metric__top"><span class="metric__label">Saldo Petty Cash</span><span class="metric__icon"><i class="bi bi-wallet2"></i></span></div><b class="metric__value metric__money">Rp <?php echo e(number_format($saldoKasKecil, 0, ',', '.')); ?></b><p class="metric__text"><a href="<?php echo e(route('akuntan.kas-kecil.index')); ?>">Kelola kas kecil</a></p></article>
    </section>

    <section class="accounting-grid">
        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.dashboard-panel','data' => ['title' => 'Transaksi Menunggu Verifikasi','icon' => 'bi-patch-check','link' => route('akuntan.transaksi.index'),'linkText' => 'Buka verifikasi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('ui.dashboard-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Transaksi Menunggu Verifikasi','icon' => 'bi-patch-check','link' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('akuntan.transaksi.index')),'link-text' => 'Buka verifikasi']); ?>
            <div class="sla-list">
                <?php $__empty_1 = true; $__currentLoopData = $pendingTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="sla-item">
                        <div>
                            <b class="sla-item__name"><?php echo e($transaction->berkas?->nasabah?->nama_nasabah ?? '-'); ?></b>
                            <span class="sla-item__sub"><?php echo e($transaction->kategori); ?> · <?php echo e(optional($transaction->tanggal_transaksi)->format('d M Y')); ?></span>
                        </div>
                        <b>Rp <?php echo e(number_format($transaction->nominal, 0, ',', '.')); ?></b>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-muted small">Belum ada transaksi yang perlu diverifikasi.</p>
                <?php endif; ?>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.dashboard-panel','data' => ['title' => 'Grafik Pemasukan dan Pengeluaran','icon' => 'bi-bar-chart-line']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('ui.dashboard-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Grafik Pemasukan dan Pengeluaran','icon' => 'bi-bar-chart-line']); ?>
            <div class="simple-bar-chart">
                <?php $__currentLoopData = $chart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $incomeHeight = max(4, round(($item['income'] / $chartMax) * 100));
                        $expenseHeight = max(4, round(($item['expense'] / $chartMax) * 100));
                    ?>
                    <div class="simple-bar-chart__item" title="<?php echo e($item['label']); ?> · Pemasukan Rp <?php echo e(number_format($item['income'], 0, ',', '.')); ?> · Pengeluaran Rp <?php echo e(number_format($item['expense'], 0, ',', '.')); ?>">
                        <div class="simple-bar-chart__bars"><i class="simple-bar-chart__bar simple-bar-chart__bar--income" style="height: <?php echo e($incomeHeight); ?>%"></i><i class="simple-bar-chart__bar simple-bar-chart__bar--expense" style="height: <?php echo e($expenseHeight); ?>%"></i></div>
                        <span><?php echo e($item['label']); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="chart-key"><span><i class="chart-key__dot chart-key__dot--income"></i>Pemasukan</span><span><i class="chart-key__dot chart-key__dot--expense"></i>Pengeluaran</span></div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.dashboard-panel','data' => ['title' => 'Jurnal Terakhir','icon' => 'bi-journal-text','link' => route('akuntan.jurnal.index'),'linkText' => 'Lihat jurnal']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('ui.dashboard-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Jurnal Terakhir','icon' => 'bi-journal-text','link' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('akuntan.jurnal.index')),'link-text' => 'Lihat jurnal']); ?>
            <div class="sla-list">
                <?php $__empty_1 = true; $__currentLoopData = $latestJournals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jurnal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="sla-item">
                        <div>
                            <b class="sla-item__name"><?php echo e($jurnal->nomor_jurnal); ?></b>
                            <span class="sla-item__sub"><?php echo e($jurnal->transaksi?->berkas?->nasabah?->nama_nasabah ?? '-'); ?> · <?php echo e(optional($jurnal->tanggal_jurnal)->format('d M Y')); ?></span>
                        </div>
                        <span class="status-badge status-diposting">Diposting</span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-muted small">Belum ada jurnal yang dibuat.</p>
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

<?php echo $__env->make('layouts.akuntan', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\punya_taupik\SEMESTER 6\BANK TA\bank_TA\resources\views/akuntan/dashboard/index.blade.php ENDPATH**/ ?>