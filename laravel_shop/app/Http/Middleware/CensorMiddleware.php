<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class CensorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->has('message') && is_string($request->message)) {
            $originalMessage = $request->message;
            $censoredMessage = $this->censorText($originalMessage);
            
            // Reemplazar el mensaje original con el censurado
            $request->merge(['message' => $censoredMessage]);
        }
        
        return $next($request);
    }
    
    private function censorText($text)
    {
        $badWords = Config::get('censored.words', []);
        $replacement = Config::get('censored.replacement', '*');
        
        foreach ($badWords as $word) {
            $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
            $replacement_text = str_repeat($replacement, strlen($word));
            $text = preg_replace($pattern, $replacement_text, $text);
        }
        
        return $text;
    }
}
