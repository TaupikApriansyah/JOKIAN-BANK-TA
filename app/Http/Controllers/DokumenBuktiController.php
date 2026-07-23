<?php

namespace App\Http\Controllers;

use App\Models\TransaksiAdministrasi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DokumenBuktiController extends Controller
{
    public function download($id)
    {
        $transaction = TransaksiAdministrasi::with('berkas')->findOrFail($id);
        $user = auth()->user();

        $canAccess = $user->role === 'akuntan'
            || ($user->role === 'cs' && (int) $transaction->berkas?->user_id === (int) $user->id);

        abort_unless($canAccess, 403, 'Akses bukti pembayaran ditolak.');
        abort_unless($transaction->bukti_pembayaran, 404, 'Bukti pembayaran belum tersedia.');

        $downloadName = 'bukti-transaksi-' . $transaction->id . '.' . $this->extension($transaction->bukti_pembayaran);
        if (Storage::disk('local')->exists($transaction->bukti_pembayaran)) {
            return Storage::disk('local')->download($transaction->bukti_pembayaran, $downloadName, $this->headers());
        }

        // Dukungan untuk bukti yang terlanjur diunggah sebelum penyimpanan privat diterapkan.
        if (Storage::disk('public')->exists($transaction->bukti_pembayaran)) {
            return Storage::disk('public')->download($transaction->bukti_pembayaran, $downloadName, $this->headers());
        }

        abort(404, 'File bukti pembayaran tidak ditemukan.');
    }

    private function extension(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($extension, ['pdf', 'jpg', 'jpeg', 'png'], true) ? $extension : 'bin';
    }

    private function headers(): array
    {
        return ['Cache-Control' => 'no-store, private', 'X-Content-Type-Options' => 'nosniff'];
    }
}
