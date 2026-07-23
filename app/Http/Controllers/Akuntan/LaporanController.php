<?php

namespace App\Http\Controllers\Akuntan;

use App\Http\Controllers\Controller;
use App\Models\TransaksiAdministrasi;
use App\Services\SimpleExcelExport;
use App\Services\SimplePdfExport;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        return view('akuntan.laporan.index', $this->makeReport($request));
    }

    public function exportExcel(Request $request, SimpleExcelExport $excel)
    {
        $data = $this->makeReport($request);
        return $excel->download('laporan-akuntansi-siberkas-' . now()->format('Ymd-His') . '.xlsx', $this->excelRows($data));
    }

    public function exportPdf(Request $request, SimplePdfExport $pdf)
    {
        $data = $this->makeReport($request);
        return $pdf->download(
            'laporan-akuntansi-siberkas-' . now()->format('Ymd-His') . '.pdf',
            'Laporan Akuntansi Administratif',
            'Periode: ' . $data['periodeLabel'] . ' | Dibuat: ' . now()->format('d/m/Y H:i'),
            [
                'Pemasukan Diposting' => $this->rupiah($data['pemasukan']),
                'Pengeluaran Diposting' => $this->rupiah($data['pengeluaran']),
                'Saldo Administratif' => $this->rupiah($data['saldo']),
                'Piutang Administratif' => $this->rupiah($data['piutangTotal']),
                'Jumlah Transaksi' => $data['transactions']->count(),
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
        $status = $request->input('status', 'Diposting');

        $transactions = $this->transactionQuery($start, $end)
            ->when($status !== 'Semua', fn ($query) => $query->where('status_transaksi', $status))
            ->orderByDesc('tanggal_transaksi')
            ->get();

        $postedTransactions = $this->transactionQuery($start, $end)->where('status_transaksi', 'Diposting');
        $pemasukan = (clone $postedTransactions)->where('arah_transaksi', 'Pemasukan')->sum('nominal');
        $pengeluaran = (clone $postedTransactions)->where('arah_transaksi', 'Pengeluaran')->sum('nominal');
        $piutang = $this->transactionQuery($start, $end)->where('arah_transaksi', 'Pemasukan')
            ->whereIn('status_transaksi', ['Belum Dibayar', 'Menunggu Verifikasi'])
            ->orderByDesc('tanggal_transaksi')->get();

        $incomeCategories = (clone $postedTransactions)->where('arah_transaksi', 'Pemasukan')->get()
            ->groupBy(fn ($transaction) => $transaction->kategori ?: 'Lainnya')
            ->map(fn ($items) => $items->sum('nominal'))->sortDesc();
        $expenseCategories = (clone $postedTransactions)->where('arah_transaksi', 'Pengeluaran')->get()
            ->groupBy(fn ($transaction) => $transaction->kategori ?: 'Lainnya')
            ->map(fn ($items) => $items->sum('nominal'))->sortDesc();

        return compact('transactions', 'pemasukan', 'pengeluaran', 'piutang', 'incomeCategories', 'expenseCategories', 'start', 'end', 'status') + [
            'saldo' => $pemasukan - $pengeluaran,
            'piutangTotal' => $piutang->sum('nominal'),
            'periodeLabel' => $this->periodLabel($start, $end),
        ];
    }

    private function excelRows(array $data): array
    {
        $rows = [
            ['LAPORAN AKUNTANSI ADMINISTRATIF SIBERKAS'],
            ['Periode', $data['periodeLabel']],
            [],
            ['RINGKASAN TRANSAKSI DIPOSTING', 'NOMINAL'],
            ['Pemasukan', $this->rupiah($data['pemasukan'])],
            ['Pengeluaran', $this->rupiah($data['pengeluaran'])],
            ['Saldo Administratif', $this->rupiah($data['saldo'])],
            ['Piutang Administratif', $this->rupiah($data['piutangTotal'])],
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
