<?php

namespace Database\Seeders;

use App\Enums\CaseStatus;
use App\Enums\SlaStatus;
use App\Enums\TransactionStatus;
use App\Models\AdministrativeTransaction;
use App\Models\Customer;
use App\Models\ServiceCase;
use App\Models\ServiceType;
use App\Models\User;
use App\Services\ReferenceNumberService;
use App\Services\TransactionApprovalService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'employee_id' => 'ADM-001',
            'name' => 'Budi Admin',
            'email' => 'admin@bankx.test',
            'role' => 'admin',
            'password' => Hash::make('password123'),
        ]);

        $cs = User::create([
            'employee_id' => 'CS-001',
            'name' => 'Rina S.',
            'email' => 'rina@bankx.test',
            'role' => 'cs',
            'password' => Hash::make('password123'),
        ]);

        $references = app(ReferenceNumberService::class);
        $serviceTypes = collect([
            ['name' => 'Pembukaan Rekening Giro', 'sla_hours' => 24, 'required_documents' => ['Formulir Pembukaan Rekening', 'Akta Pendirian Perusahaan', 'KTP Penanggung Jawab']],
            ['name' => 'Penggantian Kartu', 'sla_hours' => 4, 'required_documents' => ['Formulir Penggantian Kartu', 'Identitas Nasabah']],
            ['name' => 'Cetak Rekening Koran', 'sla_hours' => 2, 'required_documents' => ['Formulir Permintaan', 'Identitas Nasabah']],
        ])->map(fn (array $type) => ServiceType::create($type));

        $customer = Customer::create([
            'customer_number' => 'PENDING',
            'name' => 'PT. Maju Bersama',
            'nik' => '3273123456781234',
            'account_number' => '1234567890123456',
            'phone' => '081234567890',
            'email' => 'admin@maju-bersama.test',
            'assigned_to' => $cs->id,
        ]);
        $customer->update(['customer_number' => $references->customerNumber($customer)]);

        $case = ServiceCase::create([
            'file_number' => 'PENDING',
            'customer_id' => $customer->id,
            'service_type_id' => $serviceTypes->first()->id,
            'assigned_to' => $cs->id,
            'created_by' => $cs->id,
            'status' => CaseStatus::Diproses,
            'sla_status' => SlaStatus::Mendekati,
            'received_at' => now()->subHours(20),
            'due_at' => now()->addHours(4),
            'notes' => 'Permohonan pembukaan rekening giro untuk payroll.',
        ]);
        $case->update(['file_number' => $references->fileNumber($case)]);

        $pending = AdministrativeTransaction::create([
            'transaction_number' => 'PENDING',
            'service_case_id' => $case->id,
            'customer_id' => $customer->id,
            'created_by' => $cs->id,
            'category' => 'Biaya Materai',
            'payment_method' => 'Setoran Tunai',
            'amount' => 10000,
            'status' => TransactionStatus::MenungguVerifikasi,
            'submitted_at' => now()->subMinutes(20),
            'description' => 'Biaya materai pembukaan rekening giro.',
        ]);
        $pending->update(['transaction_number' => $references->transactionNumber($pending)]);

        $approved = AdministrativeTransaction::create([
            'transaction_number' => 'PENDING',
            'service_case_id' => $case->id,
            'customer_id' => $customer->id,
            'created_by' => $cs->id,
            'category' => 'Biaya Administrasi Layanan',
            'payment_method' => 'Potong Saldo Rekening (Auto-debit)',
            'amount' => 50000,
            'status' => TransactionStatus::MenungguVerifikasi,
            'submitted_at' => now()->subDay(),
            'description' => 'Biaya administrasi pembukaan rekening giro.',
        ]);
        $approved->update(['transaction_number' => $references->transactionNumber($approved)]);
        app(TransactionApprovalService::class)->approve($approved, $admin, 'Data dan nominal sesuai.');
    }
}
