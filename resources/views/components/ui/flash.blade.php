@if(session('success'))
    <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i>{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i>Data belum tersimpan. Cek kembali kolom yang wajib diisi.</div>
@endif
