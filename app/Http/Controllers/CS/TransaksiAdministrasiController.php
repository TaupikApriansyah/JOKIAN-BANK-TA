<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use App\Models\Berkas;
use App\Models\TransaksiAdministrasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class TransaksiAdministrasiController extends Controller
{
    private function berkasSaya()
    {
        return Berkas::with('nasabah')
            ->where('user_id', Auth::id())
            ->orderByDesc('tanggal_masuk')
            ->orderByDesc('id');
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
            ->orderByDesc('id')
            ->get();

        $totalNominal = $transaksis->sum('nominal');
        $berkasList = $this->berkasSaya()->get();
        $categories = $this->categories();
        $paymentStatuses = $this->paymentStatuses();
        $paymentMethods = $this->paymentMethods();
        $transactionDirections = $this->transactionDirections();

        return view('cs.transaksi.index', compact(
            'transaksis',
            'totalNominal',
            'berkasList',
            'categories',
            'paymentStatuses',
            'paymentMethods',
            'transactionDirections'
        ));
    }

    public function create()
    {
        $berkasList = $this->berkasSaya()->get();
        $categories = $this->categories();
        $paymentStatuses = $this->paymentStatuses();
        $paymentMethods = $this->paymentMethods();
        $transactionDirections = $this->transactionDirections();

        return view('cs.transaksi.create', compact(
            'berkasList',
            'categories',
            'paymentStatuses',
            'paymentMethods',
            'transactionDirections'
        ));
    }

    public function store(Request $request)
    {
        $this->applyLegacyFormDefaults($request);
        $data = $this->validateData($request);
        $storedProof = $this->saveBukti($request);

        if ($storedProof) {
            $data['bukti_pembayaran'] = $storedProof;
        }

        try {
            DB::transaction(fn () => TransaksiAdministrasi::create($data));
        } catch (Throwable $exception) {
            $this->deleteBukti($storedProof);
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Transaksi gagal disimpan. Silakan periksa data lalu coba lagi.');
        }

        return redirect()
            ->route('cs.transaksi.index')
            ->with('success', 'Transaksi berhasil disimpan dan muncul pada daftar transaksi.');
    }

    public function update(Request $request, $id)
    {
        $transaksi = TransaksiAdministrasi::whereHas(
            'berkas',
            fn ($query) => $query->where('user_id', Auth::id())
        )->findOrFail($id);

        if ($transaksi->status_transaksi === 'Diposting') {
            return back()->with('error', 'Transaksi yang sudah diposting tidak boleh diubah oleh CS.');
        }

        $this->applyLegacyFormDefaults($request);
        $data = $this->validateData($request);
        $newBukti = $this->saveBukti($request);

        if ($newBukti) {
            $data['bukti_pembayaran'] = $newBukti;
        }

        if (($data['status_transaksi'] ?? '') !== 'Ditolak') {
            $data['diperiksa_oleh'] = null;
            $data['tanggal_verifikasi'] = null;
            $data['catatan_verifikasi'] = null;
        }

        try {
            DB::transaction(function () use ($transaksi, $data, $newBukti) {
                $oldBukti = $transaksi->bukti_pembayaran;
                $transaksi->update($data);

                if ($newBukti && $oldBukti) {
                    $this->deleteBukti($oldBukti);
                }
            });
        } catch (Throwable $exception) {
            $this->deleteBukti($newBukti);
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Perubahan transaksi gagal disimpan. Silakan coba lagi.');
        }

        return redirect()->route('cs.transaksi.index')->with('success', 'Transaksi administrasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $transaksi = TransaksiAdministrasi::whereHas(
            'berkas',
            fn ($query) => $query->where('user_id', Auth::id())
        )->findOrFail($id);

        if ($transaksi->status_transaksi === 'Diposting') {
            return back()->with('error', 'Transaksi yang sudah diposting tidak boleh dihapus.');
        }

        $proof = $transaksi->bukti_pembayaran;
        $transaksi->delete();
        $this->deleteBukti($proof);

        return redirect()->route('cs.transaksi.index')->with('success', 'Transaksi administrasi berhasil dihapus.');
    }

    public function perBerkas($idBerkas)
    {
        $berkas = $this->berkasSaya()->findOrFail($idBerkas);
        $transaksis = $berkas->transaksis()->latest('tanggal_transaksi')->get();

        return view('cs.transaksi.perberkas', compact('berkas', 'transaksis'));
    }

    private function applyLegacyFormDefaults(Request $request): void
    {
        // Form transaksi lama hanya mengirim beberapa kolom. Default ini menjaga
        // kompatibilitas agar data tetap tersimpan setelah fitur akuntansi ditambah.
        $request->merge([
            'arah_transaksi' => $request->input('arah_transaksi', 'Pemasukan'),
            'kategori' => $request->input('kategori', 'Biaya Administrasi'),
            'status_transaksi' => $request->input('status_transaksi', 'Menunggu Verifikasi'),
            'metode_pembayaran' => $request->input('metode_pembayaran', 'Tunai'),
        ]);
    }

    private function validateData(Request $request): array
    {
        $validated = $request->validate([
            'id_berkas' => ['required', 'integer', 'exists:berkas,id'],
            'arah_transaksi' => ['required', 'in:Pemasukan,Pengeluaran'],
            'jenis_transaksi' => ['required', 'string', 'min:3', 'max:150'],
            'kategori' => ['required', 'in:' . implode(',', $this->categories())],
            'nominal' => ['required', 'numeric', 'min:1', 'max:999999999999'],
            'status_transaksi' => ['required', 'in:' . implode(',', $this->paymentStatuses())],
            'metode_pembayaran' => ['required', 'in:' . implode(',', $this->paymentMethods())],
            'nomor_referensi' => ['nullable', 'string', 'max:100'],
            'tanggal_transaksi' => ['required', 'date', 'before_or_equal:today'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'bukti_pembayaran' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ], [
            'id_berkas.required' => 'Berkas nasabah wajib dipilih.',
            'id_berkas.exists' => 'Berkas yang dipilih tidak ditemukan.',
            'jenis_transaksi.required' => 'Nama transaksi wajib diisi.',
            'jenis_transaksi.min' => 'Nama transaksi minimal 3 karakter.',
            'nominal.required' => 'Nominal transaksi wajib diisi.',
            'nominal.min' => 'Nominal transaksi minimal Rp1.',
            'tanggal_transaksi.required' => 'Tanggal transaksi wajib diisi.',
            'tanggal_transaksi.before_or_equal' => 'Tanggal transaksi tidak boleh melewati hari ini.',
            'bukti_pembayaran.mimes' => 'Bukti pembayaran harus berupa JPG, JPEG, PNG, atau PDF.',
            'bukti_pembayaran.max' => 'Ukuran bukti pembayaran maksimal 2 MB.',
        ]);

        // Pastikan CS tidak dapat memasukkan transaksi pada berkas milik CS lain.
        $this->berkasSaya()->findOrFail($validated['id_berkas']);

        return $validated;
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
        if (!$path) {
            return;
        }

        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
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

    private function paymentStatuses(): array
    {
        return ['Belum Dibayar', 'Menunggu Verifikasi', 'Ditolak'];
    }

    private function paymentMethods(): array
    {
        return ['Tunai', 'Transfer Bank', 'QRIS', 'Lainnya'];
    }

    private function transactionDirections(): array
    {
        return ['Pemasukan', 'Pengeluaran'];
    }
}
