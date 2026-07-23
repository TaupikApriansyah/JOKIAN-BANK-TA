<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['title', 'icon' => 'bi-grid', 'link' => null, 'linkText' => 'Lihat semua']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['title', 'icon' => 'bi-grid', 'link' => null, 'linkText' => 'Lihat semua']); ?>
<?php foreach (array_filter((['title', 'icon' => 'bi-grid', 'link' => null, 'linkText' => 'Lihat semua']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>
<section class="dashboard-panel">
    <header class="dashboard-panel__head">
        <h2 class="dashboard-panel__title"><i class="bi <?php echo e($icon); ?>"></i><?php echo e($title); ?></h2>
        <?php if($link): ?><a class="dashboard-panel__link" href="<?php echo e($link); ?>"><?php echo e($linkText); ?> <i class="bi bi-arrow-right"></i></a><?php endif; ?>
    </header>
    <div class="dashboard-panel__body"><?php echo e($slot); ?></div>
</section>
<?php /**PATH E:\punya_taupik\SEMESTER 6\BANK TA\bank_TA\resources\views/components/ui/dashboard-panel.blade.php ENDPATH**/ ?>