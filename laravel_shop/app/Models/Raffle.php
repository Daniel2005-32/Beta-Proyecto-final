<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Raffle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'draw_date', 'status', 'winner_id'
    ];

    protected $casts = [
        'draw_date' => 'datetime',
    ];

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function entries()
    {
        return $this->hasMany(RaffleEntry::class);
    }

    /**
     * Obtener los datos extra del sorteo desde la descripción
     */
    public function getExtraData()
    {
        if (preg_match('/\[DATOS_SORTEO\]\n(.*?)$/s', $this->description, $matches)) {
            return json_decode($matches[1], true) ?? [];
        }
        return [];
    }

    /**
     * Obtener la descripción limpia (sin el JSON)
     */
    public function getCleanDescription()
    {
        return preg_replace('/\n\n\[DATOS_SORTEO\].*$/s', '', $this->description);
    }

    /**
     * Obtener el producto asociado al sorteo
     */
    public function getProduct()
    {
        $extra = $this->getExtraData();
        if (isset($extra['product_id'])) {
            return Product::find($extra['product_id']);
        }
        return null;
    }

    /**
     * Obtener el precio por entrada
     */
    public function getTicketPrice()
    {
        $extra = $this->getExtraData();
        return $extra['ticket_price'] ?? 20;
    }

    /**
     * Obtener el límite de entradas
     */
    public function getMaxEntries()
    {
        $extra = $this->getExtraData();
        return $extra['max_entries'] ?? null;
    }

    /**
     * Obtener la fecha de inicio
     */
    public function getStartDate()
    {
        $extra = $this->getExtraData();
        return isset($extra['start_date']) ? Carbon::parse($extra['start_date']) : null;
    }

    /**
     * Obtener la fecha de fin
     */
    public function getEndDate()
    {
        $extra = $this->getExtraData();
        return isset($extra['end_date']) ? Carbon::parse($extra['end_date']) : $this->draw_date;
    }

    public function isActive()
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        
        return $this->status === 'pending' && 
               $startDate && 
               Carbon::now()->between($startDate, $endDate);
    }

    public function timeLeft()
    {
        if (!$this->isActive()) return 'Finalizado';
        
        $endDate = $this->getEndDate();
        $diff = Carbon::now()->diff($endDate);
        
        if ($diff->days > 0) {
            return $diff->days . ' días y ' . $diff->h . ' horas';
        }
        if ($diff->h > 0) {
            return $diff->h . ' horas y ' . $diff->i . ' minutos';
        }
        return $diff->i . ' minutos';
    }

    public function getUserEntries($userId)
    {
        return $this->entries()->where('user_id', $userId)->sum('quantity');
    }

    public function getUserChance($userId)
    {
        $totalEntries = $this->entries()->sum('quantity');
        if ($totalEntries == 0) return 0;
        
        $userEntries = $this->getUserEntries($userId);
        return round(($userEntries / $totalEntries) * 100, 2);
    }

    public function drawWinner()
    {
        if ($this->status === 'completed') return null;

        $entries = $this->entries()->with('user')->get();
        
        if ($entries->isEmpty()) {
            $this->update(['status' => 'completed']);
            return null;
        }

        $pool = [];
        foreach ($entries as $entry) {
            for ($i = 0; $i < $entry->quantity; $i++) {
                $pool[] = $entry->user_id;
            }
        }

        $winnerId = $pool[array_rand($pool)];
        
        $this->update([
            'winner_id' => $winnerId,
            'status' => 'completed'
        ]);

        return $winnerId;
    }
}
