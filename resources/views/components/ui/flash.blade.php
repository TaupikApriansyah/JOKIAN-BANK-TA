@if(session('success'))
    <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i><span>{{ session('success') }}</span></div>
@endif
@if(session('error'))
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i><span>{{ session('error') }}</span></div>
@endif
@if($errors->any())
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div>
            <b>Data belum tersimpan. Periksa bagian berikut:</b>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
