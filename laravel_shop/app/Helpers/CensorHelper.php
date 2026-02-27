<?php

namespace App\Helpers;

class CensorHelper
{
    /**
     * Lista de palabras prohibidas
     */
    protected static $badWords = [];
    
    /**
     * Patrones precompilados para mejor rendimiento
     */
    protected static $patterns = [];
    
    /**
     * Obtener la lista de palabras prohibidas
     */
    protected static function getBadWords()
    {
        if (empty(self::$badWords)) {
            self::$badWords = config('censored_words.words', []);
        }
        return self::$badWords;
    }
    
    /**
     * Precompilar patrones para búsqueda eficiente
     */
    protected static function compilePatterns()
    {
        if (!empty(self::$patterns)) {
            return self::$patterns;
        }
        
        $badWords = self::getBadWords();
        
        // Separar palabras simples de frases
        $simpleWords = [];
        $phrases = [];
        
        foreach ($badWords as $word) {
            if (strpos($word, ' ') !== false) {
                $phrases[] = preg_quote($word, '/');
            } else {
                $simpleWords[] = preg_quote($word, '/');
            }
        }
        
        // Patrón para palabras simples (búsqueda de palabra completa)
        if (!empty($simpleWords)) {
            self::$patterns['simple'] = '/\b(' . implode('|', $simpleWords) . ')\b/i';
        }
        
        // Patrón para frases (búsqueda literal)
        if (!empty($phrases)) {
            self::$patterns['phrases'] = '/(' . implode('|', $phrases) . ')/i';
        }
        
        // Patrón combinado (para detección rápida)
        if (!empty($badWords)) {
            $allWords = array_map('preg_quote', $badWords);
            self::$patterns['all'] = '/(' . implode('|', $allWords) . ')/i';
        }
        
        return self::$patterns;
    }
    
    /**
     * Censurar palabras prohibidas en un texto
     *
     * @param string $text
     * @return string
     */
    public static function censor($text)
    {
        $config = config('censored_words');
        $replacement = $config['replacement'] ?? '*';
        $fullCensor = $config['full_message_censor'] ?? false;
        
        self::compilePatterns();
        
        // Verificar si contiene malas palabras
        $containsBadWords = self::containsBadWords($text);
        
        // Si está activado el censurado completo
        if ($fullCensor && $containsBadWords) {
            return '[Mensaje censurado por contenido inapropiado]';
        }
        
        if (!$containsBadWords) {
            return $text;
        }
        
        // Aplicar censura por frases primero
        if (isset(self::$patterns['phrases'])) {
            $text = preg_replace_callback(self::$patterns['phrases'], function($matches) use ($replacement) {
                return str_repeat($replacement, mb_strlen($matches[0]));
            }, $text);
        }
        
        // Aplicar censura por palabras simples
        if (isset(self::$patterns['simple'])) {
            $text = preg_replace_callback(self::$patterns['simple'], function($matches) use ($replacement) {
                return str_repeat($replacement, mb_strlen($matches[0]));
            }, $text);
        }
        
        return $text;
    }
    
    /**
     * Versión ultra agresiva de censura (reemplaza todo)
     */
    public static function censorAggressive($text)
    {
        self::compilePatterns();
        
        if (isset(self::$patterns['all'])) {
            return preg_replace_callback(self::$patterns['all'], function($matches) {
                return str_repeat('*', mb_strlen($matches[0]));
            }, $text);
        }
        
        return $text;
    }
    
    /**
     * Verificar si un mensaje contiene palabras prohibidas
     *
     * @param string $text
     * @return boolean
     */
    public static function containsBadWords($text)
    {
        self::compilePatterns();
        
        if (isset(self::$patterns['all'])) {
            return preg_match(self::$patterns['all'], $text) === 1;
        }
        
        return false;
    }
    
    /**
     * Obtener las palabras prohibidas encontradas en un texto
     *
     * @param string $text
     * @return array
     */
    public static function getBadWordsFound($text)
    {
        self::compilePatterns();
        $found = [];
        
        if (isset(self::$patterns['all'])) {
            preg_match_all(self::$patterns['all'], $text, $matches);
            $found = array_unique($matches[0] ?? []);
        }
        
        return $found;
    }
    
    /**
     * Limpiar un texto eliminando completamente las malas palabras
     */
    public static function removeBadWords($text)
    {
        self::compilePatterns();
        
        if (isset(self::$patterns['all'])) {
            return preg_replace(self::$patterns['all'], '', $text);
        }
        
        return $text;
    }
    
    /**
     * Versión para chat en tiempo real (optimizada)
     */
    public static function censorFast($text)
    {
        static $compiled = false;
        static $patterns = [];
        
        if (!$compiled) {
            $badWords = config('censored_words.words', []);
            $allWords = array_map('preg_quote', $badWords);
            $patterns['all'] = '/(' . implode('|', $allWords) . ')/i';
            $compiled = true;
        }
        
        return preg_replace_callback($patterns['all'], function($matches) {
            return str_repeat('*', mb_strlen($matches[0]));
        }, $text);
    }
}
