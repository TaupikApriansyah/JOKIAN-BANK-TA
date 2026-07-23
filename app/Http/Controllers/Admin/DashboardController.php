<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berkas;
use App\Models\TransaksiAdministrasi;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $bulanIni = now();

        $totalUserAktif = User::where('status', 'aktif')->count();
        $totalBerkas = Berkas::count();
        $berkasSelesai = Berkas::where('status_berkas', 'Selesai')->count();
        $berkasTerlambat = Berkas::where('status_berkas', '!=', 'Selesai')
            ->whereNotNull('estimasi_selesai')
            ->whereDate('estimasi_selesai', '<', $today)
            ->count();
        $slaHampir = Berkas::where('status_berkas', '!=', 'Selesai')
            ->whereNotNull('estimasi_selesai')
            ->whereBetween('estimasi_selesai', [$today, $today->copy()->addDays(2)])
            ->count();

        $posted = TransaksiAdministrasi::where('status_transaksi', 'Diposting')
            ->whereMonth('tanggal_transaksi', $bulanIni->month)
            ->whereYear('tanggal_transaksi', $bulanIni->year);

        $pemasukanBulan = (clone $posted)
            ->where('arah_transaksi', 'Pemasukan')
            ->sum('nominal');
        $pengeluaranBulan = (clone $posted)
            ->where('arah_transaksi', 'Pengeluaran')
            ->sum('nominal');

        $menungguVerifikasi = TransaksiAdministrasi::whereIn('status_transaksi', [
            'Belum Dibayar',
            'Menunggu Verifikasi',
            'Lunas',
        ])->count();

        $csAktifHariIni = User::where('role', 'cs')->where('status', 'aktif')->count();
        $slaList = Berkas::with('nasabah')
            ->where('status_berkas', '!=', 'Selesai')
            ->whereNotNull('estimasi_selesai')
            ->whereDate('estimasi_selesai', '<=', $today->copy()->addDays(2))
            ->orderBy('estimasi_selesai')
            ->take(5)
            ->get();

        $statusSummary = [
            'Diterima' => Berkas::where('status_berkas', 'Diterima')->count(),
            'Diproses' => Berkas::where('status_berkas', 'Diproses')->count(),
            'Selesai' => $berkasSelesai,
        ];

        $latestTransactions = TransaksiAdministrasi::with('berkas.nasabah')
            ->latest('tanggal_transaksi')
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalUserAktif',
            'totalBerkas',
            'berkasSelesai',
            'berkasTerlambat',
            'slaHampir',
            'pemasukanBulan',
            'pengeluaranBulan',
            'menungguVerifikasi',
            'csAktifHariIni',
            'slaList',
            'statusSummary',
            'latestTransactions'
        ));
    }
}
