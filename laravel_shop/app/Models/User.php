<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;
use App\Notifications\ResetPasswordCustom;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'avatar',
        'last_ip_address',
        'show_censored_content',
        'points',
        'last_game_at',
        'last_memory_at',
        'last_roulette_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['avatar_url', 'is_banned', 'ban_reason', 'rank_name', 'rank_progress'];

    /**
     * Relación con la lista de deseos
     */
    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Nombre del Rango basado en puntos
     */
    public function getRankNameAttribute()
    {
        $p = $this->points;
        if ($p >= 600) return 'Platino';
        if ($p >= 300) return 'Oro';
        if ($p >= 100) return 'Plata';
        return 'Bronce';
    }

    /**
     * Progreso hacia el siguiente rango (%)
     */
    public function getRankProgressAttribute()
    {
        $p = $this->points;
        if ($p >= 600) return 100;
        if ($p >= 300) return (($p - 300) / 300) * 100;
        if ($p >= 100) return (($p - 100) / 200) * 100;
        return ($p / 100) * 100;
    }

    public function getBanReasonAttribute()
    {
        $activeBan = $this->activeBan();
        return $activeBan ? $activeBan->reason : null;
    }


    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0D8ABC&color=fff';
    }

    public function getIsBannedAttribute()
    {
        return $this->isBanned();
    }


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_super_admin' => 'boolean',
            'show_censored_content' => 'boolean',
            'points' => 'integer',
            'last_game_at' => 'datetime',
            'last_memory_at' => 'datetime',
            'last_roulette_at' => 'datetime',
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
     * Obtener el baneo activo actual (opcionalmente filtrado por tipo)
     */
    public function activeBan($type = null)
    {
        $query = $this->bans()
            ->where(function($query) {
                $query->where('is_permanent', true)
                      ->orWhere('banned_until', '>', \Carbon\Carbon::now());
            });

        if ($type) {
            $query->where('type', $type);
        }

        return $query->latest()->first();
    }

    /**
     * Verificar si el usuario está baneado (tipo 'account' por defecto)
     */
    public function isBanned($type = 'account')
    {
        return $this->activeBan($type) !== null;
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
            'ip_address' => $this->last_ip_address,
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

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    /**
     * Override the password reset notification.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordCustom($token));
    }

}