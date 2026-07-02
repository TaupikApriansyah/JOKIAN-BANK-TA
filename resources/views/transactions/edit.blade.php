@extends('layouts.app', ['pageTitle' => 'Ubah Draft Transaksi'])

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-6"><p class="font-mono text-xs font-semibold text-brand-700">{{ $transaction->transaction_number }}</p><h1 class="text-2xl font-bold">Ubah Draft Transaksi</h1><p class="mt-1 text-sm text-slate-500">Perubahan dicatat dalam audit trail. Setelah diperbarui, ajukan kembali untuk verifikasi Admin.</p></div>
    <form method="POST" action="{{ route('transactions.update', $transaction) }}" enctype="multipart/form-data" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf @method('PUT')
        <div class="grid gap-5 md:grid-cols-2">
            <label class="text-sm font-medium">Kategori<select name="category" class="mt-1.5 w-full rounded-lg border-slate-300">@foreach(array_keys(config('bank.transaction_categories')) as $category)<option value="{{ $category }}" @selected(old('category', $transaction->category) === $category)>{{ $category }}</option>@endforeach</select></label>
            <label class="text-sm font-medium">Metode Pembayaran<select name="payment_method" class="mt-1.5 w-full rounded-lg border-slate-300"><option @selected(old('payment_method', $transaction->payment_method) === 'Setoran Tunai')>Setoran Tunai</option><option @selected(old('payment_method', $transaction->payment_method) === 'Potong Saldo Rekening (Auto-debit)')>Potong Saldo Rekening (Auto-debit)</option></select></label>
            <label class="text-sm font-medium">Nominal (Rp)<input name="amount" type="number" min="1" value="{{ old('amount', $transaction->amount) }}" class="mt-1.5 w-full rounded-lg border-slate-300"></label>
            <div class="text-sm font-medium">Bukti Pembayaran Baru (opsional)<x-file-upload name="proof" id="edit-transaction-proof" label="Pilih bukti pembayaran baru" /></div>
            <label class="text-sm font-medium md:col-span-2">Keterangan<input name="description" value="{{ old('description', $transaction->description) }}" class="mt-1.5 w-full rounded-lg border-slate-300"></label>
        </div>
        <div class="mt-6 flex justify-end gap-3"><a href="{{ route('cases.show', $transaction->serviceCase) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold">Batal</a><button class="rounded-lg bg-brand-800 px-4 py-2 text-sm font-semibold text-white">Simpan Perubahan</button></div>
    </form>
</div>
@endsection
