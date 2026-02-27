<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\Helpers\CensorHelper;
use Illuminate\Console\Command;

class FindBadWords extends Command
{
    protected $signature = 'badwords:find 
                            {--check : Analizar mensajes existentes}
                            {--stats : Mostrar estadísticas}
                            {--add= : Añadir una palabra a la lista}';
    
    protected $description = 'Gestiona la detección de malas palabras';

    public function handle()
    {
        if ($this->option('add')) {
            $this->addWord($this->option('add'));
            return 0;
        }
        
        if ($this->option('stats')) {
            $this->showStats();
            return 0;
        }
        
        if ($this->option('check')) {
            $this->checkMessages();
            return 0;
        }
        
        $this->info('Uso: php artisan badwords:find [--check] [--stats] [--add="palabra"]');
        return 0;
    }

    private function addWord($word)
    {
        $word = strtolower(trim($word));
        
        $existing = \App\Models\CensoredWord::where('word', $word)->first();
        
        if ($existing) {
            $this->warn("La palabra '{$word}' ya existe en la lista.");
            return;
        }
        
        \App\Models\CensoredWord::create([
            'word' => $word,
            'severity' => 'medium',
            'active' => true
        ]);
        
        $this->info("✅ Palabra '{$word}' añadida a la lista de censura.");
    }

    private function checkMessages()
    {
        $messages = Message::with('user')->get();
        $badMessages = [];
        
        $this->info("Analizando " . $messages->count() . " mensajes...\n");
        
        foreach ($messages as $message) {
            if (CensorHelper::containsBadWords($message->message)) {
                $badMessages[] = [
                    'id' => $message->id,
                    'user' => $message->user->name,
                    'message' => $message->message,
                    'censored' => CensorHelper::censor($message->message),
                    'date' => $message->created_at->format('d/m/Y H:i')
                ];
            }
        }
        
        if (empty($badMessages)) {
            $this->info("✅ No se encontraron mensajes con malas palabras.");
            return;
        }
        
        $this->table(
            ['ID', 'Usuario', 'Mensaje', 'Censurado', 'Fecha'],
            $badMessages
        );
        
        $this->warn("⚠️ Se encontraron " . count($badMessages) . " mensajes con malas palabras.");
    }

    private function showStats()
    {
        $totalMessages = Message::count();
        $totalWords = \App\Models\CensoredWord::count();
        $activeWords = \App\Models\CensoredWord::where('active', true)->count();
        
        $messagesWithBadWords = 0;
        $messages = Message::all();
        
        foreach ($messages as $message) {
            if (CensorHelper::containsBadWords($message->message)) {
                $messagesWithBadWords++;
            }
        }
        
        $this->info("📊 ESTADÍSTICAS DE CENSURA");
        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->line("Total de mensajes: " . $totalMessages);
        $this->line("Palabras censuradas totales: " . $totalWords);
        $this->line("Palabras activas: " . $activeWords);
        $this->line("Mensajes con malas palabras: " . $messagesWithBadWords);
        
        if ($totalMessages > 0) {
            $percentage = round(($messagesWithBadWords / $totalMessages) * 100, 2);
            $this->line("Porcentaje de mensajes ofensivos: " . $percentage . "%");
        }
    }
}
