<?php

namespace App\Console\Commands;

use App\Models\Raffle;
use Illuminate\Console\Command;

class EndRaffles extends Command
{
    protected $signature = 'raffles:end';
    protected $description = 'Finaliza los sorteos que han terminado y selecciona ganadores';

    public function handle()
    {
        $endedRaffles = Raffle::where('status', 'active')
            ->where('end_date', '<=', now())
            ->get();

        foreach ($endedRaffles as $raffle) {
            $winnerId = $raffle->drawWinner();
            
            if ($winnerId) {
                $this->info("✅ Sorteo '{$raffle->name}' finalizado. Ganador ID: {$winnerId}");
            } else {
                $this->info("ℹ️ Sorteo '{$raffle->name}' finalizado sin participantes");
            }
        }

        return 0;
    }
}
