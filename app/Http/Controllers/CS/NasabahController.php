<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use App\Models\Nasabah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NasabahController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $nasabah = Nasabah::where('created_by', Auth::id())
                    ->when($search, function ($query, $search) {
                        return $query->where(function($q) use ($search) {
                            $q->where('nama_nasabah', 'like', "%{$search}%")
                              ->orWhere('nik', 'like', "%{$search}%")
                              ->orWhere('no_telepon', 'like', "%{$search}%");
                        });
                    })
                    ->latest()
                    ->get();

        return view('cs.nasabah.index', compact('nasabah'));
    }

    public function create()
    {
        return view('cs.nasabah.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_nasabah' => 'required|string|min:3|max:100',
            'nik' => 'required|digits:16|unique:nasabah,nik',
            'alamat' => 'required|string|min:5|max:500',
            'no_telepon' => ['required', 'string', 'regex:/^[0-9+\-\s]{8,20}$/'],
        ]);

        $validated['created_by'] = Auth::id();

        Nasabah::create($validated);

        return redirect()
            ->route('cs.nasabah.index')
            ->with('success', 'Nasabah berhasil ditambahkan');
    }

    public function show($id)
    {
        $nasabah = Nasabah::with(['berkas.transaksis' => fn ($query) => $query->latest('tanggal_transaksi')])
                    ->where('created_by', Auth::id())
                    ->findOrFail($id);

        return view('cs.nasabah.show', compact('nasabah'));
    }

    public function edit($id)
    {
        $nasabah = Nasabah::with(['berkas.transaksis' => fn ($query) => $query->latest('tanggal_transaksi')])
                    ->where('created_by', Auth::id())
                    ->findOrFail($id);

        return view('cs.nasabah.edit', compact('nasabah'));
    }

    public function update(Request $request, $id)
    {
        $nasabah = Nasabah::with(['berkas.transaksis' => fn ($query) => $query->latest('tanggal_transaksi')])
                    ->where('created_by', Auth::id())
                    ->findOrFail($id);

        $validated = $request->validate([
            'nama_nasabah' => 'required|string|min:3|max:100',
            'nik' => 'required|digits:16|unique:nasabah,nik,' . $nasabah->id,
            'alamat' => 'required|string|min:5|max:500',
            'no_telepon' => ['required', 'string', 'regex:/^[0-9+\-\s]{8,20}$/'],
        ]);

        $nasabah->update($validated);

        return redirect()
            ->route('cs.nasabah.index')
            ->with('success', 'Nasabah berhasil diupdate');
    }

    public function destroy($id)
    {
        $nasabah = Nasabah::with(['berkas.transaksis' => fn ($query) => $query->latest('tanggal_transaksi')])
                    ->where('created_by', Auth::id())
                    ->findOrFail($id);

        $nasabah->delete();

        return redirect()
            ->route('cs.nasabah.index')
            ->with('success', 'Nasabah berhasil dihapus');
    }
}