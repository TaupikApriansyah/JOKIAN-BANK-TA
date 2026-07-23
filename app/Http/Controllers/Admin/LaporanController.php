<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berkas;
use App\Models\Nasabah;
use App\Models\TransaksiAdministrasi;
use App\Services\SimpleExcelExport;
use App\Services\SimplePdfExport;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.laporan.index', $this->makeReport($request));
    }

    public function exportExcel(Request $request, SimpleExcelExport $excel)
    {
        $data = $this->makeReport($request);

        return $excel->download(
            'laporan-admin-siberkas-' . now()->format('Ymd-His') . '.xlsx',
            $this->excelRows($data)
        );
    }

    public function exportPdf(Request $request, SimplePdfExport $pdf)
    {
        $data = $this->makeReport($request);

        return $pdf->download(
            'laporan-admin-siberkas-' . now()->format('Ymd-His') . '.pdf',
            'Laporan Operasional dan Transaksi',
            'Periode: ' . $data['periodeLabel'] . ' | Dibuat: ' . now()->format('d/m/Y H:i'),
            [
                'Nasabah Terdaftar' => $data['totalNasabah'],
                'Berkas Masuk' => $data['totalBerkas'],
                'Berkas Selesai' => $data['berkasSelesai'],
                'SLA Hampir Jatuh Tempo' => $data['slaHampir'],
                'SLA Terlambat' => $data['slaTerlambat'],
                'Pemasukan Diposting' => $this->rupiah($data['pemasukan']),
                'Pengeluaran Diposting' => $this->rupiah($data['pengeluaran']),
                'Saldo Administratif' => $this->rupiah($data['saldo']),
            ],
            ['Tanggal', 'Nasabah', 'Berkas', 'Arah', 'Kategori', 'Nominal', 'Status'],
            $data['transactions']->map(fn ($transaction) => [
                optional($transaction->tanggal_transaksi)->format('d-m-Y') ?? '-',
                optional(optional($transaction->berkas)->nasabah)->nama_nasabah ?? '-',
                optional($transaction->berkas)->jenis_layanan ?? '-',
                $transaction->arah_transaksi ?? '-',
                $transaction->kategori ?? '-',
                $this->rupiah($transaction->nominal),
                $transaction->status_transaksi ?? '-',
            ])->all()
        );
    }

    private function makeReport(Request $request): array
    {
        $request->validate([
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['nullable', 'in:Semua,Belum Dibayar,Menunggu Verifikasi,Lunas,Ditolak,Diposting'],
        ]);

        $start = $request->input('tanggal_mulai');
        $end = $request->input('tanggal_selesai');
        $status = $request->input('status', 'Semua');
        $today = Carbon::today();

        $transactions = $this->transactionQuery($start, $end)
            ->when($status !== 'Semua', fn ($query) => $query->where('status_transaksi', $status))
            ->orderByDesc('tanggal_transaksi')
            ->get();

        $postedQuery = $this->transactionQuery($start, $end)->where('status_transaksi', 'Diposting');
        $pemasukan = (clone $postedQuery)->where('arah_transaksi', 'Pemasukan')->sum('nominal');
        $pengeluaran = (clone $postedQuery)->where('arah_transaksi', 'Pengeluaran')->sum('nominal');

        $berkasQuery = Berkas::query()
            ->when($start, fn ($query) => $query->whereDate('tanggal_masuk', '>=', $start))
            ->when($end, fn ($query) => $query->whereDate('tanggal_masuk', '<=', $end));

        $totalBerkas = (clone $berkasQuery)->count();
        $berkasSelesai = (clone $berkasQuery)->where('status_berkas', 'Selesai')->count();
        $statusSummary = [
            'Diterima' => (clone $berkasQuery)->where('status_berkas', 'Diterima')->count(),
            'Diproses' => (clone $berkasQuery)->where('status_berkas', 'Diproses')->count(),
            'Selesai' => $berkasSelesai,
        ];

        $slaBase = Berkas::with('nasabah')->where('status_berkas', '!=', 'Selesai')->whereNotNull('estimasi_selesai');
        $slaHampir = (clone $slaBase)->whereBetween('estimasi_selesai', [$today, $today->copy()->addDays(2)])->count();
        $slaTerlambat = (clone $slaBase)->whereDate('estimasi_selesai', '<', $today)->count();
        $slaList = (clone $slaBase)->whereDate('estimasi_selesai', '<=', $today->copy()->addDays(2))->orderBy('estimasi_selesai')->take(5)->get();

        $menungguVerifikasi = TransaksiAdministrasi::whereIn('status_transaksi', ['Belum Dibayar', 'Menunggu Verifikasi', 'Lunas'])
            ->when($start, fn ($query) => $query->whereDate('tanggal_transaksi', '>=', $start))
            ->when($end, fn ($query) => $query->whereDate('tanggal_transaksi', '<=', $end))
            ->count();

        $categorySummary = (clone $postedQuery)->get()
            ->groupBy(fn ($transaction) => $transaction->kategori ?: 'Lainnya')
            ->map(fn ($items) => $items->sum('nominal'))
            ->sortDesc();

        $monthlySummary = $this->monthlySummary($start, $end);
        $monthlyMax = max(1, collect($monthlySummary)->flatMap(fn ($row) => [$row['pemasukan'], $row['pengeluaran']])->max());

        return compact(
            'transactions', 'totalBerkas', 'berkasSelesai', 'statusSummary', 'slaHampir', 'slaTerlambat',
            'slaList', 'pemasukan', 'pengeluaran', 'menungguVerifikasi', 'categorySummary', 'monthlySummary',
            'monthlyMax', 'start', 'end', 'status'
        ) + [
            'totalNasabah' => Nasabah::count(),
            'saldo' => $pemasukan - $pengeluaran,
            'periodeLabel' => $this->periodLabel($start, $end),
        ];
    }

    private function excelRows(array $data): array
    {
        $rows = [
            ['LAPORAN OPERASIONAL DAN TRANSAKSI SIBERKAS'],
            ['Periode', $data['periodeLabel']],
            [],
            ['RINGKASAN OPERASIONAL', 'JUMLAH'],
            ['Nasabah Terdaftar', $data['totalNasabah']],
            ['Berkas Masuk', $data['totalBerkas']],
            ['Berkas Selesai', $data['berkasSelesai']],
            ['SLA Hampir Jatuh Tempo', $data['slaHampir']],
            ['SLA Terlambat', $data['slaTerlambat']],
            [],
            ['RINGKASAN TRANSAKSI DIPOSTING', 'NOMINAL'],
            ['Pemasukan Diposting', $this->rupiah($data['pemasukan'])],
            ['Pengeluaran Diposting', $this->rupiah($data['pengeluaran'])],
            ['Saldo Administratif', $this->rupiah($data['saldo'])],
            ['Menunggu Verifikasi', $data['menungguVerifikasi']],
            [],
            ['DETAIL TRANSAKSI'],
            ['Tanggal', 'Nasabah', 'Berkas', 'Arah', 'Kategori', 'Nominal', 'Status'],
        ];

        foreach ($data['transactions'] as $transaction) {
            $rows[] = [
                optional($transaction->tanggal_transaksi)->format('d-m-Y') ?? '-',
                optional(optional($transaction->berkas)->nasabah)->nama_nasabah ?? '-',
                optional($transaction->berkas)->jenis_layanan ?? '-',
                $transaction->arah_transaksi ?? '-',
                $transaction->kategori ?? '-',
                $this->rupiah($transaction->nominal),
                $transaction->status_transaksi ?? '-',
            ];
        }

        return $rows;
    }

    private function monthlySummary(?string $start, ?string $end): array
    {
        $filterStart = $start ? Carbon::parse($start)->startOfDay() : null;
        $filterEnd = $end ? Carbon::parse($end)->endOfDay() : null;
        $endDate = ($filterEnd ?: now())->copy()->startOfMonth();
        $startDate = $filterStart ? $filterStart->copy()->startOfMonth() : $endDate->copy()->subMonths(5);
        if ($startDate->diffInMonths($endDate) > 11) $startDate = $endDate->copy()->subMonths(11);

        $rows = [];
        for ($cursor = $startDate->copy(); $cursor->lte($endDate); $cursor->addMonth()) {
            $monthStart = $cursor->copy()->startOfMonth();
            $monthEnd = $cursor->copy()->endOfMonth();
            $queryStart = $filterStart && $filterStart->gt($monthStart) ? $filterStart : $monthStart;
            $queryEnd = $filterEnd && $filterEnd->lt($monthEnd) ? $filterEnd : $monthEnd;
            $query = TransaksiAdministrasi::where('status_transaksi', 'Diposting')->whereBetween('tanggal_transaksi', [$queryStart->toDateString(), $queryEnd->toDateString()]);
            $rows[] = [
                'label' => $cursor->translatedFormat('M Y'),
                'pemasukan' => (float) (clone $query)->where('arah_transaksi', 'Pemasukan')->sum('nominal'),
                'pengeluaran' => (float) (clone $query)->where('arah_transaksi', 'Pengeluaran')->sum('nominal'),
            ];
        }
        return $rows;
    }

    private function transactionQuery(?string $start, ?string $end)
    {
        return TransaksiAdministrasi::with('berkas.nasabah')
            ->when($start, fn ($query) => $query->whereDate('tanggal_transaksi', '>=', $start))
            ->when($end, fn ($query) => $query->whereDate('tanggal_transaksi', '<=', $end));
    }

    private function periodLabel(?string $start, ?string $end): string
    {
        $from = $start ? Carbon::parse($start)->format('d/m/Y') : 'Awal data';
        $to = $end ? Carbon::parse($end)->format('d/m/Y') : 'Sekarang';
        return $from . ' s.d. ' . $to;
    }

    private function rupiah($amount): string
    {
        return 'Rp ' . number_format((float) $amount, 0, ',', '.');
    }
}
