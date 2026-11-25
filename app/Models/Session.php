<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Session extends Model
{
    use HasFactory;

    // Указываем фактическое имя таблицы в MySQL
    protected $table = 'movie_sessions';

    protected $fillable = [
        'movie_id',
        'hall_id',
        'starts_at',
        'price',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'price' => 'decimal:2',
    ];

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }

    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}