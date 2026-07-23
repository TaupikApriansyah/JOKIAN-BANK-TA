<?php $__env->startSection('title', 'Daftar Akun'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <header class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-journal-bookmark"></i>Daftar Akun</h1>
            <p class="page-subtitle">Master akun sederhana untuk jurnal transaksi layanan. Akun yang sudah ada tetap dapat dipakai sebagai pilihan posting.</p>
        </div>
        <button type="button" class="btn-add" data-modal-open="akun-create-modal"><i class="bi bi-plus-circle"></i>Tambah Akun</button>
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

    <section class="table-wrapper">
        <table class="table table-hover">
            <thead><tr><th>Kode</th><th>Nama Akun</th><th>Kelompok</th><th>Saldo Normal</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><span class="account-code"><?php echo e($account->kode_akun); ?></span></td>
                    <td class="fw-semibold"><?php echo e($account->nama_akun); ?></td>
                    <td><?php echo e($account->kelompok); ?></td>
                    <td><?php echo e($account->saldo_normal); ?></td>
                    <td><span class="status-badge status-<?php echo e($account->status); ?>"><?php echo e(ucfirst($account->status)); ?></span></td>
                    <td><button type="button" class="btn-action btn-edit" data-modal-open="akun-edit-<?php echo e($account->id); ?>"><i class="bi bi-pencil"></i>Edit</button></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada daftar akun.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </section>
</div>

<?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.modal','data' => ['id' => 'akun-create-modal','title' => 'Tambah Akun','icon' => 'bi-plus-circle']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('ui.modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'akun-create-modal','title' => 'Tambah Akun','icon' => 'bi-plus-circle']); ?>
    <form method="POST" action="<?php echo e(route('akuntan.akun.store')); ?>"><?php echo csrf_field(); ?>
        <?php echo $__env->make('akuntan.akun.form', ['account' => null], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div class="form-actions"><button class="btn-save"><i class="bi bi-save"></i>Simpan Akun</button><button type="button" class="btn-back" data-modal-close>Batal</button></div>
    </form>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

<?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.modal','data' => ['id' => 'akun-edit-'.e($account->id).'','title' => 'Edit Akun','icon' => 'bi-pencil-square']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('ui.modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'akun-edit-'.e($account->id).'','title' => 'Edit Akun','icon' => 'bi-pencil-square']); ?>
        <form method="POST" action="<?php echo e(route('akuntan.akun.update', $account->id)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <?php echo $__env->make('akuntan.akun.form', ['account' => $account], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="form-actions"><button class="btn-save"><i class="bi bi-save"></i>Update Akun</button><button type="button" class="btn-back" data-modal-close>Batal</button></div>
        </form>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.akuntan', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\punya_taupik\SEMESTER 6\BANK TA\bank_TA\resources\views/akuntan/akun/index.blade.php ENDPATH**/ ?>