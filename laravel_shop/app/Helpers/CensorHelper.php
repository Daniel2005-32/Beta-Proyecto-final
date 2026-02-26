<?php

namespace App\Helpers;

class CensorHelper
{
    /**
     * Censurar palabras prohibidas en un texto
     *
     * @param string $text
     * @return string
     */
    public static function censor($text)
    {
        $config = config('censored_words');
        $badWords = $config['words'];
        $replacement = $config['replacement'];
        $fullCensor = $config['full_message_censor'];
        
        // Si está activado el censurado completo
        if ($fullCensor) {
            foreach ($badWords as $word) {
                if (stripos($text, $word) !== false) {
                    return '[Mensaje censurado por contenido inapropiado]';
                }
            }
            return $text;
        }
        
        // Censura parcial (solo las palabras malas)
        $pattern = '/\b(' . implode('|', array_map('preg_quote', $badWords)) . ')\b/i';
        
        $callback = function($matches) use ($replacement) {
            return str_repeat($replacement, strlen($matches[0]));
        };
        
        return preg_replace_callback($pattern, $callback, $text);
    }
    
    /**
     * Verificar si un mensaje contiene palabras prohibidas
     *
     * @param string $text
     * @return boolean
     */
    public static function containsBadWords($text)
    {
        $badWords = config('censored_words.words');
        
        foreach ($badWords as $word) {
            if (stripos($text, $word) !== false) {
                return true;
            }
        }
        
        return false;
    }
}
