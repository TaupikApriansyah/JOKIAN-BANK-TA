@php($isEdit = !empty($account))
<div class="modal-form-grid">
    <label class="form-group"><span class="form-label">Kode Akun</span><input class="form-control" name="kode_akun" value="{{ old('kode_akun', $account->kode_akun ?? '') }}" placeholder="Contoh: 111" required></label>
    <label class="form-group"><span class="form-label">Nama Akun</span><input class="form-control" name="nama_akun" value="{{ old('nama_akun', $account->nama_akun ?? '') }}" placeholder="Contoh: Kas" required></label>
    <label class="form-group"><span class="form-label">Kelompok</span><select class="form-select" name="kelompok" required>@foreach(['Aset', 'Pendapatan', 'Beban'] as $item)<option value="{{ $item }}" @selected(old('kelompok', $account->kelompok ?? 'Aset') === $item)>{{ $item }}</option>@endforeach</select></label>
    <label class="form-group"><span class="form-label">Saldo Normal</span><select class="form-select" name="saldo_normal" required>@foreach(['Debit', 'Kredit'] as $item)<option value="{{ $item }}" @selected(old('saldo_normal', $account->saldo_normal ?? 'Debit') === $item)>{{ $item }}</option>@endforeach</select></label>
    <label class="form-group span-2"><span class="form-label">Status</span><select class="form-select" name="status" required>@foreach(['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'] as $value => $label)<option value="{{ $value }}" @selected(old('status', $account->status ?? 'aktif') === $value)>{{ $label }}</option>@endforeach</select></label>
</div>
