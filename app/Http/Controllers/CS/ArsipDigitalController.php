<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use App\Models\ArsipDigital;
use App\Models\Berkas;
use App\Services\SimpleExcelExport;
use App\Services\SimplePdfExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArsipDigitalController extends Controller
{
    private function berkasSaya()
    {
        return Berkas::with('nasabah')->where('user_id', Auth::id())->orderByDesc('tanggal_masuk');
    }

    private function arsipSaya()
    {
        return ArsipDigital::with('berkas.nasabah')
            ->whereHas('berkas', fn ($query) => $query->where('user_id', Auth::id()));
    }

    public function index(Request $request)
    {
        $request->validate(['search' => ['nullable', 'string', 'max:100']]);
        $search = $request->input('search');
        $arsips = $this->arsipSaya()
            ->when($search, function ($query, $search) {
                $query->where(function ($item) use ($search) {
                    $item->where('nama_file', 'like', "%{$search}%")
                        ->orWhere('jenis_dokumen', 'like', "%{$search}%")
                        ->orWhereHas('berkas.nasabah', fn ($nasabah) => $nasabah->where('nama_nasabah', 'like', "%{$search}%"));
                });
            })
            ->orderByDesc('tanggal_upload')
            ->get();
        $berkasList = $this->berkasSaya()->get();

        return view('cs.arsip.index', compact('arsips', 'berkasList'));
    }

    public function create()
    {
        $berkasList = $this->berkasSaya()->get();
        return view('cs.arsip.create', compact('berkasList'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request, true);
        $this->berkasSaya()->findOrFail($validated['berkas_id']);

        ArsipDigital::create([
            'berkas_id' => $validated['berkas_id'],
            'jenis_dokumen' => $validated['jenis_dokumen'],
            'tanggal_upload' => now(),
            'status_arsip' => 'Aktif',
            ...$this->saveFile($request->file('file')),
        ]);

        return redirect()->route('cs.arsip.index')->with('success', 'Arsip berhasil diunggah. File disimpan secara privat.');
    }

    public function edit($id)
    {
        $arsip = $this->arsipSaya()->findOrFail($id);
        $berkasList = $this->berkasSaya()->get();
        return view('cs.arsip.edit', compact('arsip', 'berkasList'));
    }

    public function update(Request $request, $id)
    {
        $arsip = $this->arsipSaya()->findOrFail($id);
        $validated = $this->validateData($request, false);
        $this->berkasSaya()->findOrFail($validated['berkas_id']);

        $updateData = [
            'berkas_id' => $validated['berkas_id'],
            'jenis_dokumen' => $validated['jenis_dokumen'],
            'tanggal_upload' => $validated['tanggal_upload'],
        ];

        if ($request->hasFile('file')) {
            $this->deleteFile($arsip->path_file);
            $updateData = [...$updateData, ...$this->saveFile($request->file('file'))];
        }

        $arsip->update($updateData);
        return redirect()->route('cs.arsip.index')->with('success', 'Arsip berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $arsip = $this->arsipSaya()->findOrFail($id);
        $this->deleteFile($arsip->path_file);
        $arsip->delete();

        return redirect()->route('cs.arsip.index')->with('success', 'Arsip berhasil dihapus.');
    }

    public function download($id)
    {
        $arsip = $this->arsipSaya()->findOrFail($id);
        $downloadName = $this->safeOriginalName($arsip->nama_file);

        if (Storage::disk('local')->exists($arsip->path_file)) {
            return Storage::disk('local')->download($arsip->path_file, $downloadName, [
                'Cache-Control' => 'no-store, private',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        // Kompatibilitas untuk arsip lama yang masih tersimpan di public/storage.
        if (Storage::disk('public')->exists($arsip->path_file)) {
            return Storage::disk('public')->download($arsip->path_file, $downloadName, [
                'Cache-Control' => 'no-store, private',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        return back()->with('error', 'File fisik tidak ditemukan. Hubungi Admin Sistem bila arsip lama perlu dipulihkan.');
    }

    public function exportExcel(SimpleExcelExport $excel)
    {
        $arsips = $this->arsipSaya()->orderByDesc('tanggal_upload')->get();
        $rows = [['DAFTAR ARSIP DIGITAL SIBERKAS'], ['Dicetak', now()->format('d/m/Y H:i')], [], ['No', 'Nasabah', 'Layanan', 'Jenis Dokumen', 'Nama File', 'Tanggal Upload', 'Status']];

        foreach ($arsips as $index => $arsip) {
            $rows[] = [
                $index + 1,
                $arsip->berkas?->nasabah?->nama_nasabah ?? '-',
                $arsip->berkas?->jenis_layanan ?? '-',
                $arsip->jenis_dokumen,
                $arsip->nama_file,
                optional($arsip->tanggal_upload)->format('d-m-Y') ?? '-',
                $arsip->status_arsip,
            ];
        }

        return $excel->download('arsip-digital-siberkas-' . now()->format('Ymd-His') . '.xlsx', $rows);
    }

    public function exportPdf(SimplePdfExport $pdf)
    {
        $arsips = $this->arsipSaya()->orderByDesc('tanggal_upload')->get();
        return $pdf->download(
            'arsip-digital-siberkas-' . now()->format('Ymd-His') . '.pdf',
            'Daftar Arsip Digital',
            'Dibuat: ' . now()->format('d/m/Y H:i'),
            ['Jumlah Arsip' => $arsips->count(), 'Status Aktif' => $arsips->where('status_arsip', 'Aktif')->count()],
            ['No', 'Nasabah', 'Layanan', 'Dokumen', 'Nama File', 'Upload', 'Status'],
            $arsips->map(fn ($arsip, $index) => [
                $index + 1,
                $arsip->berkas?->nasabah?->nama_nasabah ?? '-',
                $arsip->berkas?->jenis_layanan ?? '-',
                $arsip->jenis_dokumen,
                $arsip->nama_file,
                optional($arsip->tanggal_upload)->format('d-m-Y') ?? '-',
                $arsip->status_arsip,
            ])->all()
        );
    }

    public function perBerkas($berkasId)
    {
        $berkas = $this->berkasSaya()->findOrFail($berkasId);
        $arsips = $berkas->arsips()->orderByDesc('tanggal_upload')->get();
        return view('cs.arsip.perberkas', compact('berkas', 'arsips'));
    }

    private function validateData(Request $request, bool $isCreate): array
    {
        return $request->validate([
            'berkas_id' => ['required', 'integer', 'exists:berkas,id'],
            'jenis_dokumen' => ['required', 'string', 'min:2', 'max:100'],
            'tanggal_upload' => [$isCreate ? 'nullable' : 'required', 'date', 'before_or_equal:today'],
            'file' => [$isCreate ? 'required' : 'nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
        ]);
    }

    private function saveFile($file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $storedName = Str::uuid() . '.' . $extension;
        $path = $file->storeAs('private/arsip', $storedName, 'local');

        return [
            'nama_file' => $this->safeOriginalName($file->getClientOriginalName()),
            'path_file' => $path,
        ];
    }

    private function deleteFile(?string $path): void
    {
        if (!$path) return;
        if (Storage::disk('local')->exists($path)) Storage::disk('local')->delete($path);
        if (Storage::disk('public')->exists($path)) Storage::disk('public')->delete($path);
    }

    private function safeOriginalName(string $name): string
    {
        $name = str_replace(["\r", "\n", "\0"], '', basename($name));
        return Str::limit($name, 150, '');
    }
}
