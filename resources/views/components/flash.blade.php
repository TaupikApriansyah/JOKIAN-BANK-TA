@if(session('success'))
    <div class="flash-message mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">✓ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="flash-message mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">! {{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="flash-message mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"><p class="font-semibold">Periksa kembali data berikut:</p><ul class="list-disc pl-5 mt-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif
