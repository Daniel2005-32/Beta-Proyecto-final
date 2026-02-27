<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckStockForAuctions extends Command
{
    protected $signature = 'auctions:check-stock';
    protected $description = 'Verifica productos con stock=1 y los pone en subasta';

    public function handle()
    {
        $products = Product::where('stock', 1)
            ->where('is_in_auction', false)
            ->get();
        
        foreach ($products as $product) {
            $product->is_in_auction = true;
            $product->auction_start_price = $product->price * 0.8; // 20% descuento
            $product->auction_end_time = Carbon::now()->addHours(24);
            $product->save();
            
            $this->info("✅ Subasta iniciada para: {$product->name}");
        }
        
        return 0;
    }
}
