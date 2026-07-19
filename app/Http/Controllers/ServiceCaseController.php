<?php

namespace App\Http\Controllers;

use App\Enums\CaseStatus;
use App\Enums\SlaStatus;
use App\Enums\TransactionStatus;
use App\Http\Requests\StoreServiceCaseRequest;
use App\Models\Customer;
use App\Models\ServiceCase;
use App\Models\ServiceType;
use App\Services\AuditLogger;
use App\Services\ReferenceNumberService;
use App\Services\SlaService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ServiceCaseController extends Controller
{
    public function index(Request $request, SlaService $sla): View
    {
        $sla->refreshOpenCases();
        $query = ServiceCase::query()->with(['customer', 'serviceType', 'assignedTo'])->latest('received_at');

        if (!$request->user()->isAdmin()) {
            $query->where('assigned_to', $request->user()->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->value());
        }
        if ($request->filled('sla_status')) {
            $query->where('sla_status', $request->string('sla_status')->value());
        }
        if ($search = $request->string('q')->trim()->value()) {
            $query->where(fn ($builder) => $builder
                ->where('file_number', 'like', "%{$search}%")
                ->orWhereHas('customer', fn ($customer) => $customer->where('name', 'like', "%{$search}%")));
        }

        return view('cases.index', ['cases' => $query->paginate(15)->withQueryString()]);
    }

    public function create(Request $request): View
    {
        $customers = Customer::query()
            ->where('assigned_to', $request->user()->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedCustomerId = $request->integer('customer');
        if ($selectedCustomerId && !$customers->contains('id', $selectedCustomerId)) {
            // Never allow a Maker to create a case for another CS's customer.
            $selectedCustomerId = null;
        }

        return view('cases.create', [
            'customers' => $customers,
            'serviceTypes' => ServiceType::query()->where('is_active', true)->orderBy('name')->get(),
            'selectedCustomerId' => $selectedCustomerId,
        ]);
    }

    public function store(StoreServiceCaseRequest $request, ReferenceNumberService $references, AuditLogger $audit): RedirectResponse
    {
        abort_unless($request->user()->isCustomerService(), 403);
        $customer = Customer::findOrFail($request->integer('customer_id'));
        abort_unless($customer->assigned_to === $request->user()->id && $customer->is_active, 403);

        $serviceType = ServiceType::query()->where('is_active', true)->findOrFail($request->integer('service_type_id'));
        $receivedAt = Carbon::parse($request->input('received_at'));

        $serviceCase = DB::transaction(function () use ($request, $references, $customer, $serviceType, $receivedAt) {
            $serviceCase = ServiceCase::create([
                'file_number' => 'PENDING-'.Str::uuid(),
                'customer_id' => $customer->id,
                'service_type_id' => $serviceType->id,
                'assigned_to' => $request->user()->id,
                'created_by' => $request->user()->id,
                'status' => CaseStatus::MenungguDokumen,
                'received_at' => $receivedAt,
                'due_at' => $receivedAt->copy()->addHours($serviceType->sla_hours),
                'notes' => $request->input('notes'),
            ]);

            $serviceCase->update(['file_number' => $references->fileNumber($serviceCase)]);

            return $serviceCase;
        });

        $audit->log($request, 'service_case', 'create', $serviceCase, null, $this->auditValues($serviceCase), 'Berkas layanan dibuat oleh Maker.');

        return redirect()->route('cases.show', $serviceCase)->with('success', 'Berkas berhasil dibuat. Lengkapi dokumen wajib untuk memulai proses layanan.');
    }

    public function show(Request $request, ServiceCase $serviceCase, SlaService $sla): View
    {
        $this->authorizeRead($request, $serviceCase);
        $serviceCase = $sla->refresh($serviceCase);
        $serviceCase->load([
            'customer', 'serviceType', 'assignedTo', 'documents.uploadedBy',
            'transactions.createdBy', 'transactions.verifiedBy', 'transactions.journals', 'transactions.correctionRequests',
        ]);

        return view('cases.show', compact('serviceCase'));
    }

    public function edit(Request $request, ServiceCase $serviceCase): View
    {
        $this->authorizeEditable($request, $serviceCase);
        return view('cases.edit', [
            'serviceCase' => $serviceCase,
            'customers' => Customer::query()->where('assigned_to', $request->user()->id)->where('is_active', true)->orderBy('name')->get(),
            'serviceTypes' => ServiceType::query()->where('is_active', true)->orWhereKey($serviceCase->service_type_id)->orderBy('name')->get(),
        ]);
    }

    public function update(StoreServiceCaseRequest $request, ServiceCase $serviceCase, AuditLogger $audit): RedirectResponse
    {
        $this->authorizeEditable($request, $serviceCase);
        $customer = Customer::query()->where('assigned_to', $request->user()->id)->where('is_active', true)->findOrFail($request->integer('customer_id'));
        $serviceType = ServiceType::query()->where('is_active', true)->findOrFail($request->integer('service_type_id'));
        $receivedAt = Carbon::parse($request->input('received_at'));
        $before = $this->auditValues($serviceCase);

        $serviceCase->update([
            'customer_id' => $customer->id,
            'service_type_id' => $serviceType->id,
            'received_at' => $receivedAt,
            'due_at' => $receivedAt->copy()->addHours($serviceType->sla_hours),
            'notes' => $request->input('notes'),
        ]);
        $audit->log($request, 'service_case', 'update', $serviceCase, $before, $this->auditValues($serviceCase), 'Berkas draft diperbarui oleh Maker.');

        return redirect()->route('cases.show', $serviceCase)->with('success', 'Berkas berhasil diperbarui.');
    }

    public function destroy(Request $request, ServiceCase $serviceCase, AuditLogger $audit): RedirectResponse
    {
        $this->authorizeEditable($request, $serviceCase);
        abort_if($serviceCase->documents()->exists() || $serviceCase->transactions()->exists(), 422, 'Berkas yang sudah memiliki dokumen atau transaksi tidak dapat dihapus. Gunakan Tolak Berkas agar jejak audit tetap tersimpan.');
        $before = $this->auditValues($serviceCase);
        $audit->log($request, 'service_case', 'delete_draft', $serviceCase, $before, null, 'Maker menghapus berkas draft tanpa dokumen dan transaksi.');
        $serviceCase->delete();

        return redirect()->route('cases.index')->with('success', 'Berkas draft berhasil dihapus.');
    }

    public function reject(Request $request, ServiceCase $serviceCase, AuditLogger $audit): RedirectResponse
    {
        $this->authorizeMaker($request, $serviceCase);
        abort_if($serviceCase->status === CaseStatus::Selesai, 422, 'Berkas yang sudah selesai tidak dapat ditolak.');
        $request->validate(['reason' => ['required', 'string', 'max:500']]);
        $before = $this->auditValues($serviceCase);
        $notes = trim(($serviceCase->notes ? $serviceCase->notes."\n\n" : '').'[DITOLAK] '.$request->string('reason')->trim());
        $serviceCase->update([
            'status' => CaseStatus::Ditolak,
            'sla_status' => SlaStatus::Selesai,
            'completed_at' => now(),
            'notes' => $notes,
        ]);
        $audit->log($request, 'service_case', 'reject', $serviceCase, $before, $this->auditValues($serviceCase), 'Berkas ditolak dengan alasan: '.$request->string('reason')->trim());

        return back()->with('success', 'Berkas ditolak dan SLA dihentikan.');
    }

    public function process(Request $request, ServiceCase $serviceCase, AuditLogger $audit): RedirectResponse
    {
        $this->authorizeMaker($request, $serviceCase);
        if (!$serviceCase->hasAllRequiredDocuments()) {
            return back()->with('error', 'Dokumen wajib belum lengkap. Berkas belum dapat diproses.');
        }
        $before = $this->auditValues($serviceCase);
        $serviceCase->update(['status' => CaseStatus::Diproses]);
        $audit->log($request, 'service_case', 'process', $serviceCase, $before, $this->auditValues($serviceCase), 'Berkas mulai diproses.');

        return back()->with('success', 'Berkas sekarang berstatus Diproses.');
    }

    public function complete(Request $request, ServiceCase $serviceCase, AuditLogger $audit): RedirectResponse
    {
        $this->authorizeMaker($request, $serviceCase);
        if (!$serviceCase->hasAllRequiredDocuments()) {
            return back()->with('error', 'Arsip belum lengkap. Lengkapi dokumen sebelum menutup berkas.');
        }
        $unresolved = [TransactionStatus::Draft->value, TransactionStatus::MenungguVerifikasi->value, TransactionStatus::Dikembalikan->value];
        if ($serviceCase->transactions()->whereIn('status', $unresolved)->exists()) {
            return back()->with('error', 'Masih ada transaksi draft, dikembalikan, atau menunggu verifikasi Admin.');
        }
        if ($serviceCase->transactions()->whereHas('correctionRequests', fn ($query) => $query->where('status', 'menunggu_verifikasi'))->exists()) {
            return back()->with('error', 'Masih ada permintaan koreksi transaksi yang menunggu verifikasi Admin.');
        }

        $before = $this->auditValues($serviceCase);
        $serviceCase->update(['status' => CaseStatus::Selesai, 'sla_status' => SlaStatus::Selesai, 'completed_at' => now()]);
        $audit->log($request, 'service_case', 'complete', $serviceCase, $before, $this->auditValues($serviceCase), 'Berkas ditutup dan SLA dihentikan.');

        return back()->with('success', 'Berkas berhasil ditutup.');
    }

    private function authorizeRead(Request $request, ServiceCase $serviceCase): void
    {
        abort_unless($request->user()->isAdmin() || $serviceCase->assigned_to === $request->user()->id, 403);
    }
    private function authorizeMaker(Request $request, ServiceCase $serviceCase): void
    {
        abort_unless($request->user()->isCustomerService() && $serviceCase->assigned_to === $request->user()->id, 403);
    }
    private function authorizeEditable(Request $request, ServiceCase $serviceCase): void
    {
        $this->authorizeMaker($request, $serviceCase);
        abort_unless(in_array($serviceCase->status, [CaseStatus::Baru, CaseStatus::MenungguDokumen], true), 422, 'Hanya berkas baru atau menunggu dokumen yang dapat diubah atau dihapus.');
    }
    /** @return array<string, mixed> */
    private function auditValues(ServiceCase $serviceCase): array
    {
        return ['file_number' => $serviceCase->file_number, 'customer_id' => $serviceCase->customer_id, 'service_type_id' => $serviceCase->service_type_id, 'status' => $serviceCase->status->value, 'received_at' => optional($serviceCase->received_at)->toDateTimeString(), 'due_at' => optional($serviceCase->due_at)->toDateTimeString()];
    }
}
