<?php

return [
    'sla' => [
        'near_percent' => (int) env('BANK_SLA_NEAR_PERCENT', 20),
        'near_minutes' => (int) env('BANK_SLA_NEAR_MINUTES', 60),
    ],

    'archives' => [
        'retention_years' => (int) env('BANK_DOCUMENT_RETENTION_YEARS', 5),
    ],

    'transaction_categories' => [
        'Biaya Penggantian Kartu' => [
            'debit_account' => '101.00.00 Kas (Teller)',
            'credit_account' => '401.01.00 Pendapatan Biaya Penggantian Kartu',
        ],
        'Biaya Materai' => [
            'debit_account' => '101.00.00 Kas (Teller)',
            'credit_account' => '401.02.00 Pendapatan Biaya Materai',
        ],
        'Biaya Cetak Rekening Koran' => [
            'debit_account' => '101.00.00 Kas (Teller)',
            'credit_account' => '401.03.00 Pendapatan Cetak Rekening Koran',
        ],
        'Biaya Administrasi Layanan' => [
            'debit_account' => '101.00.00 Kas (Teller)',
            'credit_account' => '401.05.00 Pendapatan Biaya Administrasi',
        ],
        'Biaya Layanan Lainnya' => [
            'debit_account' => '101.00.00 Kas (Teller)',
            'credit_account' => '401.99.00 Pendapatan Layanan Lainnya',
        ],
    ],
];
