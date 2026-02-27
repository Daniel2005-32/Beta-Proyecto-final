<?php

namespace App\Console\Commands;

use App\Models\Message;
use Illuminate\Console\Command;

class DeleteOldMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:delete-old {--hours=1 : Número de horas a mantener los mensajes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina mensajes antiguos del chat';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        
        // Calcular la fecha límite
        $limitDate = now()->subHours($hours);
        
        // Contar mensajes a eliminar
        $count = Message::where('created_at', '<', $limitDate)->count();
        
        if ($count === 0) {
            $this->info("No hay mensajes antiguos que eliminar.");
            return 0;
        }
        
        // Eliminar mensajes
        Message::where('created_at', '<', $limitDate)->delete();
        
        $this->info("Se eliminaron {$count} mensajes anteriores a {$limitDate->format('d/m/Y H:i')}");
        
        return 0;
    }
}
