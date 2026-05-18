<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoyaltyController extends Controller
{
    /**
     * Obtener el saldo de puntos del usuario actual
     */
    public function getPoints()
    {
        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'points' => $user->points
        ]);
    }

    /**
     * Añadir puntos tras completar un juego
     */
    public function addPoints(Request $request)
    {
        $request->validate([
            'points' => 'required|integer|min:-100|max:100',
            'game_id' => 'required|string'
        ]);

        $user = Auth::user();
        
        // Determinar que columna de cooldown usar
        $cooldownCol = match($request->game_id) {
            'soul_memory' => 'last_memory_at',
            'soul_roulette' => 'last_roulette_at',
            'soul_battle' => 'last_rpg_at',
            default => 'last_game_at'
        };

        // 24 Hour Cooldown check
        if (!$user->is_admin && $user->$cooldownCol) {
            $lastGame = \Carbon\Carbon::parse($user->$cooldownCol);
            $nextGame = $lastGame->addHours(24);
            
            if (now()->lessThan($nextGame)) {
                $diff = now()->diff($nextGame);
                $remaining = "";
                if ($diff->h > 0) $remaining .= $diff->h . "h ";
                $remaining .= $diff->i . "m";
                
                return response()->json([
                    'status' => 'error',
                    'message' => "Cooldown activo para este juego. Vuelve en: {$remaining}"
                ], 403);
            }
        }

        $user->points += $request->points;
        if ($user->points < 0) $user->points = 0;
        
        // Actualizar el cooldown específico
        $user->$cooldownCol = now();
        $user->save();

        $type = $request->points >= 0 ? 'ganado' : 'perdido';
        $absPoints = abs($request->points);

        return response()->json([
            'status' => 'success',
            'message' => "¡Has {$type} {$absPoints} puntos!",
            'new_balance' => $user->points,
            'last_game_at' => $user->$cooldownCol,
            'game_id' => $request->game_id
        ]);
    }

    /**
     * Obtener el ranking global de usuarios (Top 10)
     */
    public function getRanking()
    {
        $users = \App\Models\User::orderBy('points', 'desc')
            ->limit(10)
            ->get(['name', 'avatar', 'points']);

        foreach ($users as $user) {
            $user->append('avatar_url');
        }

        return response()->json([
            'status' => 'success',
            'ranking' => $users
        ]);
    }

    /**
     * Reclamar un cupón de la ruleta (5% de descuento)
     */
    public function claimRouletteCoupon(Request $request)
    {
        $user = auth()->user();
        
        // Generar un código único aleatorio
        $code = 'SOUL-' . strtoupper(bin2hex(random_bytes(4)));

        $coupon = \App\Models\Coupon::create([
            'user_id' => $user->id,
            'code' => $code,
            'type' => 'percentage',
            'value' => 5,
            'usage_limit' => 1,
            'used_count' => 0,
            'is_active' => true,
            'expires_at' => \Carbon\Carbon::now()->addDays(30),
            'min_purchase' => 0,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => '¡Cupón del 5% guardado en tu perfil!',
            'coupon' => $coupon
        ]);
    }

    /**
     * Reclamar un cupón de batalla (10% de descuento para Modo Imposible)
     */
    public function claimBattleCoupon(Request $request)
    {
        $request->validate(['difficulty' => 'required|string']);
        
        if ($request->difficulty !== 'impossible') {
            return response()->json([
                'status' => 'error', 
                'message' => 'Dificultad no elegible para recompensa especial.'
            ], 403);
        }

        $user = auth()->user();
        
        $code = 'SOUL-BOSS-' . strtoupper(bin2hex(random_bytes(4)));

        $coupon = \App\Models\Coupon::create([
            'user_id' => $user->id,
            'code' => $code,
            'type' => 'percentage',
            'value' => 10,
            'usage_limit' => 1,
            'used_count' => 0,
            'is_active' => true,
            'expires_at' => \Carbon\Carbon::now()->addDays(30),
            'min_purchase' => 0,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => '¡Hazaña completada! Cupón del 10% guardado en tu perfil.',
            'coupon' => $coupon
        ]);
    }

    /**
     * Ver mis cupones
     */
    public function myCoupons()
    {
        $coupons = auth()->user()->coupons()
            ->where('is_active', true)
            ->where('usage_limit', '>', \DB::raw('COALESCE(used_count, 0)'))
            ->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->get();

        return response()->json(['coupons' => $coupons]);
    }
}
