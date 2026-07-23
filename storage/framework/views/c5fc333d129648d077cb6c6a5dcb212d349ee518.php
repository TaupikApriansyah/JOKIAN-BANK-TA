<?php
    $isEdit = !empty($transaction);
    $selectedType = old('jenis', $transaction->jenis ?? 'Keluar');
?>
<div class="modal-form-grid">
    <label class="form-group"><span class="form-label">Tanggal</span><input class="form-control" type="date" name="tanggal" value="<?php echo e(old('tanggal', $isEdit ? optional($transaction->tanggal)->format('Y-m-d') : date('Y-m-d'))); ?>" required></label>
    <label class="form-group"><span class="form-label">Jenis</span><select class="form-select" name="jenis" required><option value="Masuk" <?php if($selectedType === 'Masuk'): echo 'selected'; endif; ?>>Dana Masuk</option><option value="Keluar" <?php if($selectedType === 'Keluar'): echo 'selected'; endif; ?>>Dana Keluar</option></select></label>
    <label class="form-group"><span class="form-label">Kategori</span><select class="form-select" name="kategori" required><option value="">Pilih kategori</option><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($category); ?>" <?php if(old('kategori', $transaction->kategori ?? '') === $category): echo 'selected'; endif; ?>><?php echo e($category); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></label>
    <label class="form-group"><span class="form-label">Nominal</span><input class="form-control" type="number" min="1" step="1" name="nominal" value="<?php echo e(old('nominal', $transaction->nominal ?? '')); ?>" placeholder="0" required></label>
    <label class="form-group span-2"><span class="form-label">Keterangan</span><input class="form-control" name="keterangan" value="<?php echo e(old('keterangan', $transaction->keterangan ?? '')); ?>" placeholder="Contoh: Pembelian kertas dan tinta printer" required></label>
    <label class="form-group span-2"><span class="form-label">Nomor Bukti</span><input class="form-control" name="nomor_bukti" value="<?php echo e(old('nomor_bukti', $transaction->nomor_bukti ?? '')); ?>" placeholder="Opsional, misalnya KK-2026-001"></label>
</div>
<div class="mini-note"><i class="bi bi-info-circle"></i>Dana keluar tidak dapat melebihi saldo kas kecil yang tersedia.</div>
<?php /**PATH E:\punya_taupik\SEMESTER 6\BANK TA\bank_TA\resources\views/akuntan/kas_kecil/partials/form.blade.php ENDPATH**/ ?>