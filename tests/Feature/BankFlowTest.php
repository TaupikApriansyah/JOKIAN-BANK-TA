<?php

namespace Tests\Feature;

use App\Models\AdministrativeTransaction;
use App\Models\Customer;
use App\Models\ServiceCase;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BankFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_cs_cannot_access_admin_transaction_verification_page(): void
    {
        $cs = User::factory()->create(['employee_id' => 'CS-TEST', 'role' => 'cs']);

        $this->actingAs($cs)
            ->get(route('admin.transactions.index'))
            ->assertForbidden();
    }

    public function test_maker_can_create_service_case_and_see_it_in_the_case_list(): void
    {
        $cs = User::factory()->create(['employee_id' => 'CS-CASE', 'role' => 'cs']);
        $type = ServiceType::create(['name' => 'Pembukaan Rekening', 'sla_hours' => 6]);
        $customer = Customer::create(['customer_number' => 'CIF-CASE', 'name' => 'Nasabah Baru', 'assigned_to' => $cs->id, 'is_active' => true]);

        $response = $this->actingAs($cs)->post(route('cases.store'), [
            'customer_id' => $customer->id,
            'service_type_id' => $type->id,
            'received_at' => now()->format('Y-m-d\TH:i'),
            'notes' => 'Berkas awal dibuat untuk pengujian.',
        ]);

        $case = ServiceCase::first();
        $response->assertRedirect(route('cases.show', $case));
        $this->assertDatabaseHas('service_cases', ['id' => $case->id, 'customer_id' => $customer->id, 'assigned_to' => $cs->id]);

        $this->actingAs($cs)
            ->get(route('cases.index'))
            ->assertSee($case->file_number)
            ->assertSee($customer->name);
    }

    public function test_maker_can_save_transaction_as_draft_and_submit_it_for_verification(): void
    {
        $cs = User::factory()->create(['employee_id' => 'CS-TRX', 'role' => 'cs']);
        $type = ServiceType::create(['name' => 'Perubahan Data', 'sla_hours' => 3]);
        $customer = Customer::create(['customer_number' => 'CIF-TRX', 'name' => 'Nasabah Transaksi', 'assigned_to' => $cs->id, 'is_active' => true]);
        $case = ServiceCase::create(['file_number' => 'BRK-TRX', 'customer_id' => $customer->id, 'service_type_id' => $type->id, 'assigned_to' => $cs->id, 'created_by' => $cs->id, 'received_at' => now(), 'due_at' => now()->addHours(3)]);

        $draftResponse = $this->actingAs($cs)->post(route('cases.transactions.store', $case), [
            'category' => array_key_first(config('bank.transaction_categories')),
            'payment_method' => 'Setoran Tunai',
            'amount' => 15000,
            'description' => 'Draft transaksi.',
            'action' => 'draft',
        ]);

        $transaction = AdministrativeTransaction::first();

        $draftResponse->assertRedirect(route('cases.show', $case));
        $this->assertDatabaseHas('administrative_transactions', ['id' => $transaction->id, 'status' => 'draft', 'created_by' => $cs->id]);

        $this->actingAs($cs)
            ->get(route('cases.show', $case))
            ->assertSee($transaction->transaction_number)
            ->assertSee('Draft transaksi.');

        $this->actingAs($cs)
            ->get(route('transactions.index'))
            ->assertSee($transaction->transaction_number);

        $this->actingAs($cs)
            ->post(route('transactions.submit', $transaction))
            ->assertRedirect();

        $this->assertDatabaseHas('administrative_transactions', ['id' => $transaction->id, 'status' => 'menunggu_verifikasi']);
    }

    public function test_maker_cannot_add_transaction_to_another_cs_case(): void
    {
        $cs1 = User::factory()->create(['employee_id' => 'CS-OWN1', 'role' => 'cs']);
        $cs2 = User::factory()->create(['employee_id' => 'CS-OWN2', 'role' => 'cs']);
        $type = ServiceType::create(['name' => 'Layanan Pemblokiran', 'sla_hours' => 2]);
        $customer = Customer::create(['customer_number' => 'CIF-OWN', 'name' => 'Nasabah Orang Lain', 'assigned_to' => $cs1->id, 'is_active' => true]);
        $case = ServiceCase::create(['file_number' => 'BRK-OWN', 'customer_id' => $customer->id, 'service_type_id' => $type->id, 'assigned_to' => $cs1->id, 'created_by' => $cs1->id, 'received_at' => now(), 'due_at' => now()->addHours(2)]);

        $this->actingAs($cs2)
            ->post(route('cases.transactions.store', $case), [
                'category' => array_key_first(config('bank.transaction_categories')),
                'payment_method' => 'Setoran Tunai',
                'amount' => 5000,
                'description' => 'Coba transaksi tidak berhak.',
                'action' => 'draft',
            ])
            ->assertForbidden();
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
