<?php if(session('success')): ?>
    <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i><?php echo e(session('error')); ?></div>
<?php endif; ?>
<?php if($errors->any()): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i>Data belum tersimpan. Cek kembali kolom yang wajib diisi.</div>
<?php endif; ?>
<?php /**PATH E:\punya_taupik\SEMESTER 6\BANK TA\bank_TA\resources\views/components/ui/flash.blade.php ENDPATH**/ ?>