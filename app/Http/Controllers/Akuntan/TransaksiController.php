<?php

namespace App\Http\Controllers\Akuntan;

use App\Http\Controllers\Controller;
use App\Models\AkunAkuntansi;
use App\Models\DetailJurnal;
use App\Models\JurnalUmum;
use App\Models\TransaksiAdministrasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    private array $pendingStatuses = ['Menunggu Verifikasi', 'Lunas'];

    public function index(Request $request)
    {
        $status = $request->input('status', 'Menunggu Verifikasi');
        $search = $request->input('search');

        $transactions = TransaksiAdministrasi::with(['berkas.nasabah', 'verifikator', 'jurnal'])
            ->when($status !== 'Semua', function ($query) use ($status) {
                if ($status === 'Menunggu Verifikasi') {
                    return $query->whereIn('status_transaksi', $this->pendingStatuses);
                }

                return $query->where('status_transaksi', $status);
            })
            ->when($search, function ($query, $search) {
                $query->where(function ($item) use ($search) {
                    $item->where('jenis_transaksi', 'like', "%{$search}%")
                        ->orWhere('kategori', 'like', "%{$search}%")
                        ->orWhereHas('berkas.nasabah', fn ($nasabah) => $nasabah->where('nama_nasabah', 'like', "%{$search}%"));
                });
            })
            ->orderByRaw("FIELD(status_transaksi, 'Menunggu Verifikasi', 'Lunas', 'Ditolak', 'Diposting', 'Belum Dibayar')")
            ->orderByDesc('tanggal_transaksi')
            ->get();

        $accounts = AkunAkuntansi::where('status', 'aktif')->orderBy('kode_akun')->get();
        $statuses = ['Menunggu Verifikasi', 'Diposting', 'Ditolak', 'Belum Dibayar', 'Semua'];

        return view('akuntan.transaksi.index', compact('transactions', 'accounts', 'statuses', 'status', 'search'));
    }

    public function post(Request $request, $id)
    {
        $transaction = TransaksiAdministrasi::with('jurnal')->findOrFail($id);

        if (!in_array($transaction->status_transaksi, $this->pendingStatuses)) {
            return back()->with('error', 'Hanya transaksi berstatus Menunggu Verifikasi yang dapat diposting.');
        }

        if ($transaction->jurnal) {
            return back()->with('error', 'Transaksi ini sudah memiliki jurnal umum.');
        }

        $data = $request->validate([
            'akun_debit_id' => 'required|different:akun_kredit_id|exists:akun_akuntansi,id',
            'akun_kredit_id' => 'required|exists:akun_akuntansi,id',
            'catatan_verifikasi' => 'nullable|string|max:500',
        ]);

        $debit = AkunAkuntansi::where('status', 'aktif')->find($data['akun_debit_id']);
        $kredit = AkunAkuntansi::where('status', 'aktif')->find($data['akun_kredit_id']);
        if (!$debit || !$kredit) {
            return back()->with('error', 'Akun debit dan kredit harus berstatus aktif.');
        }

        DB::transaction(function () use ($transaction, $data) {
            $tanggal = $transaction->tanggal_transaksi;
            $jumlahJurnal = JurnalUmum::whereDate('tanggal_jurnal', $tanggal)->lockForUpdate()->count() + 1;
            $nomorJurnal = 'JU-' . $tanggal->format('Ymd') . '-' . str_pad((string) $jumlahJurnal, 3, '0', STR_PAD_LEFT);

            $jurnal = JurnalUmum::create([
                'transaksi_id' => $transaction->id,
                'user_id' => Auth::id(),
                'nomor_jurnal' => $nomorJurnal,
                'tanggal_jurnal' => $tanggal,
                'keterangan' => $transaction->jenis_transaksi . ' - ' . ($transaction->berkas?->nasabah?->nama_nasabah ?? 'Nasabah'),
            ]);

            DetailJurnal::create([
                'jurnal_id' => $jurnal->id,
                'akun_id' => $data['akun_debit_id'],
                'debit' => $transaction->nominal,
                'kredit' => 0,
                'keterangan' => 'Debit transaksi layanan',
            ]);

            DetailJurnal::create([
                'jurnal_id' => $jurnal->id,
                'akun_id' => $data['akun_kredit_id'],
                'debit' => 0,
                'kredit' => $transaction->nominal,
                'keterangan' => 'Kredit transaksi layanan',
            ]);

            $transaction->update([
                'status_transaksi' => 'Diposting',
                'diperiksa_oleh' => Auth::id(),
                'tanggal_verifikasi' => now(),
                'catatan_verifikasi' => $data['catatan_verifikasi'] ?? null,
            ]);
        });

        return back()->with('success', 'Transaksi berhasil diposting. Jurnal umum dibuat otomatis.');
    }

    public function reject(Request $request, $id)
    {
        $transaction = TransaksiAdministrasi::findOrFail($id);

        if (!in_array($transaction->status_transaksi, $this->pendingStatuses)) {
            return back()->with('error', 'Hanya transaksi yang menunggu verifikasi yang dapat ditolak.');
        }

        $data = $request->validate([
            'catatan_verifikasi' => 'required|string|max:500',
        ]);

        $transaction->update([
            'status_transaksi' => 'Ditolak',
            'diperiksa_oleh' => Auth::id(),
            'tanggal_verifikasi' => now(),
            'catatan_verifikasi' => $data['catatan_verifikasi'],
        ]);

        return back()->with('success', 'Transaksi ditolak dan dapat diperbaiki kembali oleh CS.');
    }
}
