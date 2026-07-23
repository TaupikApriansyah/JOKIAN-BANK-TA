<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use App\Models\Berkas;
use App\Models\Nasabah;
use App\Models\TrackingStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class BerkasController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $berkas = Berkas::query()
            ->where('user_id', auth()->id())
            ->with(['nasabah', 'latestTracking'])
            ->when($search, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('jenis_layanan', 'like', "%{$search}%")
                        ->orWhereHas('nasabah', function ($nasabahQuery) use ($search) {
                            $nasabahQuery->where('nama_nasabah', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByDesc('tanggal_masuk')
            ->orderByDesc('id')
            ->get();

        $nasabah = Nasabah::where('created_by', auth()->id())
            ->orderBy('nama_nasabah')
            ->get();

        return view('cs.berkas.index', compact('berkas', 'nasabah'));
    }

    public function create()
    {
        $nasabah = Nasabah::where('created_by', auth()->id())
            ->orderBy('nama_nasabah')
            ->get();

        return view('cs.berkas.create', compact('nasabah'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_nasabah' => ['required', 'integer', 'exists:nasabah,id'],
            'jenis_layanan' => ['required', 'string', 'min:3', 'max:150'],
            'tanggal_masuk' => ['required', 'date', 'before_or_equal:today'],
            'estimasi_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_masuk'],
        ], [
            'id_nasabah.required' => 'Nasabah wajib dipilih.',
            'id_nasabah.exists' => 'Nasabah yang dipilih tidak ditemukan.',
            'jenis_layanan.required' => 'Jenis layanan wajib diisi.',
            'jenis_layanan.min' => 'Jenis layanan minimal 3 karakter.',
            'tanggal_masuk.required' => 'Tanggal masuk wajib diisi.',
            'tanggal_masuk.before_or_equal' => 'Tanggal masuk tidak boleh melewati hari ini.',
            'estimasi_selesai.after_or_equal' => 'Estimasi selesai tidak boleh lebih awal dari tanggal masuk.',
        ]);

        Nasabah::where('created_by', auth()->id())->findOrFail($validated['id_nasabah']);

        try {
            DB::transaction(function () use ($validated) {
                $berkas = Berkas::create([
                    'id_nasabah' => $validated['id_nasabah'],
                    'user_id' => auth()->id(),
                    'jenis_layanan' => $validated['jenis_layanan'],
                    'tanggal_masuk' => $validated['tanggal_masuk'],
                    'estimasi_selesai' => $validated['estimasi_selesai'] ?? null,
                    'status_berkas' => 'Diterima',
                ]);

                // Setiap berkas baru langsung mempunyai riwayat tracking awal.
                TrackingStatus::create([
                    'berkas_id' => $berkas->id,
                    'user_id' => auth()->id(),
                    'status' => 'Diterima',
                    'tanggal_update' => now(),
                    'keterangan' => 'Berkas baru diterima dan dicatat oleh Customer Service.',
                ]);
            });
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Berkas gagal disimpan. Silakan periksa data lalu coba lagi.');
        }

        return redirect()
            ->route('cs.berkas.index')
            ->with('success', 'Berkas berhasil ditambahkan dan langsung masuk ke fitur Tracking.');
    }

    public function show($id)
    {
        $berkas = Berkas::with(['nasabah', 'trackings.user'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return view('cs.berkas.show', compact('berkas'));
    }

    public function edit($id)
    {
        $berkas = Berkas::where('user_id', auth()->id())->findOrFail($id);
        $nasabah = Nasabah::where('created_by', auth()->id())
            ->orderBy('nama_nasabah')
            ->get();

        return view('cs.berkas.edit', compact('berkas', 'nasabah'));
    }

    public function update(Request $request, $id)
    {
        $berkas = Berkas::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'id_nasabah' => ['required', 'integer', 'exists:nasabah,id'],
            'jenis_layanan' => ['required', 'string', 'min:3', 'max:150'],
            'tanggal_masuk' => ['required', 'date', 'before_or_equal:today'],
            'estimasi_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_masuk'],
        ]);

        Nasabah::where('created_by', auth()->id())->findOrFail($validated['id_nasabah']);
        $berkas->update($validated);

        return redirect()->route('cs.berkas.index')->with('success', 'Data berkas berhasil diperbarui.');
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status_berkas' => ['required', 'in:Diterima,Diproses,Selesai'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($validated, $id) {
            $berkas = Berkas::where('user_id', auth()->id())->findOrFail($id);

            $berkas->update(['status_berkas' => $validated['status_berkas']]);

            TrackingStatus::create([
                'berkas_id' => $berkas->id,
                'user_id' => auth()->id(),
                'status' => $validated['status_berkas'],
                'keterangan' => $validated['keterangan'] ?? null,
                'tanggal_update' => now(),
            ]);
        });

        return redirect()
            ->route('cs.berkas.index')
            ->with('success', 'Status berkas berhasil diperbarui dan tercatat pada Tracking.');
    }

    public function destroy($id)
    {
        $berkas = Berkas::where('user_id', auth()->id())->findOrFail($id);

        DB::transaction(function () use ($berkas) {
            // Relasi database sudah memakai cascade. Hapus eksplisit agar tetap aman
            // pada database lama yang belum memiliki constraint cascade.
            TrackingStatus::where('berkas_id', $berkas->id)->delete();
            $berkas->delete();
        });

        return redirect()
            ->route('cs.berkas.index')
            ->with('success', 'Berkas berhasil dihapus.');
    }
}
