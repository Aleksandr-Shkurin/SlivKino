<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Booking extends Model { use HasFactory; protected $fillable=['session_id','user_id','customer_name','customer_email','seats','status','expires_at']; protected $casts=['seats'=>'array','expires_at'=>'datetime']; public function session(): BelongsTo { return $this->belongsTo(Session::class); } public function user(): BelongsTo { return $this->belongsTo(User::class); } }
