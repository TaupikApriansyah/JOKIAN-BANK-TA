$ErrorActionPreference = "Stop"
Set-Location $PSScriptRoot

if (-not (Test-Path "artisan")) {
    Write-Host "File ini harus diletakkan di folder utama proyek, sejajar dengan artisan." -ForegroundColor Red
    exit 1
}

$source = Join-Path $PSScriptRoot "database\migrations"
$disabled = Join-Path $PSScriptRoot "database\migrations_disabled"
New-Item -ItemType Directory -Force -Path $disabled | Out-Null

$conflictingMigrations = @(
    "0001_01_01_000000_create_users_table.php",
    "0001_01_01_000001_create_cache_table.php",
    "0001_01_01_000002_create_jobs_table.php",
    "2026_07_03_000100_create_customers_table.php",
    "2026_07_03_000110_create_service_types_table.php",
    "2026_07_03_000120_create_service_cases_table.php",
    "2026_07_03_000130_create_case_documents_table.php",
    "2026_07_03_000140_create_administrative_transactions_table.php",
    "2026_07_03_000150_create_journal_entries_table.php",
    "2026_07_03_000160_create_daily_reconciliations_table.php",
    "2026_07_03_000170_create_audit_logs_table.php",
    "2026_07_03_000180_create_export_logs_table.php",
    "2026_07_03_000190_create_sla_notifications_table.php",
    "2026_07_03_000200_create_transaction_correction_requests_table.php",
    "2026_07_19_000210_add_accountant_role_and_journal_workflow.php"
)

Write-Host "Menonaktifkan migration yang bentrok..." -ForegroundColor Cyan
foreach ($file in $conflictingMigrations) {
    $from = Join-Path $source $file
    $to = Join-Path $disabled $file

    if (Test-Path $from) {
        Move-Item -Force $from $to
        Write-Host "Dipindahkan: $file"
    }
}

php artisan optimize:clear

Write-Host "" 
Write-Host "PERINGATAN: proses berikut menghapus semua tabel dan data di database aktif." -ForegroundColor Yellow
$answer = Read-Host "Ketik RESET untuk membuat ulang database"
if ($answer -ne "RESET") {
    Write-Host "Migration sudah diperbaiki. Reset database dibatalkan." -ForegroundColor Yellow
    exit 0
}

php artisan migrate:fresh --seed

if ($LASTEXITCODE -ne 0) {
    Write-Host "Migrasi masih gagal. Kirim output error terbaru." -ForegroundColor Red
    exit $LASTEXITCODE
}

Write-Host "" 
Write-Host "Database berhasil dibuat ulang." -ForegroundColor Green
Write-Host "Admin   : admin@gmail.com / admin123"
Write-Host "CS      : cs@gmail.com / cs123"
Write-Host "Akuntan : akuntan@gmail.com / akuntan123"
