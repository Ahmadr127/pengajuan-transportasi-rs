<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserAccountService
{
    /**
     * Parse nama lengkap menjadi first_name, last_name, dan username.
     * - Username dari kata ke-1 & ke-2 (nama1.nama2 lowercase).
     * - Contoh: "M. Rangga Aditya" → "m.rangga".
     */
    public function parseNama(string $namaLengkap): array
    {
        $namaLengkap = trim($namaLengkap);

        $parts = preg_split('/\s+/', $namaLengkap);

        $firstName = $parts[0] ?? '';
        $lastName = count($parts) >= 2
            ? implode(' ', array_slice($parts, 1))
            : '';

        $namaUntukUsername = trim(explode(',', $namaLengkap)[0]);
        $partsUsername = preg_split('/\s+/', $namaUntukUsername);

        $w1 = preg_replace('/[^a-z0-9]/', '', strtolower($partsUsername[0] ?? ''));
        $w2 = isset($partsUsername[1]) ? preg_replace('/[^a-z0-9]/', '', strtolower($partsUsername[1])) : null;

        $username = ($w2 !== null && $w2 !== '')
            ? $w1.'.'.$w2
            : $w1;

        return compact('firstName', 'lastName', 'username');
    }

    public function create(array $data, Role $role): User
    {
        $parsed = $this->parseNama($data['nama_lengkap']);
        $username = $this->uniqueUsername($parsed['username']);

        $user = User::create([
            'first_name' => $parsed['firstName'],
            'last_name' => $parsed['lastName'],
            'username' => $username,
            'nip' => $data['nip'] ?? null,
            'password' => Hash::make($data['password']),
            'unit_kerja' => $data['unit_kerja'] ?? null,
            'posisi_pekerjaan' => $data['posisi_pekerjaan'] ?? null,
            'profesi' => $data['profesi'] ?? null,
            'jabatan' => $data['jabatan'] ?? null,
            'role' => $role->name,
            'priority_level' => $data['priority_level'] ?? 0,
        ]);

        $user->syncRolesByName([$role->name]);

        return $user;
    }

    public function update(User $user, array $data, Role $role): void
    {
        $namaLengkap = ! empty($data['nama_lengkap'])
            ? $data['nama_lengkap']
            : trim($user->first_name.' '.$user->last_name);

        $parsed = $this->parseNama($namaLengkap);
        $username = $this->uniqueUsername($parsed['username'], $user->id);

        $updateData = [
            'first_name' => $parsed['firstName'],
            'last_name' => $parsed['lastName'],
            'username' => $username,
            'nip' => $data['nip'] ?? null,
            'unit_kerja' => $data['unit_kerja'] ?? null,
            'posisi_pekerjaan' => $data['posisi_pekerjaan'] ?? null,
            'profesi' => $data['profesi'] ?? null,
            'jabatan' => $data['jabatan'] ?? null,
            'role' => $role->name,
            'priority_level' => $data['priority_level'] ?? 0,
        ];

        if (! empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);
        $user->syncRolesByName([$role->name]);
    }

    private function uniqueUsername(string $base, ?int $ignoreId = null): string
    {
        $username = $base;
        $i = 2;

        while (
            User::where('username', $username)
                ->when($ignoreId !== null, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $username = $base.$i++;
        }

        return $username;
    }
}
