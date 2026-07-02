<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use App\Services\AuditLogger;
use App\Services\ReferenceNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::query()->with('assignedTo')->latest();

        if (!$request->user()->isAdmin()) {
            $query->where('assigned_to', $request->user()->id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->string('status')->value() === 'aktif');
        }

        if ($search = $request->string('q')->trim()->value()) {
            $query->where(fn ($builder) => $builder
                ->where('name', 'like', "%{$search}%")
                ->orWhere('customer_number', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%"));
        }

        return view('customers.index', ['customers' => $query->paginate(12)->withQueryString()]);
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(StoreCustomerRequest $request, ReferenceNumberService $references, AuditLogger $audit): RedirectResponse
    {
        abort_unless($request->user()->isCustomerService(), 403);

        $customer = Customer::create([
            'customer_number' => 'PENDING',
            'name' => $request->string('name')->trim(),
            'nik' => $request->input('nik'),
            'account_number' => $request->input('account_number'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'assigned_to' => $request->user()->id,
        ]);
        $customer->update(['customer_number' => $references->customerNumber($customer)]);

        $audit->log($request, 'customer', 'create', $customer, null, $this->auditValues($customer), 'Data nasabah ditambahkan oleh Maker.');

        return redirect()->route('customers.show', $customer)->with('success', 'Data nasabah berhasil ditambahkan.');
    }

    public function show(Request $request, Customer $customer): View
    {
        $this->authorizeRead($request, $customer);
        $customer->load(['assignedTo', 'serviceCases.serviceType', 'transactions.serviceCase']);

        return view('customers.show', compact('customer'));
    }

    public function edit(Request $request, Customer $customer): View
    {
        $this->authorizeMaker($request, $customer);
        abort_unless($customer->is_active, 422, 'Nasabah nonaktif tidak dapat diubah oleh Maker.');

        return view('customers.edit', compact('customer'));
    }

    public function update(StoreCustomerRequest $request, Customer $customer, AuditLogger $audit): RedirectResponse
    {
        $this->authorizeMaker($request, $customer);
        abort_unless($customer->is_active, 422, 'Nasabah nonaktif tidak dapat diubah oleh Maker.');

        $before = $this->auditValues($customer);
        $customer->update([
            'name' => $request->string('name')->trim(),
            'nik' => $request->input('nik'),
            'account_number' => $request->input('account_number'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
        ]);
        $audit->log($request, 'customer', 'update', $customer, $before, $this->auditValues($customer), 'Data nasabah diperbarui oleh Maker.');

        return redirect()->route('customers.show', $customer)->with('success', 'Data nasabah berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Customer $customer, AuditLogger $audit): RedirectResponse
    {
        $validated = $request->validate(['is_active' => ['required', 'boolean']]);
        $active = (bool) $validated['is_active'];

        if (!$active && $customer->serviceCases()->whereNotIn('status', ['selesai', 'ditolak'])->exists()) {
            return back()->with('error', 'Nasabah masih memiliki berkas aktif dan belum dapat dinonaktifkan.');
        }

        $before = $this->auditValues($customer);
        $customer->update(['is_active' => $active]);
        $audit->log($request, 'customer', $active ? 'activate' : 'deactivate', $customer, $before, $this->auditValues($customer), 'Admin mengubah status aktif data nasabah.');

        return back()->with('success', $active ? 'Nasabah berhasil diaktifkan.' : 'Nasabah berhasil dinonaktifkan.');
    }

    private function authorizeRead(Request $request, Customer $customer): void
    {
        abort_unless($request->user()->isAdmin() || $customer->assigned_to === $request->user()->id, 403);
    }

    private function authorizeMaker(Request $request, Customer $customer): void
    {
        abort_unless($request->user()->isCustomerService() && $customer->assigned_to === $request->user()->id, 403);
    }

    /** @return array<string, mixed> */
    private function auditValues(Customer $customer): array
    {
        return [
            'customer_number' => $customer->customer_number,
            'name' => $customer->name,
            'phone' => $customer->phone,
            'email' => $customer->email,
            'is_active' => $customer->is_active,
        ];
    }
}
