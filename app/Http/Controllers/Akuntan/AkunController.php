<?php

namespace App\Http\Controllers\Akuntan;

use App\Http\Controllers\Controller;
use App\Models\AkunAkuntansi;
use Illuminate\Http\Request;

class AkunController extends Controller
{
    public function index()
    {
        $accounts = AkunAkuntansi::orderBy('kode_akun')->get();
        return view('akuntan.akun.index', compact('accounts'));
    }

    public function store(Request $request)
    {
        AkunAkuntansi::create($this->validateAccount($request));
        return back()->with('success', 'Daftar akun berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $account = AkunAkuntansi::findOrFail($id);
        $data = $this->validateAccount($request, $account->id);
        $account->update($data);

        return back()->with('success', 'Daftar akun berhasil diperbarui.');
    }

    private function validateAccount(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'kode_akun' => 'required|string|max:20|unique:akun_akuntansi,kode_akun,' . $id,
            'nama_akun' => 'required|string|max:100',
            'kelompok' => 'required|in:Aset,Pendapatan,Beban',
            'saldo_normal' => 'required|in:Debit,Kredit',
            'status' => 'required|in:aktif,nonaktif',
        ]);
    }
}
