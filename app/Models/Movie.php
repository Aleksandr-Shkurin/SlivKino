<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_en',
        'country',
        'year',
        'age_rating',
        'release_date',
        'end_date',
        'poster',
        'description',
        'full_description',
        'director',
        'cast',
        'duration_minutes',
        'coming_soon',
    ];

    protected $casts = [
        'release_date' => 'date',
        'end_date' => 'date',
        'cast' => 'array',
        'coming_soon' => 'boolean',
    ];

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'movie_genre');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    public function scopeNowPlaying($query)
    {
        return $query->where('coming_soon', false);
    }
}
