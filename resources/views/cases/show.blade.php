@extends('layouts.app', ['pageTitle' => 'Workspace Berkas'])

@section('content')
@php($isMaker = auth()->user()->isCustomerService() && $serviceCase->assigned_to === auth()->id())
<div class="mb-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <p class="font-mono text-xs font-semibold text-brand-700">{{ $serviceCase->file_number }}</p>
            <div class="mt-1 flex flex-wrap items-center gap-2">
                <h1 class="text-2xl font-bold">{{ $serviceCase->customer->name }}</h1>
                <span class="status-chip bg-blue-100 text-blue-700">{{ $serviceCase->status->label() }}</span>
            </div>
            <p class="mt-2 text-sm text-slate-500">{{ $serviceCase->serviceType->name }} · CS Penanggung Jawab: {{ $serviceCase->assignedTo->name }}</p>
        </div>
        <div class="rounded-lg border px-4 py-3 {{ $serviceCase->sla_status->value === 'terlambat' ? 'border-red-200 bg-red-50 text-red-800' : 'border-yellow-200 bg-yellow-50 text-yellow-800' }}">
            <p class="text-xs font-semibold uppercase">Batas SLA</p>
            <p class="mt-1 font-bold">{{ $serviceCase->due_at->format('d M Y, H:i') }}</p>
            <div class="mt-1">@include('partials.sla-badge', ['serviceCase' => $serviceCase])</div>
        </div>
    </div>
</div>

<div class="grid gap-6 xl:grid-cols-3">
    <section class="space-y-6 xl:col-span-2">
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-start justify-between border-b p-5">
                <div>
                    <h2 class="font-semibold">Kelengkapan Dokumen</h2>
                    <p class="mt-1 text-sm text-slate-500">Dokumen wajib menentukan apakah berkas dapat diproses dan ditutup.</p>
                </div>
                <span class="text-sm font-semibold {{ $serviceCase->hasAllRequiredDocuments() ? 'text-emerald-600' : 'text-yellow-600' }}">
                    {{ $serviceCase->hasAllRequiredDocuments() ? 'Lengkap' : count($serviceCase->missingDocuments()).' belum ada' }}
                </span>
            </div>
            <div class="p-5">
                <div class="space-y-2">
                    @foreach($serviceCase->serviceType->required_documents ?? [] as $requirement)
                        <div class="flex items-center justify-between rounded-lg border p-3">
                            <span class="text-sm">{{ $requirement }}</span>
                            @if($serviceCase->documents->contains('document_type', $requirement))
                                <span class="text-xs font-semibold text-emerald-700">✓ Sudah diunggah</span>
                            @else
                                <span class="text-xs font-semibold text-red-600">Belum ada</span>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if($isMaker && $serviceCase->status->value !== 'selesai')
                    <form method="POST" action="{{ route('cases.documents.store', $serviceCase) }}" enctype="multipart/form-data" class="mt-5 grid gap-3 rounded-lg bg-slate-50 p-4 md:grid-cols-3">
                        @csrf
                        <select name="document_type" class="rounded-lg border-slate-300 text-sm">
                            @foreach($serviceCase->missingDocuments() as $document)
                                <option>{{ $document }}</option>
                            @endforeach
                            @if($serviceCase->missingDocuments() === [])
                                <option>Dokumen Pendukung Tambahan</option>
                            @endif
                        </select>
                        <x-file-upload name="document" id="case-document" label="Pilih dokumen berkas" :required="true" />
                        <button class="rounded-lg bg-brand-800 px-4 py-2 text-sm font-semibold text-white">Upload Dokumen</button>
                    </form>
                @endif

                <div class="mt-4 divide-y">
                    @forelse($serviceCase->documents as $document)
                        <div class="flex items-center justify-between gap-3 py-3 text-sm">
                            <div>
                                <p class="font-medium">{{ $document->document_type }}</p>
                                <p class="text-xs text-slate-500">{{ $document->original_name }} · {{ $document->uploadedBy->name }}</p>
                            </div>
                            <x-download-button :href="route('documents.download', $document)" label="Unduh" :compact="true" />
                        </div>
                    @empty
                        <p class="py-3 text-sm text-slate-500">Belum ada dokumen diarsipkan.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b p-5">
                <h2 class="font-semibold">Transaksi Administrasi</h2>
                <p class="mt-1 text-sm text-slate-500">CS bertindak sebagai Maker. Jurnal hanya terbentuk setelah Admin menyetujui transaksi.</p>
            </div>
            <div class="p-5">
                @if($isMaker && $serviceCase->status->value !== 'selesai')
                    <form method="POST" action="{{ route('cases.transactions.store', $serviceCase) }}" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2">
                        @csrf
                        <label class="text-sm font-medium">Kategori
                            <select name="category" class="mt-1.5 w-full rounded-lg border-slate-300">
                                @foreach(array_keys(config('bank.transaction_categories')) as $category)
                                    <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="text-sm font-medium">Metode Pembayaran
                            <select name="payment_method" class="mt-1.5 w-full rounded-lg border-slate-300">
                                <option>Setoran Tunai</option>
                                <option>Potong Saldo Rekening (Auto-debit)</option>
                            </select>
                        </label>
                        <label class="text-sm font-medium">Nominal (Rp)
                            <input name="amount" type="number" min="1" value="{{ old('amount') }}" required class="mt-1.5 w-full rounded-lg border-slate-300">
                        </label>
                        <div class="text-sm font-medium">Bukti Pembayaran
                            <x-file-upload name="proof" id="transaction-proof" label="Pilih bukti pembayaran" />
                        </div>
                        <label class="text-sm font-medium md:col-span-2">Keterangan
                            <input name="description" value="{{ old('description') }}" class="mt-1.5 w-full rounded-lg border-slate-300">
                        </label>
                        <div class="flex justify-end gap-2 md:col-span-2">
                            <button name="action" value="draft" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold">Simpan Draft</button>
                            <button name="action" value="submit" class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white">Ajukan Verifikasi</button>
                        </div>
                    </form>
                @endif

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                            <tr><th class="px-3 py-2">No.</th><th class="px-3 py-2">Kategori</th><th class="px-3 py-2">Nominal</th><th class="px-3 py-2">Status</th><th class="px-3 py-2 text-right">Aksi</th></tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($serviceCase->transactions as $transaction)
                                <tr>
                                    <td class="px-3 py-3 font-mono text-xs">{{ $transaction->transaction_number }}</td>
                                    <td class="px-3 py-3">{{ $transaction->category }}</td>
                                    <td class="px-3 py-3">Rp{{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                    <td class="px-3 py-3">@include('partials.transaction-badge', ['transaction' => $transaction])</td>
                                    <td class="px-3 py-3 text-right">
                                        @if($isMaker && in_array($transaction->status->value, ['draft', 'dikembalikan']))
                                            <a href="{{ route('transactions.edit', $transaction) }}" class="mr-3 text-xs font-semibold text-slate-700">Ubah</a>
                                            <form class="inline" method="POST" action="{{ route('transactions.submit', $transaction) }}">@csrf<button class="text-xs font-semibold text-brand-700">Ajukan</button></form>
                                        @elseif($isMaker && $transaction->status->value === 'disetujui')
                                            <a href="{{ route('transactions.corrections.create', $transaction) }}" class="text-xs font-semibold text-yellow-700">Ajukan Koreksi</a>
                                        @else
                                            <span class="text-xs text-slate-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-3 py-5 text-center text-slate-500">Belum ada transaksi terkait berkas ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <aside class="space-y-5">
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="font-semibold">Aksi Berkas</h2>
            <div class="mt-4 space-y-2">
                @if($isMaker && $serviceCase->status->value !== 'selesai')
                    <form method="POST" action="{{ route('cases.process', $serviceCase) }}">@csrf
                        <button class="w-full rounded-lg border border-teal-200 bg-teal-50 px-4 py-2.5 text-left text-sm font-semibold text-teal-800">▶ Mulai / Lanjutkan Proses</button>
                    </form>
                    <form method="POST" action="{{ route('cases.complete', $serviceCase) }}">@csrf
                        <button class="w-full rounded-lg bg-brand-800 px-4 py-2.5 text-left text-sm font-semibold text-white">✓ Selesaikan & Tutup Berkas</button>
                    </form>
                @elseif($serviceCase->status->value === 'selesai')
                    <div class="rounded-lg bg-emerald-50 p-3 text-sm text-emerald-800">Berkas selesai pada {{ $serviceCase->completed_at?->format('d M Y H:i') }}.</div>
                @else
                    <div class="rounded-lg bg-slate-50 p-3 text-sm text-slate-600">Mode monitoring: Admin tidak dapat mengubah proses operasional berkas.</div>
                @endif
            </div>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="font-semibold">Informasi Nasabah</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div><dt class="text-slate-500">ID Nasabah</dt><dd class="font-medium">{{ $serviceCase->customer->customer_number }}</dd></div>
                <div><dt class="text-slate-500">NIK</dt><dd class="font-medium">{{ $serviceCase->customer->maskedNik() }}</dd></div>
                <div><dt class="text-slate-500">Rekening</dt><dd class="font-medium">{{ $serviceCase->customer->maskedAccountNumber() }}</dd></div>
            </dl>
        </section>
    </aside>
</div>
@endsection
