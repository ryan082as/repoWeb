<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = [
        'title',
        'type',
        'genre',
        'rating',
        'release_year',
        'total_count',
        'creator',
    ];

    protected $casts = [
        'rating' => 'float',
        'release_year' => 'integer',
        'total_count' => 'integer',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        $keyword = trim((string) $keyword);

        if ($keyword === '') {
            return $query;
        }

        return $query->where('title', 'like', "%{$keyword}%");
    }

    public function scopeByType(Builder $query, ?string $type): Builder
    {
        $type = trim((string) $type);

        if ($type === '') {
            return $query;
        }

        return $query->where('type', $type);
    }
}
