<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use App\Models\Berkas;
use App\Models\TransaksiAdministrasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TransaksiAdministrasiController extends Controller
{
    private function berkasSaya()
    {
        return Berkas::with('nasabah')
            ->where('user_id', Auth::id())
            ->orderByDesc('tanggal_masuk');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('kategori');
        $status = $request->input('status');

        $transaksis = TransaksiAdministrasi::with('berkas.nasabah')
            ->whereHas('berkas', fn ($query) => $query->where('user_id', Auth::id()))
            ->when($search, function ($query, $search) {
                $query->where(function ($item) use ($search) {
                    $item->where('jenis_transaksi', 'like', "%{$search}%")
                        ->orWhere('kategori', 'like', "%{$search}%")
                        ->orWhereHas('berkas.nasabah', fn ($nasabah) => $nasabah->where('nama_nasabah', 'like', "%{$search}%"));
                });
            })
            ->when($category, fn ($query) => $query->where('kategori', $category))
            ->when($status, fn ($query) => $query->where('status_transaksi', $status))
            ->orderByDesc('tanggal_transaksi')
            ->get();

        $totalNominal = $transaksis->sum('nominal');
        $berkasList = $this->berkasSaya()->get();
        $categories = $this->categories();
        $paymentStatuses = ['Belum Dibayar', 'Menunggu Verifikasi', 'Ditolak'];
        $paymentMethods = ['Tunai', 'Transfer Bank', 'QRIS', 'Lainnya'];
        $transactionDirections = ['Pemasukan', 'Pengeluaran'];

        return view('cs.transaksi.index', compact(
            'transaksis', 'totalNominal', 'berkasList', 'categories', 'paymentStatuses', 'paymentMethods', 'transactionDirections'
        ));
    }

    public function create()
    {
        $berkasList = $this->berkasSaya()->get();
        return view('cs.transaksi.create', compact('berkasList'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['bukti_pembayaran'] = $this->saveBukti($request);

        TransaksiAdministrasi::create($data);

        return redirect()->route('cs.transaksi.index')->with('success', 'Transaksi tersimpan. Kirimkan ke Akuntan melalui status Menunggu Verifikasi.');
    }

    public function update(Request $request, $id)
    {
        $transaksi = TransaksiAdministrasi::whereHas('berkas', fn ($query) => $query->where('user_id', Auth::id()))->findOrFail($id);

        if ($transaksi->status_transaksi === 'Diposting') {
            return back()->with('error', 'Transaksi yang sudah diposting tidak boleh diubah oleh CS.');
        }

        $data = $this->validateData($request);
        $newBukti = $this->saveBukti($request);
        if ($newBukti) {
            $this->deleteBukti($transaksi->bukti_pembayaran);
            $data['bukti_pembayaran'] = $newBukti;
        }

        // Saat transaksi ditolak lalu diperbaiki oleh CS, status verifikasinya kembali netral.
        if (($data['status_transaksi'] ?? '') !== 'Ditolak') {
            $data['diperiksa_oleh'] = null;
            $data['tanggal_verifikasi'] = null;
            $data['catatan_verifikasi'] = null;
        }

        $transaksi->update($data);

        return redirect()->route('cs.transaksi.index')->with('success', 'Transaksi administrasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $transaksi = TransaksiAdministrasi::whereHas('berkas', fn ($query) => $query->where('user_id', Auth::id()))->findOrFail($id);

        if ($transaksi->status_transaksi === 'Diposting') {
            return back()->with('error', 'Transaksi yang sudah diposting tidak boleh dihapus.');
        }

        $this->deleteBukti($transaksi->bukti_pembayaran);
        $transaksi->delete();

        return redirect()->route('cs.transaksi.index')->with('success', 'Transaksi administrasi berhasil dihapus.');
    }

    public function perBerkas($idBerkas)
    {
        $berkas = $this->berkasSaya()->findOrFail($idBerkas);
        $transaksis = $berkas->transaksis()->latest('tanggal_transaksi')->get();

        return view('cs.transaksi.perberkas', compact('berkas', 'transaksis'));
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'id_berkas' => 'required|exists:berkas,id',
            'arah_transaksi' => 'required|in:Pemasukan,Pengeluaran',
            'jenis_transaksi' => 'required|string|min:3|max:150',
            'kategori' => 'required|in:Biaya Administrasi,Biaya Layanan,Biaya Pendaftaran,Biaya Legalitas,ATK dan Cetak,Transportasi,Operasional Lainnya,Administrasi,Pendaftaran,Legalitas,Pembayaran Layanan,Lainnya',
            'nominal' => 'required|numeric|min:1|max:999999999999',
            'status_transaksi' => 'required|in:Belum Dibayar,Menunggu Verifikasi,Ditolak',
            'metode_pembayaran' => 'required|in:Tunai,Transfer Bank,QRIS,Lainnya',
            'nomor_referensi' => 'nullable|string|max:100',
            'tanggal_transaksi' => 'required|date|before_or_equal:today',
            'keterangan' => 'nullable|string|max:500',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
    }

    private function saveBukti(Request $request): ?string
    {
        if (!$request->hasFile('bukti_pembayaran')) {
            return null;
        }

        $file = $request->file('bukti_pembayaran');
        $extension = strtolower($file->getClientOriginalExtension());
        $name = Str::uuid() . '.' . $extension;

        return $file->storeAs('private/bukti_pembayaran', $name, 'local');
    }

    private function deleteBukti(?string $path): void
    {
        if (!$path) return;
        if (Storage::disk('local')->exists($path)) Storage::disk('local')->delete($path);
        // Tetap bersihkan bukti lama yang pernah disimpan pada folder public.
        if (Storage::disk('public')->exists($path)) Storage::disk('public')->delete($path);
    }

    private function categories(): array
    {
        return [
            'Biaya Administrasi',
            'Biaya Layanan',
            'Biaya Pendaftaran',
            'Biaya Legalitas',
            'ATK dan Cetak',
            'Transportasi',
            'Operasional Lainnya',
        ];
    }
}
