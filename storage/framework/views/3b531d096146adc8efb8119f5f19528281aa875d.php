<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['id', 'title', 'icon' => 'bi-pencil-square', 'size' => 'md']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['id', 'title', 'icon' => 'bi-pencil-square', 'size' => 'md']); ?>
<?php foreach (array_filter((['id', 'title', 'icon' => 'bi-pencil-square', 'size' => 'md']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<section id="<?php echo e($id); ?>" class="app-modal" role="dialog" aria-modal="true" aria-labelledby="<?php echo e($id); ?>-title" data-modal>
    <button type="button" class="app-modal__backdrop" aria-label="Tutup pop-up" data-modal-close></button>
    <div class="app-modal__dialog <?php echo e($size === 'lg' ? 'app-modal__dialog--lg' : ''); ?>">
        <header class="app-modal__head">
            <h2 id="<?php echo e($id); ?>-title" class="app-modal__title"><i class="bi <?php echo e($icon); ?>"></i><?php echo e($title); ?></h2>
            <button type="button" class="app-modal__close" aria-label="Tutup pop-up" data-modal-close><i class="bi bi-x-lg"></i></button>
        </header>
        <div class="app-modal__body"><?php echo e($slot); ?></div>
    </div>
</section>
<?php /**PATH E:\punya_taupik\SEMESTER 6\BANK TA\bank_TA\resources\views/components/ui/modal.blade.php ENDPATH**/ ?>