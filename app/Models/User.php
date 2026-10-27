<?php

namespace App\Models;

use App\Support\AclCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'nip',
        'unit_kerja',
        'posisi_pekerjaan',
        'profesi',
        'jabatan',
        'password',
        'priority_level',
        'simutu_id',
        'simutu_status',
        'simutu_synced_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @var array<int, string>|null */
    protected ?array $permissionNamesCache = null;

    protected ?bool $superAdminCache = null;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'priority_level' => 'integer',
        ];
    }

    public function transportRequests()
    {
        return $this->hasMany(TransportRequest::class);
    }

    public function driver()
    {
        return $this->hasOne(\App\Models\Driver::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')->withTimestamps();
    }

    /**
     * Semua nama permission milik user (gabungan dari seluruh role).
     *
     * @return array<int, string>
     */
    public function permissionNames(): array
    {
        if ($this->permissionNamesCache !== null) {
            return $this->permissionNamesCache;
        }

        if (! $this->exists) {
            return $this->permissionNamesCache = [];
        }

        $cacheKey = 'acl.user.'.$this->getKey().'.'.AclCache::version();

        return $this->permissionNamesCache = Cache::remember($cacheKey, 300, function () {
            $this->loadMissing('roles.permissions');

            return $this->roles
                ->flatMap(fn (Role $role) => $role->permissions->pluck('name'))
                ->unique()
                ->values()
                ->all();
        });
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissionNames(), true);
    }

    public function isSuperAdmin(): bool
    {
        if ($this->superAdminCache !== null) {
            return $this->superAdminCache;
        }

        if (! $this->exists) {
            return $this->superAdminCache = false;
        }

        $this->loadMissing('roles');

        return $this->superAdminCache = $this->roles->contains(
            fn (Role $role) => (bool) $role->is_super
        );
    }

    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    public function primaryRole(): ?Role
    {
        return $this->roles()->orderBy('roles.id')->first();
    }

    public function primaryRoleLabel(): string
    {
        return $this->primaryRole()?->label ?? ucfirst((string) ($this->primaryRole()?->name ?? ''));
    }

    /**
     * Sinkronkan role berdasarkan nama role (dipakai admin UI & sync Simutu).
     *
     * @param  array<int, string>  $roleNames
     */
    public function syncRolesByName(array $roleNames): void
    {
        $roles = Role::query()->whereIn('name', $roleNames)->get();

        $this->roles()->sync($roles->pluck('id')->all());

        $this->permissionNamesCache = null;
        $this->superAdminCache = null;
        AclCache::flush();
    }

    public function scopeWithPermission($query, string $permission)
    {
        return $query->where(function ($query) use ($permission) {
            $query->whereHas('roles.permissions', function ($q) use ($permission) {
                $q->where('name', $permission);
            })->orWhereHas('roles', function ($q) {
                $q->where('is_super', true);
            });
        });
    }

    public function isPriority(): bool
    {
        return (int) $this->priority_level === 1;
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
