<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Cinema extends Model { use HasFactory; protected $fillable=['name','name_en','address','phone','email','working_hours','description','features']; protected $casts=['features'=>'array']; public function halls(): HasMany { return $this->hasMany(Hall::class); } }
