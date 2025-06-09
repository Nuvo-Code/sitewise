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
        'blade_template',
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

    public function getFieldsForFormAttribute(): array
    {
        if (empty($this->structure)) {
            return [];
        }

        // Handle both old and new structure formats
        $fields = [];

        // Check if this is the new format (array of field objects)
        $isNewFormat = is_array($this->structure) &&
                      !empty($this->structure) &&
                      isset($this->structure[0]) &&
                      is_array($this->structure[0]) &&
                      isset($this->structure[0]['key'], $this->structure[0]['type']);

        if ($isNewFormat) {
            // New format - array of field objects
            foreach ($this->structure as $field) {
                if (is_array($field) && isset($field['key'], $field['type'])) {
                    $fields[$field['key']] = $field;
                }
            }
        } else {
            // Old format - key-value pairs
            foreach ($this->structure as $key => $type) {
                $fields[$key] = [
                    'name' => ucwords(str_replace('_', ' ', $key)),
                    'key' => $key,
                    'type' => $type,
                    'required' => false,
                    'description' => null,
                    'default_value' => null,
                    'options' => [],
                    'validation_rules' => [],
                ];
            }
        }

        return $fields;
    }

    public function getFieldKeysAttribute(): array
    {
        return array_keys($this->getFieldsForFormAttribute());
    }

    public function getFieldByKey(string $key): ?array
    {
        $fields = $this->getFieldsForFormAttribute();
        return $fields[$key] ?? null;
    }

    public function hasBladeTemplate(): bool
    {
        return !empty($this->blade_template);
    }

    public function isBladeTemplate(): bool
    {
        return $this->hasBladeTemplate();
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
