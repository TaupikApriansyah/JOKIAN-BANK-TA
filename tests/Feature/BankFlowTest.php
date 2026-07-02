<?php

namespace Tests\Feature;

use App\Models\AdministrativeTransaction;
use App\Models\Customer;
use App\Models\ServiceCase;
use App\Models\ServiceType;
use App\Models\User;
use App\Services\ReferenceNumberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BankFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_cs_cannot_access_admin_transaction_verification_page(): void
    {
        $cs = User::factory()->create(['employee_id' => 'CS-TEST', 'role' => 'cs']);
        $this->actingAs($cs)->get(route('admin.transactions.index'))->assertForbidden();
    }

    public function test_admin_can_approve_pending_transaction_and_system_creates_journal(): void
    {
        $admin = User::factory()->create(['employee_id' => 'ADM-TEST', 'role' => 'admin']);
        $cs = User::factory()->create(['employee_id' => 'CS-TEST2', 'role' => 'cs']);
        $type = ServiceType::create(['name' => 'Penggantian Kartu', 'sla_hours' => 4]);
        $customer = Customer::create(['customer_number' => 'CIF-TEST', 'name' => 'Nasabah Uji', 'assigned_to' => $cs->id]);
        $case = ServiceCase::create(['file_number' => 'BRK-TEST', 'customer_id' => $customer->id, 'service_type_id' => $type->id, 'assigned_to' => $cs->id, 'created_by' => $cs->id, 'received_at' => now(), 'due_at' => now()->addHours(4)]);
        $transaction = AdministrativeTransaction::create(['transaction_number' => 'TRX-TEST', 'service_case_id' => $case->id, 'customer_id' => $customer->id, 'created_by' => $cs->id, 'category' => 'Biaya Materai', 'payment_method' => 'Setoran Tunai', 'amount' => 10000, 'status' => 'menunggu_verifikasi', 'submitted_at' => now()]);

        $this->actingAs($admin)->post(route('admin.transactions.verify', $transaction), ['decision' => 'approve', 'note' => 'Sesuai.'])->assertRedirect();
        $this->assertDatabaseHas('administrative_transactions', ['id' => $transaction->id, 'status' => 'disetujui', 'verified_by' => $admin->id]);
        $this->assertDatabaseHas('journal_entries', ['administrative_transaction_id' => $transaction->id, 'amount' => 10000]);
    }

    public function test_maker_cannot_approve_own_transaction_even_if_route_is_attempted(): void
    {
        $cs = User::factory()->create(['employee_id' => 'CS-TEST3', 'role' => 'cs']);
        $type = ServiceType::create(['name' => 'Cetak Rekening Koran', 'sla_hours' => 2]);
        $customer = Customer::create(['customer_number' => 'CIF-TEST2', 'name' => 'Nasabah Uji', 'assigned_to' => $cs->id]);
        $case = ServiceCase::create(['file_number' => 'BRK-TEST2', 'customer_id' => $customer->id, 'service_type_id' => $type->id, 'assigned_to' => $cs->id, 'created_by' => $cs->id, 'received_at' => now(), 'due_at' => now()->addHours(2)]);
        $transaction = AdministrativeTransaction::create(['transaction_number' => 'TRX-TEST2', 'service_case_id' => $case->id, 'customer_id' => $customer->id, 'created_by' => $cs->id, 'category' => 'Biaya Materai', 'payment_method' => 'Setoran Tunai', 'amount' => 10000, 'status' => 'menunggu_verifikasi', 'submitted_at' => now()]);

        $this->actingAs($cs)->post(route('admin.transactions.verify', $transaction), ['decision' => 'approve'])->assertForbidden();
    }
}
