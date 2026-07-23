@php
    $isEdit = !empty($transaction);
    $selectedType = old('jenis', $transaction->jenis ?? 'Keluar');
@endphp
<div class="modal-form-grid">
    <label class="form-group"><span class="form-label">Tanggal</span><input class="form-control" type="date" name="tanggal" value="{{ old('tanggal', $isEdit ? optional($transaction->tanggal)->format('Y-m-d') : date('Y-m-d')) }}" required></label>
    <label class="form-group"><span class="form-label">Jenis</span><select class="form-select" name="jenis" required><option value="Masuk" @selected($selectedType === 'Masuk')>Dana Masuk</option><option value="Keluar" @selected($selectedType === 'Keluar')>Dana Keluar</option></select></label>
    <label class="form-group"><span class="form-label">Kategori</span><select class="form-select" name="kategori" required><option value="">Pilih kategori</option>@foreach($categories as $category)<option value="{{ $category }}" @selected(old('kategori', $transaction->kategori ?? '') === $category)>{{ $category }}</option>@endforeach</select></label>
    <label class="form-group"><span class="form-label">Nominal</span><input class="form-control" type="number" min="1" step="1" name="nominal" value="{{ old('nominal', $transaction->nominal ?? '') }}" placeholder="0" required></label>
    <label class="form-group span-2"><span class="form-label">Keterangan</span><input class="form-control" name="keterangan" value="{{ old('keterangan', $transaction->keterangan ?? '') }}" placeholder="Contoh: Pembelian kertas dan tinta printer" required></label>
    <label class="form-group span-2"><span class="form-label">Nomor Bukti</span><input class="form-control" name="nomor_bukti" value="{{ old('nomor_bukti', $transaction->nomor_bukti ?? '') }}" placeholder="Opsional, misalnya KK-2026-001"></label>
</div>
<div class="mini-note"><i class="bi bi-info-circle"></i>Dana keluar tidak dapat melebihi saldo kas kecil yang tersedia.</div>
