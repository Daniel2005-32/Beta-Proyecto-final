<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class GenerateSeederFromLocal extends Command
{
    protected $signature = 'db:generate-seeder';
    protected $description = 'Genera un Seeder estático con todos los datos de la DB local para migrar a producción (Render)';

    public function handle()
    {
        $this->info('🚀 Extrayendo datos de la base de datos local...');

        $categories = Category::all()->toArray();
        $users = User::all()->toArray();
        $products = Product::all()->toArray();
        
        // Tablas opcionales por si aún no están migradas o vacías
        $auctions = Schema::hasTable('auctions') ? \App\Models\Auction::all()->toArray() : [];
        $raffles = Schema::hasTable('raffles') ? \App\Models\Raffle::all()->toArray() : [];

        $code = "<?php\n\nnamespace Database\Seeders;\n\nuse Illuminate\Database\Seeder;\nuse App\Models\User;\nuse App\Models\Category;\nuse App\Models\Product;\n\nclass LocalDataSeeder extends Seeder\n{\n    public function run(): void\n    {\n";

        // Desactivar claves foráneas para evitar conflictos de orden de inserción
        $code .= "        // \\Illuminate\\Support\\Facades\\DB::statement('SET CONSTRAINTS ALL DEFERRED'); // Para Postgres\n";
        $code .= "        // Si es MySQL: \\Illuminate\\Support\\Facades\\Schema::disableForeignKeyConstraints();\n\n";

        // 1. Categorías
        $code .= "        \$categories = " . var_export($categories, true) . ";\n";
        $code .= "        foreach (\$categories as \$cat) {\n";
        $code .= "            Category::updateOrCreate(['id' => \$cat['id']], \$cat);\n";
        $code .= "        }\n        \$this->command->info('✅ Categorías migradas.');\n\n";

        // 2. Usuarios
        $code .= "        \$users = " . var_export($users, true) . ";\n";
        $code .= "        foreach (\$users as \$u) {\n";
        $code .= "            User::updateOrCreate(['id' => \$u['id']], \$u);\n";
        $code .= "        }\n        \$this->command->info('✅ Usuarios migrados.');\n\n";

        // 3. Productos
        $code .= "        \$products = " . var_export($products, true) . ";\n";
        $code .= "        foreach (\$products as \$p) {\n";
        $code .= "            Product::updateOrCreate(['id' => \$p['id']], \$p);\n";
        $code .= "        }\n        \$this->command->info('✅ Productos migrados.');\n\n";
        
        // 4. Subastas
        if (count($auctions) > 0) {
            $code .= "        \$auctions = " . var_export($auctions, true) . ";\n";
            $code .= "        foreach (\$auctions as \$a) {\n";
            $code .= "            \\App\\Models\\Auction::updateOrCreate(['id' => \$a['id']], \$a);\n";
            $code .= "        }\n        \$this->command->info('✅ Subastas migradas.');\n\n";
        }

        // 5. Sorteos
        if (count($raffles) > 0) {
            $code .= "        \$raffles = " . var_export($raffles, true) . ";\n";
            $code .= "        foreach (\$raffles as \$r) {\n";
            $code .= "            \\App\\Models\\Raffle::updateOrCreate(['id' => \$r['id']], \$r);\n";
            $code .= "        }\n        \$this->command->info('✅ Sorteos migrados.');\n\n";
        }

        $code .= "    }\n}\n";

        File::put(database_path('seeders/LocalDataSeeder.php'), $code);
        $this->info('🎉 ¡LocalDataSeeder.php creado exitosamente en database/seeders/!');
        
        return 0;
    }
}
