<?php

namespace App\Http\Controllers\Akuntan;

use App\Http\Controllers\Controller;
use App\Models\AkunAkuntansi;
use App\Models\DetailJurnal;
use App\Models\JurnalUmum;
use Illuminate\Http\Request;

class JurnalController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->input('tanggal_mulai');
        $end = $request->input('tanggal_selesai');

        $journals = JurnalUmum::with(['details.akun', 'transaksi.berkas.nasabah', 'user'])
            ->when($start, fn ($query) => $query->whereDate('tanggal_jurnal', '>=', $start))
            ->when($end, fn ($query) => $query->whereDate('tanggal_jurnal', '<=', $end))
            ->orderByDesc('tanggal_jurnal')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('akuntan.jurnal.index', compact('journals', 'start', 'end'));
    }

    public function ledger(Request $request)
    {
        $accounts = AkunAkuntansi::where('status', 'aktif')->orderBy('kode_akun')->get();
        $selectedAccount = $request->input('akun_id', optional($accounts->first())->id);
        $start = $request->input('tanggal_mulai');
        $end = $request->input('tanggal_selesai');
        $account = $selectedAccount ? AkunAkuntansi::find($selectedAccount) : null;

        $rows = collect();
        $saldo = 0;

        if ($account) {
            $rows = DetailJurnal::with('jurnal')
                ->where('akun_id', $account->id)
                ->when($start, fn ($query) => $query->whereHas('jurnal', fn ($jurnal) => $jurnal->whereDate('tanggal_jurnal', '>=', $start)))
                ->when($end, fn ($query) => $query->whereHas('jurnal', fn ($jurnal) => $jurnal->whereDate('tanggal_jurnal', '<=', $end)))
                ->get()
                ->sortBy(function ($detail) {
                    return optional($detail->jurnal)->tanggal_jurnal?->format('Y-m-d') . '-' . str_pad((string) $detail->id, 10, '0', STR_PAD_LEFT);
                })
                ->values();

            $rows = $rows->map(function ($row) use (&$saldo, $account) {
                $change = $account->saldo_normal === 'Debit'
                    ? ((float) $row->debit - (float) $row->kredit)
                    : ((float) $row->kredit - (float) $row->debit);

                $saldo += $change;
                $row->saldo_berjalan = $saldo;

                return $row;
            });
        }

        return view('akuntan.jurnal.ledger', compact('accounts', 'account', 'rows', 'saldo', 'start', 'end'));
    }
}
