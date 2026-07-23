<?php

namespace App\Http\Controllers\Akuntan;

use App\Http\Controllers\Controller;
use App\Models\JurnalUmum;
use App\Models\KasKecil;
use App\Models\TransaksiAdministrasi;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $kasKecilMasuk = (float) KasKecil::where('jenis', 'Masuk')->sum('nominal');
        $kasKecilKeluar = (float) KasKecil::where('jenis', 'Keluar')->sum('nominal');
        $saldoKasKecil = $kasKecilMasuk - $kasKecilKeluar;

        $pendingCount = TransaksiAdministrasi::whereIn('status_transaksi', ['Menunggu Verifikasi', 'Lunas'])->count();
        $postedToday = TransaksiAdministrasi::where('status_transaksi', 'Diposting')
            ->whereDate('tanggal_verifikasi', $today)
            ->count();

        $postedMonth = TransaksiAdministrasi::where('status_transaksi', 'Diposting')
            ->whereMonth('tanggal_transaksi', $today->month)
            ->whereYear('tanggal_transaksi', $today->year);

        $pemasukanBulan = (clone $postedMonth)
            ->where('arah_transaksi', 'Pemasukan')
            ->sum('nominal');
        $pengeluaranBulan = (clone $postedMonth)
            ->where('arah_transaksi', 'Pengeluaran')
            ->sum('nominal');

        $pendingTransactions = TransaksiAdministrasi::with('berkas.nasabah')
            ->whereIn('status_transaksi', ['Menunggu Verifikasi', 'Lunas'])
            ->orderBy('tanggal_transaksi')
            ->take(6)
            ->get();

        $latestJournals = JurnalUmum::with(['transaksi.berkas.nasabah', 'user'])
            ->orderByDesc('tanggal_jurnal')
            ->take(5)
            ->get();

        $chart = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = $today->copy()->subMonths($i);
            $monthQuery = TransaksiAdministrasi::where('status_transaksi', 'Diposting')
                ->whereMonth('tanggal_transaksi', $date->month)
                ->whereYear('tanggal_transaksi', $date->year);

            $chart[] = [
                'label' => $date->translatedFormat('M'),
                'income' => (float) (clone $monthQuery)->where('arah_transaksi', 'Pemasukan')->sum('nominal'),
                'expense' => (float) (clone $monthQuery)->where('arah_transaksi', 'Pengeluaran')->sum('nominal'),
            ];
        }

        $chartMax = max(1, collect($chart)->flatMap(fn ($item) => [$item['income'], $item['expense']])->max());

        return view('akuntan.dashboard.index', compact(
            'pendingCount',
            'postedToday',
            'pemasukanBulan',
            'pengeluaranBulan',
            'pendingTransactions',
            'latestJournals',
            'chart',
            'chartMax',
            'saldoKasKecil'
        ));
    }
}
