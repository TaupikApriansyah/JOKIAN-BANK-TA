<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id', 'desc')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        User::create([...$data, 'password' => Hash::make($data['password'])]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $this->validateData($request, $user->id, false);

        if ($user->id === auth()->id() && ($data['role'] !== $user->role || $data['status'] !== 'aktif')) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan atau mengubah role akun sendiri.');
        }

        if ($this->removesLastActiveAdmin($user, $data['role'], $data['status'])) {
            return back()->with('error', 'Sistem harus memiliki minimal satu Admin aktif.');
        }

        unset($data['password']);
        $user->update($data);
        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) return back()->with('error', 'Akun yang sedang digunakan tidak dapat dihapus.');
        if ($this->removesLastActiveAdmin($user, 'non-admin', 'nonaktif')) return back()->with('error', 'Sistem harus memiliki minimal satu Admin aktif.');

        $hasRelatedData = $user->berkas()->exists() || $user->jurnalDiposting()->exists() || Nasabah::where('created_by', $user->id)->exists();
        if ($hasRelatedData) {
            return back()->with('error', 'User memiliki data terkait. Ubah status menjadi nonaktif, jangan dihapus.');
        }

        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }

    private function validateData(Request $request, ?int $userId = null, bool $withPassword = true): array
    {
        $rules = [
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'role' => ['required', Rule::in(['admin', 'cs', 'akuntan'])],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ];

        if ($withPassword) $rules['password'] = ['required', 'string', 'min:8', 'max:100'];
        return $request->validate($rules);
    }

    private function removesLastActiveAdmin(User $user, string $newRole, string $newStatus): bool
    {
        if ($user->role !== 'admin' || $user->status !== 'aktif') return false;
        if ($newRole === 'admin' && $newStatus === 'aktif') return false;
        return User::where('role', 'admin')->where('status', 'aktif')->count() <= 1;
    }
}
