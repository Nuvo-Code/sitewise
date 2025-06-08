<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Page extends Model
{
    protected $fillable = [
        'site_id',
        'slug',
        'title',
        'response_type',
        'html_content',
        'markdown',
        'json_content',
        'template_id',
        'active',
    ];

    protected $casts = [
        'json_content' => 'array',
        'active' => 'boolean',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
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

    public function getContentAttribute(): string
    {
        return match ($this->response_type) {
            'html' => $this->html_content ?? '',
            'markdown' => $this->markdown ?? '',
            'json' => json_encode($this->json_content ?? []),
            default => '',
        };
    }

    public function hasTemplate(): bool
    {
        return !is_null($this->template_id);
    }
}
