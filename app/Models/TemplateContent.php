<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class TemplateContent extends Model
{
    protected $fillable = [
        'page_id',
        'template_id',
        'key',
        'value',
    ];

    /**
     * Ensure value is never null
     */
    public function getValueAttribute($value): string
    {
        return $value ?? '';
    }

    /**
     * Ensure value is never null when setting
     */
    public function setValueAttribute($value): void
    {
        $this->attributes['value'] = $value ?? '';
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope('site', function (Builder $builder) {
            if (app()->bound('site') && app('site')) {
                $builder->whereHas('page', function (Builder $query) {
                    $query->where('site_id', app('site')->id);
                });
            }
        });
    }

    public static function getContentForPage(Page $page): array
    {
        return static::where('page_id', $page->id)
            ->pluck('value', 'key')
            ->toArray();
    }

    public static function updateContentForPage(Page $page, array $content): void
    {
        foreach ($content as $key => $value) {
            // Ensure value is never null - use empty string as fallback
            if ($value === null) {
                $value = '';
            }

            static::updateOrCreate(
                [
                    'page_id' => $page->id,
                    'template_id' => $page->template_id,
                    'key' => $key,
                ],
                ['value' => $value]
            );
        }
    }
}
