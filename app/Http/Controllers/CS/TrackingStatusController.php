<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use App\Models\Berkas;
use App\Models\TrackingStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackingStatusController extends Controller
{
    private function berkasSaya()
    {
        return Berkas::with('nasabah')->where('user_id', Auth::id())->orderByDesc('tanggal_masuk');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $trackings = TrackingStatus::with(['berkas.nasabah', 'user'])
            ->whereHas('berkas', fn ($query) => $query->where('user_id', Auth::id()))
            ->when($search, function ($query, $search) {
                $query->where(function ($item) use ($search) {
                    $item->where('status', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%")
                        ->orWhereHas('berkas.nasabah', fn ($nasabah) => $nasabah->where('nama_nasabah', 'like', "%{$search}%"));
                });
            })
            ->latest('tanggal_update')
            ->get();
        $berkasList = $this->berkasSaya()->get();

        return view('cs.tracking_status.index', compact('trackings', 'berkasList'));
    }

    public function show($id)
    {
        $tracking = TrackingStatus::with(['berkas.nasabah', 'user'])
            ->whereHas('berkas', fn ($query) => $query->where('user_id', Auth::id()))
            ->findOrFail($id);
        return view('cs.tracking_status.show', compact('tracking'));
    }

    public function create()
    {
        $berkas = $this->berkasSaya()->get();
        $users = collect([Auth::user()]);
        return view('cs.tracking_status.create', compact('berkas', 'users'));
    }

    public function store(Request $request)
    {
        TrackingStatus::create($this->validateData($request));
        return redirect()->route('cs.tracking.index')->with('success', 'Status tracking berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $tracking = TrackingStatus::whereHas('berkas', fn ($query) => $query->where('user_id', Auth::id()))->findOrFail($id);
        $berkas = $this->berkasSaya()->get();
        $users = collect([Auth::user()]);
        return view('cs.tracking_status.edit', compact('tracking', 'berkas', 'users'));
    }

    public function update(Request $request, $id)
    {
        $tracking = TrackingStatus::whereHas('berkas', fn ($query) => $query->where('user_id', Auth::id()))->findOrFail($id);
        $tracking->update($this->validateData($request));
        return redirect()->route('cs.tracking.index')->with('success', 'Status tracking berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $tracking = TrackingStatus::whereHas('berkas', fn ($query) => $query->where('user_id', Auth::id()))->findOrFail($id);
        $tracking->delete();
        return redirect()->route('cs.tracking.index')->with('success', 'Status tracking berhasil dihapus.');
    }

    private function validateData(Request $request): array
    {
        $validated = $request->validate([
            'berkas_id' => 'required|exists:berkas,id',
            'status' => 'required|in:Diterima,Diproses,Selesai',
            'tanggal_update' => 'required|date|before_or_equal:now',
            'keterangan' => 'nullable|string|max:500',
        ]);
        $this->berkasSaya()->findOrFail($validated['berkas_id']);
        $validated['user_id'] = Auth::id();
        return $validated;
    }
}
