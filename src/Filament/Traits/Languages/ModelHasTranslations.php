<?php

namespace Ngankt2\LaravelHelper\Filament\Traits\Languages;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int|null $language_parent_id
 */
trait ModelHasTranslations
{
    /**
     * Quan hệ với bảng chính nó (bản ghi gốc, nếu có).
     */
    public function languageParent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'language_parent_id');
    }

    /**
     * Quan hệ với các bản ghi con nếu có.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(self::class, 'language_parent_id');
    }

    public function getLanguagesSameParent(): Collection
    {
        return self::query()->where('language_parent_id', $this->language_parent_id)
            ->select(['language', 'id'])
            ->get()->keyBy('language');
    }

    public function languages(): HasMany
    {
        return $this->hasMany(self::class, 'language_parent_id', 'language_parent_id');
    }
}
