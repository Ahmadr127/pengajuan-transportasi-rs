<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'key',
        'context',
        'section',
        'label',
        'route_name',
        'url',
        'icon',
        'permission_name',
        'sort_order',
        'is_active',
        'is_landing',
        'show_sidebar',
        'show_bottom_nav',
        'active_pattern',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_landing' => 'boolean',
            'show_sidebar' => 'boolean',
            'show_bottom_nav' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function href(): string
    {
        if ($this->route_name && \Illuminate\Support\Facades\Route::has($this->route_name)) {
            return route($this->route_name);
        }

        return $this->url ?: '#';
    }

    public function isActive(): bool
    {
        $patterns = $this->active_pattern
            ? array_values(array_filter(array_map('trim', explode(',', $this->active_pattern))))
            : [];

        if ($patterns === [] && $this->route_name) {
            $patterns = [$this->route_name];
        }

        return $patterns !== [] && request()->routeIs(...$patterns);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForContext($query, string $context)
    {
        return $query->where('context', $context);
    }
}
