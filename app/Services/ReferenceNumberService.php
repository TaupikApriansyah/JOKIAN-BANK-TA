<?php

namespace App\Services;

use App\Models\AdministrativeTransaction;
use App\Models\Customer;
use App\Models\JournalEntry;
use App\Models\ServiceCase;
use Carbon\Carbon;

class ReferenceNumberService
{
    public function customerNumber(Customer $customer): string
    {
        return sprintf('CIF-%08d', $customer->id);
    }

    public function fileNumber(ServiceCase $serviceCase): string
    {
        return sprintf('BRK-%s-%05d', Carbon::now()->format('ym'), $serviceCase->id);
    }

    public function transactionNumber(AdministrativeTransaction $transaction): string
    {
        return sprintf('TRX-%s-%05d', Carbon::now()->format('ym'), $transaction->id);
    }

    public function journalNumber(JournalEntry $entry): string
    {
        return sprintf('JRN-%s-%05d', Carbon::now()->format('ym'), $entry->id);
    }
}
