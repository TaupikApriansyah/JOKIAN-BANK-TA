<?php

namespace App\Providers;

use App\Models\Berkas;
use App\Models\TransaksiAdministrasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        /*
         * Peringatan SLA ditampilkan otomatis setiap halaman dibuka.
         * Berkas akan masuk alert dua hari sebelum estimasi selesai,
         * atau langsung ditandai terlambat bila tanggalnya sudah lewat.
         */
        View::composer(['layouts.admin', 'layouts.cs'], function ($view) {
            $alerts = collect();

            if (Auth::check()) {
                try {
                    $today = Carbon::today();
                    $query = Berkas::with('nasabah')
                        ->where('status_berkas', '!=', 'Selesai')
                        ->whereNotNull('estimasi_selesai')
                        ->whereDate('estimasi_selesai', '<=', $today->copy()->addDays(2));

                    if (Auth::user()->role === 'cs') {
                        $query->where('user_id', Auth::id());
                    }

                    $alerts = $query->orderBy('estimasi_selesai')->take(5)->get()
                        ->each(function ($berkas) use ($today) {
                            $deadline = Carbon::parse($berkas->estimasi_selesai)->startOfDay();
                            $days = $today->diffInDays($deadline, false);
                            $berkas->sla_label = $days < 0
                                ? 'SLA lewat ' . abs($days) . ' hari'
                                : ($days === 0 ? 'Jatuh tempo hari ini' : 'SLA ' . $days . ' hari lagi');
                        });
                } catch (\Throwable $error) {
                    // Database dapat belum dibuat saat pertama kali instalasi.
                    $alerts = collect();
                }
            }

            $view->with('slaAlerts', $alerts)->with('slaAlertCount', $alerts->count());
        });

        // Akuntan menerima notifikasi transaksi yang harus diperiksa.
        View::composer('layouts.akuntan', function ($view) {
            $transactions = collect();

            if (Auth::check()) {
                try {
                    $transactions = TransaksiAdministrasi::with('berkas.nasabah')
                        ->whereIn('status_transaksi', ['Menunggu Verifikasi', 'Lunas'])
                        ->orderBy('tanggal_transaksi')
                        ->take(5)
                        ->get();
                } catch (\Throwable $error) {
                    $transactions = collect();
                }
            }

            $view->with('pendingVerificationTransactions', $transactions)
                ->with('pendingVerificationCount', $transactions->count());
        });
    }
}
