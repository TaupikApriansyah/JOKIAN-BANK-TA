<?php

namespace App\Http\Controllers;

use App\Enums\CaseStatus;
use App\Http\Requests\StoreDocumentRequest;
use App\Models\CaseDocument;
use App\Models\ServiceCase;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CaseDocumentController extends Controller
{
    public function store(StoreDocumentRequest $request, ServiceCase $serviceCase, AuditLogger $audit): RedirectResponse
    {
        $this->authorizeMaker($request, $serviceCase);
        $file = $request->file('document');
        $path = $file->store("private/archives/{$serviceCase->file_number}", 'local');
        $document = CaseDocument::create([
            'service_case_id' => $serviceCase->id,
            'document_type' => $request->string('document_type')->trim(),
            'original_name' => $file->getClientOriginalName(),
            'storage_path' => $path,
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
            'size_bytes' => $file->getSize(),
            'uploaded_by' => $request->user()->id,
            'retention_until' => now()->addYears((int) config('bank.archives.retention_years', 5))->toDateString(),
        ]);

        if ($serviceCase->hasAllRequiredDocuments() && $serviceCase->status === CaseStatus::MenungguDokumen) {
            $serviceCase->update(['status' => CaseStatus::Baru]);
        }
        $audit->log($request, 'archive', 'create_upload', $document, null, $this->auditValues($document), 'Dokumen berkas diunggah.');
        return back()->with('success', 'Dokumen berhasil diunggah.');
    }

    public function edit(Request $request, CaseDocument $document): View
    {
        $this->authorizeMaker($request, $document->serviceCase);
        return view('documents.edit', compact('document'));
    }

    public function update(Request $request, CaseDocument $document, AuditLogger $audit): RedirectResponse
    {
        $this->authorizeMaker($request, $document->serviceCase);
        abort_if($document->serviceCase->status === CaseStatus::Selesai, 422, 'Dokumen pada berkas selesai tidak dapat diubah.');
        $validated = $request->validate(['document_type' => ['required', 'string', 'max:150'], 'document' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx']]);
        $before = $this->auditValues($document);
        $data = ['document_type' => $validated['document_type']];
        if ($request->hasFile('document')) {
            Storage::disk('local')->delete($document->storage_path);
            $file = $request->file('document');
            $data += ['storage_path' => $file->store("private/archives/{$document->serviceCase->file_number}", 'local'), 'original_name' => $file->getClientOriginalName(), 'mime_type' => $file->getMimeType() ?: 'application/octet-stream', 'size_bytes' => $file->getSize()];
        }
        $document->update($data);
        $audit->log($request, 'archive', 'update', $document, $before, $this->auditValues($document), 'Dokumen arsip diperbarui.');
        return redirect()->route('cases.show', $document->serviceCase)->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroy(Request $request, CaseDocument $document, AuditLogger $audit): RedirectResponse
    {
        $this->authorizeMaker($request, $document->serviceCase);
        abort_if($document->serviceCase->status === CaseStatus::Selesai, 422, 'Dokumen pada berkas selesai tidak dapat dihapus.');
        $before = $this->auditValues($document);
        Storage::disk('local')->delete($document->storage_path);
        $audit->log($request, 'archive', 'delete', $document, $before, null, 'Maker menghapus dokumen arsip sebelum berkas selesai.');
        $document->delete();
        return back()->with('success', 'Dokumen berhasil dihapus.');
    }

    public function download(Request $request, CaseDocument $document, AuditLogger $audit): StreamedResponse
    {
        $serviceCase = $document->serviceCase;
        abort_unless($request->user()->isAdmin() || $serviceCase->assigned_to === $request->user()->id, 403);
        abort_unless(Storage::disk('local')->exists($document->storage_path), 404);
        $audit->log($request, 'archive', 'download', $document, null, null, 'Dokumen diunduh.');
        return Storage::disk('local')->download($document->storage_path, $document->original_name);
    }

    private function authorizeMaker(Request $request, ServiceCase $serviceCase): void
    {
        abort_unless($request->user()->isCustomerService() && $serviceCase->assigned_to === $request->user()->id, 403);
    }
    /** @return array<string,mixed> */
    private function auditValues(CaseDocument $document): array
    {
        return ['document_type' => $document->document_type, 'original_name' => $document->original_name, 'size_bytes' => $document->size_bytes, 'retention_until' => optional($document->retention_until)->toDateString()];
    }
}
