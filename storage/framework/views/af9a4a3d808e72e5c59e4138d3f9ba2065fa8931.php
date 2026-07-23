<?php ($count = $pendingVerificationCount ?? 0); ?>
<div class="app-notice" data-notice>
    <button type="button" class="app-icon-button" aria-label="Transaksi menunggu verifikasi" data-notice-toggle>
        <i class="bi bi-bell"></i><?php if($count): ?><span class="app-notice__count"><?php echo e($count > 9 ? '9+' : $count); ?></span><?php endif; ?>
    </button>
    <div class="app-notice__panel">
        <div class="app-notice__head">
            <span>Menunggu verifikasi <?php echo e($count ? '(' . $count . ')' : ''); ?></span>
            <button type="button" class="app-notice__close" aria-label="Tutup notifikasi" data-notice-close><i class="bi bi-x-lg"></i></button>
        </div>
        <?php $__empty_1 = true; $__currentLoopData = ($pendingVerificationTransactions ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="app-notice__item">
                <b><?php echo e($transaction->berkas?->nasabah?->nama_nasabah ?? 'Nasabah'); ?></b>
                <span><?php echo e($transaction->kategori); ?> · Rp <?php echo e(number_format($transaction->nominal, 0, ',', '.')); ?></span>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="app-notice__item"><span>Tidak ada transaksi yang menunggu verifikasi.</span></div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH E:\punya_taupik\SEMESTER 6\BANK TA\bank_TA\resources\views/components/ui/accounting-notice.blade.php ENDPATH**/ ?>