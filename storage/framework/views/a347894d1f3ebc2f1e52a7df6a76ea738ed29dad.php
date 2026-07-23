<?php if(session('success')): ?>
    <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i><span><?php echo e(session('success')); ?></span></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i><span><?php echo e(session('error')); ?></span></div>
<?php endif; ?>
<?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div>
            <b>Data belum tersimpan. Periksa bagian berikut:</b>
            <ul class="mb-0 mt-2">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH E:\punya_taupik\SEMESTER 6\BANK TA\bank_TA\resources\views/components/ui/flash.blade.php ENDPATH**/ ?>