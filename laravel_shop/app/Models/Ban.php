<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Ban extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'banned_by', 'reason', 'banned_until', 'is_permanent'
    ];

    protected $casts = [
        'banned_until' => 'datetime',
        'is_permanent' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function banner()
    {
        return $this->belongsTo(User::class, 'banned_by');
    }

    public function isActive()
    {
        if ($this->is_permanent) {
            return true;
        }
        
        return $this->banned_until && Carbon::now()->lt($this->banned_until);
    }

    public function timeLeft()
    {
        if ($this->is_permanent) {
            return 'Permanente';
        }
        
        if (!$this->banned_until || Carbon::now()->gt($this->banned_until)) {
            return 'Expirado';
        }
        
        $diff = Carbon::now()->diff($this->banned_until);
        
        if ($diff->days > 0) {
            return $diff->days . ' días y ' . $diff->h . ' horas';
        }
        if ($diff->h > 0) {
            return $diff->h . ' horas y ' . $diff->i . ' minutos';
        }
        if ($diff->i > 0) {
            return $diff->i . ' minutos';
        }
        return $diff->s . ' segundos';
    }
}
