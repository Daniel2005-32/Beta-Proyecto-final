<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Auction;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckStockForAuctions extends Command
{
    protected $signature = 'auctions:check-stock';
    protected $description = 'Verifica productos con stock=1 y crea subastas';

    public function handle()
    {
        $this->info('🔍 Buscando productos con stock = 1...');
        
        $products = Product::where('stock', 1)
            ->whereDoesntHave('auctions', function($query) {
                $query->where('status', 'active');
            })
            ->get();
        
        if ($products->isEmpty()) {
            $this->info('✅ No hay productos nuevos para subastar');
            return 0;
        }

        $this->info("📦 Encontrados {$products->count()} productos para subastar");
        
        foreach ($products as $product) {
            // Precio base = precio actual -20%
            $startingPrice = $product->price * 0.8;
            
            $auction = Auction::create([
                'product_id' => $product->id,
                'starting_price' => $startingPrice,
                'current_price' => $startingPrice,
                'min_bid' => 1.00,
                'start_time' => Carbon::now(),
                'end_time' => Carbon::now()->addHours(24),
                'status' => 'active'
            ]);
            
            $this->line("   ✅ Subasta creada para: {$product->name}");
            $this->line("      💰 Precio inicial: " . number_format($startingPrice, 2) . "€");
            $this->line("      ⏰ Finaliza: " . $auction->end_time->format('d/m/Y H:i'));
        }
        
        $this->info('🎉 Subastas creadas correctamente');
        return 0;
    }
}
