<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Ejecutar cada hora para eliminar mensajes de más de 1 hora
        $schedule->command('messages:delete-old --hours=1')->hourly();
        
        // Para pruebas (cada minuto) - descomenta para probar
        // $schedule->command('messages:delete-old --hours=1')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
    protected function schedule(Schedule $schedule)
    {
        // Ejecutar cada minuto para finalizar subastas
        $schedule->command('auctions:end')->everyMinute();
    }
