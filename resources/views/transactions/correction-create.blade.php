@extends('layouts.app', ['pageTitle' => 'Ajukan Koreksi Transaksi'])

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-6"><p class="font-mono text-xs font-semibold text-brand-700">{{ $transaction->transaction_number }}</p><h1 class="text-2xl font-bold">Ajukan Koreksi Transaksi</h1><p class="mt-1 text-sm text-slate-500">Transaksi yang telah disetujui tidak dihapus. Admin akan memverifikasi koreksi, sistem membuat jurnal pembalik, lalu draft transaksi pengganti.</p></div>
    <div class="mb-5 rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-900"><p class="font-semibold">Data transaksi lama</p><p class="mt-1">{{ $transaction->category }} · Rp{{ number_format($transaction->amount, 0, ',', '.') }} · {{ $transaction->payment_method }}</p></div>
    <form method="POST" action="{{ route('transactions.corrections.store', $transaction) }}" enctype="multipart/form-data" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">@csrf
        <div class="grid gap-5 md:grid-cols-2">
            <label class="text-sm font-medium">Kategori Baru<select name="proposed_category" class="mt-1.5 w-full rounded-lg border-slate-300">@foreach(array_keys(config('bank.transaction_categories')) as $category)<option value="{{ $category }}" @selected(old('proposed_category', $transaction->category) === $category)>{{ $category }}</option>@endforeach</select></label>
            <label class="text-sm font-medium">Metode Pembayaran<select name="proposed_payment_method" class="mt-1.5 w-full rounded-lg border-slate-300"><option @selected(old('proposed_payment_method', $transaction->payment_method) === 'Setoran Tunai')>Setoran Tunai</option><option @selected(old('proposed_payment_method', $transaction->payment_method) === 'Potong Saldo Rekening (Auto-debit)')>Potong Saldo Rekening (Auto-debit)</option></select></label>
            <label class="text-sm font-medium">Nominal Baru (Rp)<input name="proposed_amount" type="number" min="1" value="{{ old('proposed_amount', $transaction->amount) }}" class="mt-1.5 w-full rounded-lg border-slate-300"></label>
            <div class="text-sm font-medium">Dokumen Pendukung (opsional)<x-file-upload name="supporting_document" id="correction-supporting-document" label="Pilih dokumen pendukung" /></div>
            <label class="text-sm font-medium md:col-span-2">Keterangan Baru<input name="proposed_description" value="{{ old('proposed_description', $transaction->description) }}" class="mt-1.5 w-full rounded-lg border-slate-300"></label>
            <label class="text-sm font-medium md:col-span-2">Alasan Koreksi<textarea name="reason" rows="4" required class="mt-1.5 w-full rounded-lg border-slate-300" placeholder="Contoh: nominal salah input karena biaya layanan tidak sesuai tarif.">{{ old('reason') }}</textarea></label>
        </div>
        <div class="mt-6 flex justify-end gap-3"><a href="{{ route('cases.show', $transaction->serviceCase) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold">Batal</a><button class="rounded-lg bg-yellow-600 px-4 py-2 text-sm font-semibold text-white">Ajukan Koreksi ke Admin</button></div>
    </form>
</div>
@endsection
