<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use App\Models\Berkas;
use App\Models\Nasabah;
use App\Models\TransaksiAdministrasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $csId = Auth::id();
        $today = Carbon::today();
        $berkasQuery = Berkas::where('user_id', $csId);

        $totalNasabah = Nasabah::where('created_by', $csId)->count();
        $totalBerkas = (clone $berkasQuery)->count();
        $berkasDiproses = (clone $berkasQuery)->where('status_berkas', 'Diproses')->count();
        $berkasSelesai = (clone $berkasQuery)->where('status_berkas', 'Selesai')->count();
        $berkasTerlambat = (clone $berkasQuery)
            ->where('status_berkas', '!=', 'Selesai')
            ->whereNotNull('estimasi_selesai')
            ->whereDate('estimasi_selesai', '<', $today)
            ->count();
        $slaHampir = (clone $berkasQuery)
            ->where('status_berkas', '!=', 'Selesai')
            ->whereNotNull('estimasi_selesai')
            ->whereBetween('estimasi_selesai', [$today, $today->copy()->addDays(2)])
            ->count();
        $totalTransaksi = TransaksiAdministrasi::whereHas('berkas', fn ($query) => $query->where('user_id', $csId))->sum('nominal');
        $slaList = (clone $berkasQuery)->with('nasabah')
            ->where('status_berkas', '!=', 'Selesai')
            ->whereNotNull('estimasi_selesai')
            ->whereDate('estimasi_selesai', '<=', $today->copy()->addDays(2))
            ->orderBy('estimasi_selesai')
            ->take(5)
            ->get();
        $latestTransactions = TransaksiAdministrasi::with('berkas.nasabah')
            ->whereHas('berkas', fn ($query) => $query->where('user_id', $csId))
            ->latest('tanggal_transaksi')
            ->take(5)
            ->get();

        $statusSummary = [
            'Diterima' => (clone $berkasQuery)->where('status_berkas', 'Diterima')->count(),
            'Diproses' => $berkasDiproses,
            'Selesai' => $berkasSelesai,
        ];

        return view('cs.dashboard.index', compact(
            'totalNasabah', 'totalBerkas', 'berkasDiproses', 'berkasSelesai',
            'berkasTerlambat', 'slaHampir', 'totalTransaksi', 'slaList',
            'latestTransactions', 'statusSummary'
        ));
    }
}
