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

     /**
     * Verificar si es super admin
     */
    public function isSuperAdmin()
    {
        return $this->is_super_admin ?? false;
    }

    /**
     * Verificar si se puede modificar este usuario
     */
    public function canBeModifiedBy($user)
    {
        // Super admin no puede ser modificado por nadie
        if ($this->isSuperAdmin()) {
            return false;
        }
        
        // Un admin no puede modificar a otro admin (solo super admin)
        if ($this->is_admin && !$user->isSuperAdmin()) {
            return false;
        }
        
        return true;
    }

    /**
     * Verificar si se puede eliminar este usuario
     */
    public function canBeDeletedBy($user)
    {
        // Super admin no puede ser eliminado
        if ($this->isSuperAdmin()) {
            return false;
        }
        
        // Un admin no puede eliminar a otro admin (solo super admin)
        if ($this->is_admin && !$user->isSuperAdmin()) {
            return false;
        }
        
        return true;
    }

    /**
     * Relación con pedidos
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relación con subastas ganadas
     */
    public function wonAuctions()
    {
        return $this->hasMany(Product::class, 'auction_winner_id');
    }

    /**
     * Relación con sorteos ganados
     */
    public function wonRaffles()
    {
        return $this->hasMany(Raffle::class, 'winner_id');
    }

}