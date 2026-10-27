<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\JobRole;
use App\Models\Role;
use App\Models\Unit;
use App\Models\User;
use App\Services\SimutuUserSyncService;
use App\Services\UserAccountService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private readonly UserAccountService $accounts) {}

    public function index(Request $request)
    {
        $query = User::query()->latest();
        $like = fn ($val) => '%'.strtolower($val).'%';

        if ($request->filled('nama')) {
            $query->whereRaw('LOWER(CONCAT(first_name, \' \', last_name)) LIKE ?', [$like($request->nama)]);
        }
        if ($request->filled('username')) {
            $query->whereRaw('LOWER(username) LIKE ?', [$like($request->username)]);
        }
        if ($request->filled('nip')) {
            $query->whereRaw('LOWER(nip) LIKE ?', [$like($request->nip)]);
        }
        if ($request->filled('unit_kerja')) {
            $query->whereRaw('LOWER(unit_kerja) LIKE ?', [$like($request->unit_kerja)]);
        }
        if ($request->filled('posisi')) {
            $query->whereRaw('LOWER(posisi_pekerjaan) LIKE ?', [$like($request->posisi)]);
        }
        if ($request->filled('profesi')) {
            $query->whereRaw('LOWER(profesi) LIKE ?', [$like($request->profesi)]);
        }
        if ($request->filled('jabatan')) {
            $query->whereRaw('LOWER(jabatan) LIKE ?', [$like($request->jabatan)]);
        }
        if ($request->filled('role')) {
            $query->whereHas('roles', fn ($q) => $q->where('name', $request->role));
        }

        $users = $query->paginate(10)->withQueryString();
        $roles = Role::query()->orderBy('id')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        return view('admin.users.create', $this->formOptions());
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $role = Role::findOrFail($data['role_id']);

        $this->accounts->create($data, $role);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', ['user' => $user] + $this->formOptions());
    }

    /**
     * Opsi dropdown untuk form user (role akses, unit kerja, jabatan).
     *
     * @return array{roles: \Illuminate\Support\Collection, units: \Illuminate\Support\Collection, jobRoles: \Illuminate\Support\Collection}
     */
    private function formOptions(): array
    {
        return [
            'roles' => Role::query()->orderBy('id')->get(),
            'units' => Unit::query()->active()->orderBy('nama_unit')->get(),
            'jobRoles' => JobRole::query()->orderBy('nama_role')->get(),
        ];
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        $newRole = Role::findOrFail($data['role_id']);

        // Jangan sampai super admin terakhir kehilangan akses.
        if ($user->isSuperAdmin() && ! $newRole->is_super) {
            $superAdmins = User::whereHas('roles', fn ($q) => $q->where('is_super', true))->count();
            if ($superAdmins <= 1) {
                return back()->with('error', 'Tidak dapat mengubah role super admin terakhir.');
            }
        }

        $this->accounts->update($user, $data, $newRole);

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
