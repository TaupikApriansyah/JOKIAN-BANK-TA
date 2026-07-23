<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use App\Models\Nasabah;
use App\Models\Berkas;
use App\Models\TrackingStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BerkasController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $berkas = Berkas::where('user_id', auth()->id())->with([
                'nasabah',
                'latestTracking'
            ])
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('jenis_layanan', 'like', "%{$search}%")
                      ->orWhereHas('nasabah', function($q2) use ($search) {
                          $q2->where('nama_nasabah', 'like', "%{$search}%");
                      });
                });
            })
            ->orderByDesc('tanggal_masuk')
            ->get();

        $nasabah = Nasabah::where('created_by', auth()->id())->orderBy('nama_nasabah')->get();
        return view('cs.berkas.index', compact('berkas', 'nasabah'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $nasabah = Nasabah::where('created_by', auth()->id())->orderBy('nama_nasabah')->get();
        return view('cs.berkas.create', compact('nasabah'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'id_nasabah'       => 'required|exists:nasabah,id',
            'jenis_layanan'    => 'required|string|min:3|max:150',
            'tanggal_masuk'    => 'required|date|before_or_equal:today',
            'estimasi_selesai' => 'nullable|date|after_or_equal:tanggal_masuk',
        ]);

        Nasabah::where('created_by', auth()->id())->findOrFail($request->id_nasabah);

        Berkas::create([
            'id_nasabah'       => $request->id_nasabah,
            'user_id'          => auth()->id(),
            'jenis_layanan'    => $request->jenis_layanan,
            'tanggal_masuk'    => $request->tanggal_masuk,
            'estimasi_selesai' => $request->estimasi_selesai,
            'status_berkas'    => 'Diterima',
        ]);

        return redirect()
            ->route('cs.berkas.index')
            ->with('success', 'Berkas berhasil ditambahkan.');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $berkas = Berkas::with([
                'nasabah',
                'trackings.user'
            ])->where('user_id', auth()->id())->findOrFail($id);

        return view('cs.berkas.show', compact('berkas'));
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $berkas = Berkas::where('user_id', auth()->id())->findOrFail($id);
        return view('cs.berkas.edit', compact('berkas'));
    }

    public function update(Request $request, $id)
    {
        $berkas = Berkas::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'id_nasabah' => 'required|exists:nasabah,id',
            'jenis_layanan' => 'required|string|min:3|max:150',
            'tanggal_masuk' => 'required|date|before_or_equal:today',
            'estimasi_selesai' => 'nullable|date|after_or_equal:tanggal_masuk',
        ]);

        $nasabah = Nasabah::where('created_by', auth()->id())->findOrFail($validated['id_nasabah']);
        $berkas->update($validated);

        return redirect()->route('cs.berkas.index')->with('success', 'Data berkas berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS
    |--------------------------------------------------------------------------
    */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_berkas' => 'required|in:Diterima,Diproses,Selesai',
            'keterangan'    => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request, $id) {

            $berkas = Berkas::where('user_id', auth()->id())->findOrFail($id);

            $berkas->update([
                'status_berkas' => $request->status_berkas,
            ]);

            TrackingStatus::create([
                'berkas_id'      => $berkas->id, // pastikan ini sesuai nama kolom DB
                'user_id'        => auth()->id(),
                'status'         => $request->status_berkas,
                'keterangan'     => $request->keterangan,
                'tanggal_update' => now(),
            ]);
        });

        return redirect()
            ->route('cs.berkas.index')
            ->with('success', 'Status berkas berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $berkas = Berkas::where('user_id', auth()->id())->findOrFail($id);

        DB::transaction(function () use ($berkas) {

            // Hapus tracking terkait
            TrackingStatus::where('berkas_id', $berkas->id)->delete();

            // Hapus berkas
            $berkas->delete();
        });

        return redirect()
            ->route('cs.berkas.index')
            ->with('success', 'Berkas berhasil dihapus.');
    }
}