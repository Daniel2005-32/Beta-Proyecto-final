<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CensoredWord extends Model
{
    use HasFactory;

    protected $fillable = ['word', 'replacement', 'severity', 'active'];

    protected $casts = [
        'active' => 'boolean',
    ];

    public static function getActiveWords()
    {
        return self::where('active', true)->pluck('word')->toArray();
    }

    public static function getPattern()
    {
        $words = self::getActiveWords();
        if (empty($words)) {
            return null;
        }
        return '/\b(' . implode('|', array_map('preg_quote', $words)) . ')\b/i';
    }
}
