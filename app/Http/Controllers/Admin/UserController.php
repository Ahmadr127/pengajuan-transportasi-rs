<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\SimutuUserSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Parse nama lengkap menjadi first_name, last_name, dan username.
     * - Ambil kata ke-1 dan ke-2 untuk username (nama1.nama2 lowercase)
     * - Jika hanya 1 kata, username = kata itu
     * - Contoh: "M. Rangga Aditya" → username = "m.rangga"
     */
    private function parseNama(string $namaLengkap): array
    {
        // Simpan nama lengkap asli (termasuk koma dan gelar) untuk first_name/last_name
        $namaLengkap = trim($namaLengkap);

        $parts = preg_split('/\s+/', $namaLengkap);

        $firstName = $parts[0] ?? '';
        $lastName  = count($parts) >= 2
            ? implode(' ', array_slice($parts, 1))
            : '';

        // Username: gunakan nama tanpa gelar (potong di koma), ambil kata ke-1 dan ke-2
        $namaUntukUsername = trim(explode(',', $namaLengkap)[0]);
        $partsUsername = preg_split('/\s+/', $namaUntukUsername);

        $w1 = preg_replace('/[^a-z0-9]/', '', strtolower($partsUsername[0] ?? ''));
        $w2 = isset($partsUsername[1]) ? preg_replace('/[^a-z0-9]/', '', strtolower($partsUsername[1])) : null;

        $username = ($w2 !== null && $w2 !== '')
            ? $w1 . '.' . $w2
            : $w1;

        return compact('firstName', 'lastName', 'username');
    }

    public function index(Request $request)
    {
        $query = User::query()->latest();
        $like  = fn($val) => '%' . strtolower($val) . '%';

        if ($request->filled('nama'))       $query->whereRaw('LOWER(CONCAT(first_name, \' \', last_name)) LIKE ?', [$like($request->nama)]);
        if ($request->filled('username'))   $query->whereRaw('LOWER(username) LIKE ?', [$like($request->username)]);
        if ($request->filled('nip'))        $query->whereRaw('LOWER(nip) LIKE ?', [$like($request->nip)]);
        if ($request->filled('unit_kerja')) $query->whereRaw('LOWER(unit_kerja) LIKE ?', [$like($request->unit_kerja)]);
        if ($request->filled('posisi'))     $query->whereRaw('LOWER(posisi_pekerjaan) LIKE ?', [$like($request->posisi)]);
        if ($request->filled('profesi'))    $query->whereRaw('LOWER(profesi) LIKE ?', [$like($request->profesi)]);
        if ($request->filled('jabatan'))    $query->whereRaw('LOWER(jabatan) LIKE ?', [$like($request->jabatan)]);
        if ($request->filled('role'))       $query->whereHas('roles', fn ($q) => $q->where('name', $request->role));

        $users = $query->paginate(10)->withQueryString();
        $roles = Role::query()->orderBy('id')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::query()->orderBy('id')->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string'],
            'unit_kerja' => ['nullable', 'string', 'max:255'],
            'posisi_pekerjaan' => ['nullable', 'string', 'max:255'],
            'profesi' => ['nullable', 'string', 'max:255'],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'role_id' => ['required', 'exists:roles,id'],
            'priority_level' => ['nullable', 'integer', 'in:0,1'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.users.create')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $role = Role::findOrFail($data['role_id']);

        $parsed = $this->parseNama($data['nama_lengkap']);

        // Pastikan username unik, tambah angka mulai dari 2 jika ada duplikat
        $baseUsername = $parsed['username'];
        $username = $baseUsername;
        $i = 2;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $i++;
        }

        $user = User::create([
            'first_name'       => $parsed['firstName'],
            'last_name'        => $parsed['lastName'],
            'username'         => $username,
            'nip'              => $data['nip'] ?? null,
            'password'         => Hash::make($data['password']),
            'unit_kerja'       => $data['unit_kerja'] ?? null,
            'posisi_pekerjaan' => $data['posisi_pekerjaan'] ?? null,
            'profesi'          => $data['profesi'] ?? null,
            'jabatan'          => $data['jabatan'] ?? null,
            'role'             => $role->name,
            'priority_level'   => $data['priority_level'] ?? 0,
        ]);

        $user->syncRolesByName([$role->name]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $roles = Role::query()->orderBy('id')->get();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nama_lengkap' => ['nullable', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string'],
            'unit_kerja' => ['nullable', 'string', 'max:255'],
            'posisi_pekerjaan' => ['nullable', 'string', 'max:255'],
            'profesi' => ['nullable', 'string', 'max:255'],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'role_id' => ['required', 'exists:roles,id'],
            'priority_level' => ['nullable', 'integer', 'in:0,1'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.users.edit', $user)
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $newRole = Role::findOrFail($data['role_id']);

        // Jangan sampai super admin terakhir kehilangan akses.
        if ($user->isSuperAdmin() && ! $newRole->is_super) {
            $superAdmins = User::whereHas('roles', fn ($q) => $q->where('is_super', true))->count();
            if ($superAdmins <= 1) {
                return back()->with('error', 'Tidak dapat mengubah role super admin terakhir.');
            }
        }

        // Gunakan nama dari DB jika tidak dikirim
        $namaLengkap = !empty($data['nama_lengkap'])
            ? $data['nama_lengkap']
            : trim($user->first_name . ' ' . $user->last_name);

        $parsed = $this->parseNama($namaLengkap);

        // Pastikan username unik, kecuali milik user ini sendiri
        $baseUsername = $parsed['username'];
        $username = $baseUsername;
        $i = 2;
        while (User::where('username', $username)->where('id', '!=', $user->id)->exists()) {
            $username = $baseUsername . $i++;
        }

        $updateData = [
            'first_name'       => $parsed['firstName'],
            'last_name'        => $parsed['lastName'],
            'username'         => $username,
            'nip'              => $data['nip'] ?? null,
            'unit_kerja'       => $data['unit_kerja'] ?? null,
            'posisi_pekerjaan' => $data['posisi_pekerjaan'] ?? null,
            'profesi'          => $data['profesi'] ?? null,
            'jabatan'          => $data['jabatan'] ?? null,
            'role'             => $newRole->name,
            'priority_level'   => $data['priority_level'] ?? 0,
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);
        $user->syncRolesByName([$newRole->name]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        if ($user->isSuperAdmin()) {
            $superAdmins = User::whereHas('roles', fn ($q) => $q->where('is_super', true))->count();
            if ($superAdmins <= 1) {
                return back()->with('error', 'Tidak dapat menghapus super admin terakhir.');
            }
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    public function syncFromSimutu()
    {
        try {
            $stats = app(SimutuUserSyncService::class)->sync();

            return redirect()->route('admin.users.index')
                ->with('success', "Simutu sync selesai: {$stats['checked']} dicek, {$stats['created']} dibuat, {$stats['updated']} diperbarui.");
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal sync dari Simutu: '.$e->getMessage());

            return redirect()->route('admin.users.index')
                ->with('error', 'Gagal sync dari Simutu: '.$e->getMessage());
        }
    }
}
