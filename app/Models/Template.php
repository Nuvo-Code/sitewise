<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Template extends Model
{
    protected $fillable = [
        'site_id',
        'name',
        'description',
        'structure',
        'active',
    ];

    protected $casts = [
        'structure' => 'array',
        'active' => 'boolean',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    public function templateContents(): HasMany
    {
        return $this->hasMany(TemplateContent::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope('site', function (Builder $builder) {
            if (app()->bound('site') && app('site')) {
                $builder->where('site_id', app('site')->id);
            }
        });
    }

    public function getFieldsAttribute(): array
    {
        return $this->structure ?? [];
    }

    public function hasField(string $key): bool
    {
        return array_key_exists($key, $this->fields);
    }

    public function getFieldType(string $key): ?string
    {
        return $this->fields[$key] ?? null;
    }
}
