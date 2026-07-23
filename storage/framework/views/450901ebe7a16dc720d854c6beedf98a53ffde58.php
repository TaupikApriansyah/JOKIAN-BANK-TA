<?php $__env->startSection('title', 'Verifikasi Transaksi'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <header class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-patch-check"></i>Verifikasi Transaksi</h1>
            <p class="page-subtitle">Periksa data transaksi dari CS. Saat diposting, jurnal debit dan kredit dibuat otomatis.</p>
        </div>
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

    <?php if($accounts->count() < 2): ?>
        <section class="warning-box"><i class="bi bi-exclamation-triangle"></i><span>Minimal dua akun aktif diperlukan untuk memposting transaksi. Tambahkan akun dari menu Daftar Akun.</span></section>
    <?php endif; ?>

    <section class="search-box">
        <form class="search-form" method="GET" action="<?php echo e(route('akuntan.transaksi.index')); ?>">
            <div class="search-wrapper"><i class="bi bi-search search-icon"></i><input class="search-input" name="search" value="<?php echo e($search); ?>" placeholder="Cari nasabah, kategori, atau transaksi"><button class="btn-search"><i class="bi bi-search"></i>Cari</button></div>
            <select class="form-select" name="status"><?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($item); ?>" <?php if($status === $item): echo 'selected'; endif; ?>><?php echo e($item === 'Semua' ? 'Semua status' : $item); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
            <a class="btn-back" href="<?php echo e(route('akuntan.transaksi.index')); ?>">Reset</a>
        </form>
    </section>

    <section class="table-wrapper">
        <table class="table table-hover">
            <thead><tr><th>No</th><th>Nasabah / Berkas</th><th>Transaksi</th><th>Bukti</th><th>Nominal</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><span class="row-number"><?php echo e($loop->iteration); ?></span></td>
                    <td><b><?php echo e($transaction->berkas?->nasabah?->nama_nasabah ?? '-'); ?></b><small class="d-block text-muted"><?php echo e($transaction->berkas?->jenis_layanan ?? '-'); ?></small></td>
                    <td><span class="transaction-type transaction-type--<?php echo e(strtolower($transaction->arah_transaksi ?? 'pemasukan')); ?>"><?php echo e($transaction->arah_transaksi); ?></span><small class="d-block text-muted"><?php echo e($transaction->kategori); ?> · <?php echo e($transaction->jenis_transaksi); ?></small></td>
                    <td><?php if($transaction->bukti_pembayaran): ?><a class="file-link" href="<?php echo e(route('dokumen.bukti.download', $transaction->id)); ?>"><i class="bi bi-file-earmark-check"></i>Unduh bukti</a><?php else: ?><span class="text-muted small">Belum diunggah</span><?php endif; ?></td>
                    <td class="fw-bold">Rp <?php echo e(number_format($transaction->nominal, 0, ',', '.')); ?></td>
                    <td><span class="status-badge status-<?php echo e(\Illuminate\Support\Str::slug($transaction->status_transaksi)); ?>"><?php echo e($transaction->status_transaksi); ?></span><?php if($transaction->catatan_verifikasi): ?><small class="d-block text-muted"><?php echo e(\Illuminate\Support\Str::limit($transaction->catatan_verifikasi, 42)); ?></small><?php endif; ?></td>
                    <td>
                        <?php if(in_array($transaction->status_transaksi, ['Menunggu Verifikasi', 'Lunas'])): ?>
                            <div class="action-buttons">
                                <button type="button" class="btn-action btn-save" data-modal-open="post-transaksi-<?php echo e($transaction->id); ?>"><i class="bi bi-journal-plus"></i>Posting</button>
                                <button type="button" class="btn-action btn-delete" data-modal-open="reject-transaksi-<?php echo e($transaction->id); ?>"><i class="bi bi-x-circle"></i>Tolak</button>
                            </div>
                        <?php elseif($transaction->jurnal): ?>
                            <span class="text-muted small"><i class="bi bi-journal-check"></i><?php echo e($transaction->jurnal->nomor_jurnal); ?></span>
                        <?php else: ?>
                            <span class="text-muted small">Tidak ada aksi</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="text-center py-5 text-muted">Tidak ada transaksi pada filter ini.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </section>
</div>

<?php $__currentLoopData = $transactions->whereIn('status_transaksi', ['Menunggu Verifikasi', 'Lunas']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $cash = $accounts->firstWhere('kode_akun', '111');
        $adminIncome = $accounts->firstWhere('kode_akun', '411');
        $serviceIncome = $accounts->firstWhere('kode_akun', '412');
        $atkExpense = $accounts->firstWhere('kode_akun', '512');
        $transportExpense = $accounts->firstWhere('kode_akun', '513');
        $operationalExpense = $accounts->firstWhere('kode_akun', '511');
        $isIncome = $transaction->arah_transaksi === 'Pemasukan';
        $defaultDebit = $isIncome ? optional($cash)->id : optional($transaction->kategori === 'ATK dan Cetak' ? $atkExpense : ($transaction->kategori === 'Transportasi' ? $transportExpense : $operationalExpense))->id;
        $defaultCredit = $isIncome ? optional($transaction->kategori === 'Biaya Layanan' ? $serviceIncome : $adminIncome)->id : optional($cash)->id;
    ?>
    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.modal','data' => ['id' => 'post-transaksi-'.e($transaction->id).'','title' => 'Posting Jurnal','icon' => 'bi-journal-plus','size' => 'lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('ui.modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'post-transaksi-'.e($transaction->id).'','title' => 'Posting Jurnal','icon' => 'bi-journal-plus','size' => 'lg']); ?>
        <div class="posting-preview">
            <span><b><?php echo e($transaction->berkas?->nasabah?->nama_nasabah ?? '-'); ?></b><small><?php echo e($transaction->jenis_transaksi); ?> · <?php echo e($transaction->kategori); ?></small></span>
            <strong>Rp <?php echo e(number_format($transaction->nominal, 0, ',', '.')); ?></strong>
        </div>
        <form method="POST" action="<?php echo e(route('akuntan.transaksi.post', $transaction->id)); ?>"><?php echo csrf_field(); ?>
            <div class="modal-form-grid">
                <label class="form-group"><span class="form-label">Akun Debit</span><select class="form-select" name="akun_debit_id" required><option value="">Pilih akun debit</option><?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($account->id); ?>" <?php if($defaultDebit == $account->id): echo 'selected'; endif; ?>><?php echo e($account->kode_akun); ?> · <?php echo e($account->nama_akun); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></label>
                <label class="form-group"><span class="form-label">Akun Kredit</span><select class="form-select" name="akun_kredit_id" required><option value="">Pilih akun kredit</option><?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($account->id); ?>" <?php if($defaultCredit == $account->id): echo 'selected'; endif; ?>><?php echo e($account->kode_akun); ?> · <?php echo e($account->nama_akun); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></label>
                <label class="form-group span-2"><span class="form-label">Catatan Verifikasi</span><textarea class="form-control" name="catatan_verifikasi" rows="3" placeholder="Opsional. Contoh: bukti pembayaran sesuai."></textarea></label>
            </div>
            <div class="mini-note"><i class="bi bi-info-circle"></i>Sistem membuat satu jurnal umum dengan dua detail: debit dan kredit.</div>
            <div class="form-actions"><button class="btn-save" <?php echo e($accounts->count() < 2 ? 'disabled' : ''); ?>><i class="bi bi-check2-circle"></i>Verifikasi &amp; Posting</button><button type="button" class="btn-back" data-modal-close>Batal</button></div>
        </form>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.modal','data' => ['id' => 'reject-transaksi-'.e($transaction->id).'','title' => 'Tolak Transaksi','icon' => 'bi-x-circle']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('ui.modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'reject-transaksi-'.e($transaction->id).'','title' => 'Tolak Transaksi','icon' => 'bi-x-circle']); ?>
        <form method="POST" action="<?php echo e(route('akuntan.transaksi.reject', $transaction->id)); ?>"><?php echo csrf_field(); ?>
            <label class="form-group"><span class="form-label">Alasan Penolakan</span><textarea class="form-control" name="catatan_verifikasi" rows="4" placeholder="Jelaskan data yang perlu diperbaiki CS." required></textarea></label>
            <div class="form-actions"><button class="btn-delete"><i class="bi bi-send-x"></i>Tolak &amp; Kembalikan</button><button type="button" class="btn-back" data-modal-close>Batal</button></div>
        </form>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.akuntan', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\punya_taupik\SEMESTER 6\BANK TA\bank_TA\resources\views/akuntan/transaksi/index.blade.php ENDPATH**/ ?>