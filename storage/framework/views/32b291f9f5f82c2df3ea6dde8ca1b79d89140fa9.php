<?php $__env->startSection('title', 'Petty Cash'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <header class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-wallet2"></i>Petty Cash / Kas Kecil</h1>
            <p class="page-subtitle">Catat dana masuk, pengeluaran kecil, bukti transaksi, dan saldo kas kecil.</p>
        </div>
        <button class="btn-add" type="button" data-modal-open="kas-kecil-create-modal"><i class="bi bi-plus-circle"></i>Tambah Transaksi</button>
    </header>

    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.flash','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('ui.flash'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

    <section class="metric-grid">
        <article class="metric">
            <div class="metric__top"><span class="metric__label">Saldo Kas Kecil</span><span class="metric__icon"><i class="bi bi-wallet2"></i></span></div>
            <b class="metric__value metric__money">Rp <?php echo e(number_format($saldo, 0, ',', '.')); ?></b>
            <p class="metric__text">Saldo tersedia saat ini</p>
        </article>
        <article class="metric">
            <div class="metric__top"><span class="metric__label">Total Dana Masuk</span><span class="metric__icon"><i class="bi bi-arrow-down-circle"></i></span></div>
            <b class="metric__value metric__money">Rp <?php echo e(number_format($totalMasuk, 0, ',', '.')); ?></b>
            <p class="metric__text">Akumulasi seluruh pengisian</p>
        </article>
        <article class="metric metric--rose">
            <div class="metric__top"><span class="metric__label">Total Dana Keluar</span><span class="metric__icon"><i class="bi bi-arrow-up-circle"></i></span></div>
            <b class="metric__value metric__money">Rp <?php echo e(number_format($totalKeluar, 0, ',', '.')); ?></b>
            <p class="metric__text">Akumulasi seluruh pengeluaran</p>
        </article>
        <article class="metric metric--warn">
            <div class="metric__top"><span class="metric__label">Hasil Filter</span><span class="metric__icon"><i class="bi bi-funnel"></i></span></div>
            <b class="metric__value"><?php echo e($transactions->count()); ?></b>
            <p class="metric__text">Masuk Rp <?php echo e(number_format($filteredMasuk, 0, ',', '.')); ?> · Keluar Rp <?php echo e(number_format($filteredKeluar, 0, ',', '.')); ?></p>
        </article>
    </section>

    <section class="search-box">
        <form class="search-form" method="GET" action="<?php echo e(route('akuntan.kas-kecil.index')); ?>">
            <div class="search-wrapper"><i class="bi bi-search search-icon"></i><input class="search-input" name="search" value="<?php echo e($search); ?>" placeholder="Cari kategori, keterangan, atau nomor bukti"><button class="btn-search"><i class="bi bi-search"></i>Cari</button></div>
            <select class="form-select" name="jenis"><option value="">Semua jenis</option><option value="Masuk" <?php if($jenis === 'Masuk'): echo 'selected'; endif; ?>>Dana Masuk</option><option value="Keluar" <?php if($jenis === 'Keluar'): echo 'selected'; endif; ?>>Dana Keluar</option></select>
            <input class="form-control" type="date" name="tanggal_awal" value="<?php echo e($tanggalAwal); ?>" aria-label="Tanggal awal">
            <input class="form-control" type="date" name="tanggal_akhir" value="<?php echo e($tanggalAkhir); ?>" aria-label="Tanggal akhir">
            <button class="btn-search"><i class="bi bi-funnel"></i>Filter</button>
            <a class="btn-back" href="<?php echo e(route('akuntan.kas-kecil.index')); ?>">Reset</a>
        </form>
    </section>

    <section class="table-wrapper">
        <table class="table table-hover">
            <thead><tr><th>No</th><th>Tanggal</th><th>Jenis</th><th>Kategori / Keterangan</th><th>Nomor Bukti</th><th>Nominal</th><th>Petugas</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><span class="row-number"><?php echo e($loop->iteration); ?></span></td>
                    <td><?php echo e(optional($transaction->tanggal)->format('d M Y')); ?></td>
                    <td><span class="transaction-type transaction-type--<?php echo e($transaction->jenis === 'Masuk' ? 'pemasukan' : 'pengeluaran'); ?>"><?php echo e($transaction->jenis === 'Masuk' ? 'Dana Masuk' : 'Dana Keluar'); ?></span></td>
                    <td><b><?php echo e($transaction->kategori); ?></b><small class="d-block text-muted"><?php echo e($transaction->keterangan); ?></small></td>
                    <td><?php echo e($transaction->nomor_bukti ?: '-'); ?></td>
                    <td class="fw-bold"><?php echo e($transaction->nominal_rupiah); ?></td>
                    <td><?php echo e($transaction->pembuat?->name ?? '-'); ?></td>
                    <td><div class="action-buttons"><button class="btn-action btn-edit" type="button" data-modal-open="kas-kecil-edit-<?php echo e($transaction->id); ?>"><i class="bi bi-pencil"></i>Edit</button><form method="POST" action="<?php echo e(route('akuntan.kas-kecil.destroy', $transaction->id)); ?>" onsubmit="return confirm('Hapus transaksi kas kecil ini?')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn-action btn-delete"><i class="bi bi-trash"></i>Hapus</button></form></div></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="8" class="text-center py-5 text-muted">Belum ada transaksi kas kecil.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </section>
</div>

<?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.modal','data' => ['id' => 'kas-kecil-create-modal','title' => 'Tambah Transaksi Kas Kecil','icon' => 'bi-wallet2','size' => 'lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('ui.modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'kas-kecil-create-modal','title' => 'Tambah Transaksi Kas Kecil','icon' => 'bi-wallet2','size' => 'lg']); ?>
    <form method="POST" action="<?php echo e(route('akuntan.kas-kecil.store')); ?>"><?php echo csrf_field(); ?>
        <input type="hidden" name="_modal" value="kas-kecil-create-modal">
        <?php echo $__env->make('akuntan.kas_kecil.partials.form', ['transaction' => null], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div class="form-actions"><button class="btn-save"><i class="bi bi-save"></i>Simpan Transaksi</button><button type="button" class="btn-back" data-modal-close>Batal</button></div>
    </form>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

<?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.modal','data' => ['id' => 'kas-kecil-edit-'.e($transaction->id).'','title' => 'Edit Transaksi Kas Kecil','icon' => 'bi-pencil-square','size' => 'lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('ui.modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'kas-kecil-edit-'.e($transaction->id).'','title' => 'Edit Transaksi Kas Kecil','icon' => 'bi-pencil-square','size' => 'lg']); ?>
    <form method="POST" action="<?php echo e(route('akuntan.kas-kecil.update', $transaction->id)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <input type="hidden" name="_modal" value="kas-kecil-edit-<?php echo e($transaction->id); ?>">
        <?php echo $__env->make('akuntan.kas_kecil.partials.form', ['transaction' => $transaction], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div class="form-actions"><button class="btn-save"><i class="bi bi-save"></i>Update Transaksi</button><button type="button" class="btn-back" data-modal-close>Batal</button></div>
    </form>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.akuntan', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\punya_taupik\SEMESTER 6\BANK TA\bank_TA\resources\views/akuntan/kas_kecil/index.blade.php ENDPATH**/ ?>