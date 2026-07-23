<?php

namespace App\Http\Controllers\Akuntan;

use App\Http\Controllers\Controller;
use App\Models\KasKecil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KasKecilController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $jenis = $request->input('jenis');
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');

        $query = KasKecil::with('pembuat')
            ->when($search, function ($builder) use ($search) {
                $builder->where(function ($item) use ($search) {
                    $item->where('kategori', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%")
                        ->orWhere('nomor_bukti', 'like', "%{$search}%");
                });
            })
            ->when($jenis, fn ($builder) => $builder->where('jenis', $jenis))
            ->when($tanggalAwal, fn ($builder) => $builder->whereDate('tanggal', '>=', $tanggalAwal))
            ->when($tanggalAkhir, fn ($builder) => $builder->whereDate('tanggal', '<=', $tanggalAkhir));

        $transactions = (clone $query)
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();

        $totalMasuk = (float) KasKecil::where('jenis', 'Masuk')->sum('nominal');
        $totalKeluar = (float) KasKecil::where('jenis', 'Keluar')->sum('nominal');
        $saldo = $totalMasuk - $totalKeluar;

        $filteredMasuk = (float) (clone $query)->where('jenis', 'Masuk')->sum('nominal');
        $filteredKeluar = (float) (clone $query)->where('jenis', 'Keluar')->sum('nominal');

        $categories = $this->categories();

        return view('akuntan.kas_kecil.index', compact(
            'transactions',
            'totalMasuk',
            'totalKeluar',
            'saldo',
            'filteredMasuk',
            'filteredKeluar',
            'categories',
            'search',
            'jenis',
            'tanggalAwal',
            'tanggalAkhir'
        ));
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);
        $this->ensureSufficientBalance($validated['jenis'], (float) $validated['nominal']);
        $validated['created_by'] = Auth::id();

        DB::transaction(fn () => KasKecil::create($validated));

        return redirect()
            ->route('akuntan.kas-kecil.index')
            ->with('success', 'Transaksi kas kecil berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $transaction = KasKecil::findOrFail($id);
        $validated = $this->validateData($request);
        $this->ensureSufficientBalance(
            $validated['jenis'],
            (float) $validated['nominal'],
            $transaction
        );
        $validated['created_by'] = Auth::id();

        $transaction->update($validated);

        return redirect()
            ->route('akuntan.kas-kecil.index')
            ->with('success', 'Transaksi kas kecil berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $transaction = KasKecil::findOrFail($id);

        if ($transaction->jenis === 'Masuk') {
            $saldoSetelahDihapus = $this->currentBalance() - (float) $transaction->nominal;
            if ($saldoSetelahDihapus < 0) {
                return back()->with(
                    'error',
                    'Dana masuk ini tidak dapat dihapus karena akan membuat saldo kas kecil menjadi minus.'
                );
            }
        }

        $transaction->delete();

        return redirect()
            ->route('akuntan.kas-kecil.index')
            ->with('success', 'Transaksi kas kecil berhasil dihapus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'jenis' => ['required', 'in:Masuk,Keluar'],
            'kategori' => ['required', 'string', 'max:100'],
            'keterangan' => ['required', 'string', 'min:3', 'max:255'],
            'nominal' => ['required', 'numeric', 'min:1', 'max:999999999999999'],
            'nomor_bukti' => ['nullable', 'string', 'max:100'],
        ], [
            'tanggal.required' => 'Tanggal transaksi wajib diisi.',
            'tanggal.before_or_equal' => 'Tanggal transaksi tidak boleh melewati hari ini.',
            'jenis.required' => 'Jenis transaksi wajib dipilih.',
            'kategori.required' => 'Kategori wajib dipilih.',
            'keterangan.required' => 'Keterangan wajib diisi.',
            'keterangan.min' => 'Keterangan minimal 3 karakter.',
            'nominal.required' => 'Nominal wajib diisi.',
            'nominal.min' => 'Nominal minimal Rp1.',
        ]);
    }

    private function ensureSufficientBalance(string $jenis, float $nominal, ?KasKecil $current = null): void
    {
        if ($jenis !== 'Keluar') {
            return;
        }

        $available = $this->currentBalance();

        if ($current) {
            // Kembalikan dampak transaksi lama sebelum menghitung kemampuan saldo.
            $available += $current->jenis === 'Keluar'
                ? (float) $current->nominal
                : -(float) $current->nominal;
        }

        if ($nominal > $available) {
            throw ValidationException::withMessages([
                'nominal' => 'Saldo kas kecil tidak mencukupi. Saldo yang tersedia Rp ' . number_format(max(0, $available), 0, ',', '.') . '.',
            ]);
        }
    }

    private function currentBalance(): float
    {
        $masuk = (float) KasKecil::where('jenis', 'Masuk')->sum('nominal');
        $keluar = (float) KasKecil::where('jenis', 'Keluar')->sum('nominal');

        return $masuk - $keluar;
    }

    private function categories(): array
    {
        return [
            'Saldo Awal',
            'Pengisian Dana',
            'ATK dan Cetak',
            'Transportasi',
            'Konsumsi',
            'Biaya Kurir',
            'Perlengkapan Kantor',
            'Pemeliharaan Ringan',
            'Operasional Lainnya',
        ];
    }
}
