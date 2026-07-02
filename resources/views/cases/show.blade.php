@extends('layouts.app', ['pageTitle' => 'Workspace Berkas'])

@section('content')
@php
  $isMaker = auth()->user()->isCustomerService() && $serviceCase->assigned_to === auth()->id();
  $isClosed = in_array($serviceCase->status->value, ['selesai', 'ditolak'], true);
  $canDeleteDraft = $isMaker && in_array($serviceCase->status->value, ['baru', 'menunggu_dokumen'], true) && $serviceCase->documents->isEmpty() && $serviceCase->transactions->isEmpty();
@endphp

<div class="mb-6 app-card p-5">
  <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
    <div>
      <p class="font-mono text-xs font-semibold text-brand-700">{{ $serviceCase->file_number }}</p>
      <div class="mt-1 flex flex-wrap items-center gap-2"><h1 class="text-2xl font-bold">{{ $serviceCase->customer->name }}</h1><span class="status-chip bg-blue-100 text-blue-700">{{ $serviceCase->status->label() }}</span></div>
      <p class="mt-2 text-sm text-slate-500">{{ $serviceCase->serviceType->name }} · CS Penanggung Jawab: {{ $serviceCase->assignedTo->name }}</p>
    </div>
    <div class="rounded-lg border px-4 py-3 {{ $serviceCase->sla_status->value === 'terlambat' ? 'border-red-200 bg-red-50 text-red-800' : 'border-yellow-200 bg-yellow-50 text-yellow-800' }}">
      <p class="text-xs font-semibold uppercase">Batas SLA</p><p class="mt-1 font-bold">{{ $serviceCase->due_at->format('d M Y, H:i') }}</p><div class="mt-1">@include('partials.sla-badge', ['serviceCase' => $serviceCase])</div>
    </div>
  </div>
</div>

<div class="grid gap-6 xl:grid-cols-3">
  <section class="space-y-6 xl:col-span-2">
    <section class="app-card">
      <div class="flex items-start justify-between border-b border-slate-100 p-5"><div><h2 class="font-semibold">Kelengkapan Dokumen</h2><p class="mt-1 text-sm text-slate-500">Dokumen wajib menentukan apakah berkas dapat diproses dan ditutup.</p></div><span class="text-sm font-semibold {{ $serviceCase->hasAllRequiredDocuments() ? 'text-emerald-600' : 'text-yellow-600' }}">{{ $serviceCase->hasAllRequiredDocuments() ? 'Lengkap' : count($serviceCase->missingDocuments()).' belum ada' }}</span></div>
      <div class="p-5">
        <div class="space-y-2">
          @forelse($serviceCase->serviceType->required_documents ?? [] as $requirement)
            <div class="flex items-center justify-between rounded-lg border border-slate-200 p-3"><span class="text-sm">{{ $requirement }}</span>@if($serviceCase->documents->contains('document_type', $requirement))<span class="text-xs font-semibold text-emerald-700">✓ Sudah diunggah</span>@else<span class="text-xs font-semibold text-red-600">Belum ada</span>@endif</div>
          @empty
            <div class="rounded-lg border border-dashed border-slate-200 p-3 text-sm text-slate-500">Tidak ada dokumen wajib khusus untuk jenis layanan ini.</div>
          @endforelse
        </div>

        @if($isMaker && !$isClosed)
          <form method="POST" action="{{ route('cases.documents.store', $serviceCase) }}" enctype="multipart/form-data" class="mt-5 grid gap-3 rounded-xl bg-blue-50/60 p-4 md:grid-cols-3" data-processing-overlay>
            @csrf
            <label class="text-sm font-semibold">Jenis Dokumen<select name="document_type" class="form-input mt-1" required>@foreach($serviceCase->missingDocuments() as $document)<option value="{{ $document }}">{{ $document }}</option>@endforeach<option value="Dokumen Pendukung Tambahan">Dokumen Pendukung Tambahan</option></select></label>
            <div class="md:col-span-1"><x-file-upload name="document" id="case-document" label="Pilih dokumen berkas" :required="true" /></div>
            <div class="flex items-end"><button class="animated-button w-full"><span>Upload Dokumen</span><span></span></button></div>
          </form>
        @endif

        <div class="mt-5 divide-y divide-blue-50">
          @forelse($serviceCase->documents as $document)
            <div class="flex flex-col gap-3 py-3 sm:flex-row sm:items-center sm:justify-between"><div><p class="font-medium">{{ $document->document_type }}</p><p class="text-xs text-slate-500">{{ $document->original_name }} · {{ $document->uploadedBy->name }}</p></div><div class="flex flex-wrap items-center gap-2"><x-download-button :href="route('documents.download', $document)" label="Unduh" />@if($isMaker && !$isClosed)<a href="{{ route('documents.edit', $document) }}" class="soft-button !py-2 !text-xs">Ubah</a><form method="POST" action="{{ route('documents.destroy', $document) }}" onsubmit="return confirm('Hapus dokumen ini? Dokumen wajib yang terhapus akan membuat berkas kembali belum lengkap.');" data-processing-overlay>@csrf @method('DELETE')<button class="soft-button !border-red-200 !py-2 !text-xs !text-red-600">Hapus</button></form>@endif</div></div>
          @empty
            <p class="py-3 text-sm text-slate-500">Belum ada dokumen diarsipkan.</p>
          @endforelse
        </div>
      </div>
    </section>

    <section class="app-card">
      <div class="border-b border-slate-100 p-5"><h2 class="font-semibold">Transaksi Administrasi</h2><p class="mt-1 text-sm text-slate-500">CS sebagai Maker membuat transaksi. Admin sebagai Checker menyetujui sebelum jurnal terbentuk.</p></div>
      <div class="p-5">
        @if($isMaker && !$isClosed)
          <form method="POST" action="{{ route('cases.transactions.store', $serviceCase) }}" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2" data-processing-overlay>
            @csrf
            <label class="text-sm font-semibold">Kategori<select name="category" class="form-input mt-1.5" required>@foreach(array_keys(config('bank.transaction_categories')) as $category)<option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>@endforeach</select></label>
            <label class="text-sm font-semibold">Metode Pembayaran<select name="payment_method" class="form-input mt-1.5" required><option>Setoran Tunai</option><option>Potong Saldo Rekening (Auto-debit)</option></select></label>
            <label class="text-sm font-semibold">Nominal (Rp)<input name="amount" type="number" min="1" value="{{ old('amount') }}" required class="form-input mt-1.5"></label>
            <div class="text-sm font-semibold">Bukti Pembayaran<x-file-upload name="proof" id="transaction-proof" label="Pilih bukti pembayaran" /></div>
            <label class="text-sm font-semibold md:col-span-2">Keterangan<input name="description" value="{{ old('description') }}" class="form-input mt-1.5" placeholder="Keterangan transaksi (opsional)"></label>
            <div class="flex flex-wrap justify-end gap-2 md:col-span-2"><button name="action" value="draft" class="soft-button">Simpan Draft</button><button name="action" value="submit" class="animated-button"><span>Ajukan Verifikasi</span><span></span></button></div>
          </form>
        @endif

        <div class="mt-6 overflow-x-auto"><table class="min-w-full text-sm"><thead class="bg-blue-50/60 text-left text-xs font-bold uppercase text-slate-500"><tr><th class="px-3 py-2">No.</th><th class="px-3 py-2">Kategori</th><th class="px-3 py-2">Nominal</th><th class="px-3 py-2">Status</th><th class="px-3 py-2 text-right">Aksi</th></tr></thead><tbody class="divide-y divide-blue-50">@forelse($serviceCase->transactions as $transaction)<tr><td class="px-3 py-3 font-mono text-xs"><a href="{{ route('transactions.show', $transaction) }}" class="font-bold text-brand-700 hover:underline">{{ $transaction->transaction_number }}</a></td><td class="px-3 py-3">{{ $transaction->category }}</td><td class="px-3 py-3">Rp{{ number_format($transaction->amount, 0, ',', '.') }}</td><td class="px-3 py-3">@include('partials.transaction-badge', ['transaction' => $transaction])</td><td class="px-3 py-3 text-right"><a href="{{ route('transactions.show', $transaction) }}" class="mr-2 text-xs font-semibold text-brand-700">Detail</a>@if($isMaker && in_array($transaction->status->value, ['draft', 'dikembalikan']))<a href="{{ route('transactions.edit', $transaction) }}" class="mr-2 text-xs font-semibold text-slate-700">Ubah</a><form class="inline" method="POST" action="{{ route('transactions.submit', $transaction) }}" data-processing-overlay>@csrf<button class="text-xs font-semibold text-brand-700">Ajukan</button></form>@elseif($isMaker && $transaction->status->value === 'disetujui')<a href="{{ route('transactions.corrections.create', $transaction) }}" class="text-xs font-semibold text-yellow-700">Koreksi</a>@endif</td></tr>@empty<tr><td colspan="5" class="px-3 py-5 text-center text-slate-500">Belum ada transaksi terkait berkas ini.</td></tr>@endforelse</tbody></table></div>
      </div>
    </section>
  </section>

  <aside class="space-y-5">
    <section class="app-card p-5"><h2 class="font-semibold">Aksi Berkas</h2><div class="mt-4 space-y-3">
      @if($isMaker && !$isClosed)
        @if(in_array($serviceCase->status->value, ['baru','menunggu_dokumen']))<a href="{{ route('cases.edit', $serviceCase) }}" class="soft-button w-full justify-center">Ubah Data Berkas</a>@endif
        <form method="POST" action="{{ route('cases.process', $serviceCase) }}" data-processing-overlay>@csrf<button class="soft-button w-full justify-start !border-emerald-200 !bg-emerald-50 !text-emerald-800">▶ Mulai / Lanjutkan Proses</button></form>
        <form method="POST" action="{{ route('cases.complete', $serviceCase) }}" data-processing-overlay>@csrf<button class="animated-button w-full"><span>✓ Selesaikan & Tutup Berkas</span><span></span></button></form>
        <form method="POST" action="{{ route('cases.reject', $serviceCase) }}" class="rounded-lg border border-red-100 bg-red-50 p-3" data-processing-overlay>@csrf<label class="block text-xs font-bold text-red-700">Alasan Penolakan<input name="reason" maxlength="500" required class="form-input mt-1 !min-h-9 !border-red-200" placeholder="Contoh: Dokumen tidak memenuhi ketentuan"></label><button class="mt-2 text-xs font-bold text-red-700">Tolak Berkas</button></form>
        @if($canDeleteDraft)<form method="POST" action="{{ route('cases.destroy', $serviceCase) }}" onsubmit="return confirm('Hapus draft berkas ini? Tindakan hanya dapat dilakukan sebelum ada dokumen dan transaksi.');" data-processing-overlay>@csrf @method('DELETE')<button class="soft-button w-full justify-center !border-red-200 !text-red-600">Hapus Draft Kosong</button></form>@endif
      @elseif($serviceCase->status->value === 'selesai')
        <div class="rounded-lg bg-emerald-50 p-3 text-sm text-emerald-800">Berkas selesai pada {{ $serviceCase->completed_at?->format('d M Y H:i') }}.</div>
      @elseif($serviceCase->status->value === 'ditolak')
        <div class="rounded-lg bg-red-50 p-3 text-sm text-red-800">Berkas ditolak. SLA sudah dihentikan.</div>
      @else
        <div class="rounded-lg bg-slate-50 p-3 text-sm text-slate-600">Mode monitoring: Admin tidak mengubah proses operasional berkas.</div>
      @endif
    </div></section>
    <section class="app-card p-5"><h2 class="font-semibold">Informasi Nasabah</h2><dl class="mt-4 space-y-3 text-sm"><div><dt class="text-slate-500">ID Nasabah</dt><dd class="font-medium"><a class="text-brand-700 hover:underline" href="{{ route('customers.show', $serviceCase->customer) }}">{{ $serviceCase->customer->customer_number }}</a></dd></div><div><dt class="text-slate-500">NIK</dt><dd class="font-medium">{{ $serviceCase->customer->maskedNik() }}</dd></div><div><dt class="text-slate-500">Rekening</dt><dd class="font-medium">{{ $serviceCase->customer->maskedAccountNumber() }}</dd></div></dl></section>
  </aside>
</div>
@endsection
