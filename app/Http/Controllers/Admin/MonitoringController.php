<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berkas;
use App\Models\TrackingStatus;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | MONITORING BERKAS (READ ONLY) + SEARCH
    |--------------------------------------------------------------------------
    */
    public function berkas(Request $request)
    {
        $query = Berkas::with([
            'nasabah',
            'user',
            'latestTracking'
        ]);

        // 🔍 FITUR PENCARIAN
        if ($request->filled('search')) {
            $search = $request->search;
            
            $query->where(function($q) use ($search) {
                // Cari berdasarkan nama nasabah
                $q->whereHas('nasabah', function($q2) use ($search) {
                    $q2->where('nama_nasabah', 'LIKE', "%{$search}%");
                })
                // Atau cari berdasarkan jenis layanan
                ->orWhere('jenis_layanan', 'LIKE', "%{$search}%")
                // Atau cari berdasarkan status
                ->orWhere('status_berkas', 'LIKE', "%{$search}%")
                // Atau cari berdasarkan nama CS
                ->orWhereHas('user', function($q2) use ($search) {
                    $q2->where('name', 'LIKE', "%{$search}%");
                });
            });
        }

        // 🔍 FILTER BY STATUS
        if ($request->filled('status')) {
            $query->where('status_berkas', $request->status);
        }

        // 🔍 FILTER BY JENIS LAYANAN
        if ($request->filled('jenis_layanan')) {
            $query->where('jenis_layanan', $request->jenis_layanan);
        }

        $berkas = $query->latest()->paginate(10)->withQueryString();

        return view('admin.monitoring.berkas', compact('berkas'));
    }

    /*
    |--------------------------------------------------------------------------
    | DETAIL BERKAS (READ ONLY)
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $berkas = Berkas::with([
            'nasabah',
            'user',
            'latestTracking'
        ])->findOrFail($id);

        return view('admin.monitoring.show_berkas', compact('berkas'));
    }

    /*
    |--------------------------------------------------------------------------
    | MONITORING TRACKING STATUS (READ ONLY) + SEARCH
    |--------------------------------------------------------------------------
    */
    public function tracking(Request $request)
    {
        $query = TrackingStatus::with([
            'berkas.nasabah',
            'berkas',
            'user'
        ]);

        // 🔍 FITUR PENCARIAN
        if ($request->filled('search')) {
            $search = $request->search;
            
            $query->where(function($q) use ($search) {
                // Cari berdasarkan nama nasabah
                $q->whereHas('berkas.nasabah', function($q2) use ($search) {
                    $q2->where('nama_nasabah', 'LIKE', "%{$search}%");
                })
                // Atau cari berdasarkan jenis layanan
                ->orWhereHas('berkas', function($q2) use ($search) {
                    $q2->where('jenis_layanan', 'LIKE', "%{$search}%");
                })
                // Atau cari berdasarkan status
                ->orWhere('status', 'LIKE', "%{$search}%")
                // Atau cari berdasarkan keterangan
                ->orWhere('keterangan', 'LIKE', "%{$search}%")
                // Atau cari berdasarkan nama CS
                ->orWhereHas('user', function($q2) use ($search) {
                    $q2->where('name', 'LIKE', "%{$search}%");
                });
            });
        }

        // 🔍 FILTER BY STATUS
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 🔍 FILTER BY TANGGAL
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_update', '>=', $request->tanggal_dari);
        }
        
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_update', '<=', $request->tanggal_sampai);
        }

        $tracking = $query->latest('tanggal_update')->paginate(10)->withQueryString();

        return view('admin.monitoring.tracking', compact('tracking'));
    }

    /*
    |--------------------------------------------------------------------------
    | DETAIL TRACKING (READ ONLY)
    |--------------------------------------------------------------------------
    */
    public function showTracking($id)
    {
        $tracking = TrackingStatus::with([
            'berkas.nasabah',
            'berkas.user',
            'user'
        ])->findOrFail($id);

        return view('admin.monitoring.show_tracking', compact('tracking'));
    }
}