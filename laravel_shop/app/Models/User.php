<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Relación con baneos
     */
    public function bans()
    {
        return $this->hasMany(Ban::class);
    }

    /**
     * Obtener el baneo activo actual
     */
    public function activeBan()
    {
        return $this->bans()
            ->where(function($query) {
                $query->where('is_permanent', true)
                      ->orWhere('banned_until', '>', Carbon::now());
            })
            ->latest()
            ->first();
    }

    /**
     * Verificar si el usuario está baneado
     */
    public function isBanned()
    {
        return $this->activeBan() !== null;
    }

    /**
     * Banear usuario
     */
    public function ban($reason, $duration = null, $bannedBy = null)
    {
        $data = [
            'user_id' => $this->id,
            'banned_by' => $bannedBy ?? auth()->id(),
            'reason' => $reason,
        ];
        
        if ($duration === 'permanent') {
            $data['is_permanent'] = true;
            $data['banned_until'] = null;
        } elseif (is_numeric($duration)) {
            $hours = (int) $duration;
            $data['banned_until'] = Carbon::now()->addHours($hours);
        }
        
        return $this->bans()->create($data);
    }

    /**
     * Quitar baneo
     */
    public function unban()
    {
        return $this->bans()
            ->where(function($query) {
                $query->where('is_permanent', true)
                      ->orWhere('banned_until', '>', Carbon::now());
            })
            ->update(['banned_until' => Carbon::now()]);
    }
}