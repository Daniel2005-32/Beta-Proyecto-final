<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Carbon\Carbon;

class EndAuctions extends Command
{
    protected $signature = 'auctions:end';
    protected $description = 'Finaliza las subastas que han terminado y notifica a los ganadores';

    public function handle()
    {
        // Buscar subastas que han terminado y no tienen ganador asignado
        $endedAuctions = Product::where('is_in_auction', true)
            ->where('auction_cancelled', false)
            ->where('auction_end_time', '<=', Carbon::now())
            ->whereNull('auction_winner_id')
            ->get();

        foreach ($endedAuctions as $auction) {
            // Buscar la puja más alta (por ahora, el precio actual es la última puja)
            if ($auction->auction_winner_id) {
                // Ya tiene ganador, no hacer nada
                continue;
            }
            
            // Si no hay pujas, la subasta queda desierta
            if ($auction->price == $auction->auction_start_price) {
                $auction->is_in_auction = false;
                $auction->auction_cancelled = true;
                $auction->save();
                $this->info("❌ Subasta desierta: {$auction->name}");
            } else {
                // Hay ganador (el último que pujó)
                $this->info("✅ Subasta finalizada con ganador: {$auction->name}");
                // El ganador ya está en auction_winner_id
            }
        }

        $this->info('Proceso de finalización de subastas completado');
        return 0;
    }
}
