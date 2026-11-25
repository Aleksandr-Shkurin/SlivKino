<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Hall extends Model { use HasFactory; protected $fillable=['cinema_id','name','rows_count','seats_per_row','vip_seats','disabled_seats']; protected $casts=['vip_seats'=>'array','disabled_seats'=>'array']; public function cinema(): BelongsTo { return $this->belongsTo(Cinema::class); } public function sessions(): HasMany { return $this->hasMany(Session::class); } }
