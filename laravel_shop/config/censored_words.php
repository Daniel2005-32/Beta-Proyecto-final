<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Palabras prohibidas
    |--------------------------------------------------------------------------
    |
    | Lista de palabras que serán censuradas en los mensajes del chat.
    | Se reemplazarán automáticamente con asteriscos.
    |
    */

    'words' => [
        // Insultos en español
        'puta',
        'puto',
        'putos',
        'putas',
        'polla',
        'coño',
        'cojones',
        'cabron',
        'cabrón',
        'gilipollas',
        'hijoputa',
        'hijo de puta',
        'hijo de puta',
        'mierda',
        'estupido',
        'estúpido',
        'idiota',
        'tonto',
        'tonta',
        'imbecil',
        'imbécil',
        'subnormal',
        'retrasado',
        'retrasada',
        'joder',
        'cago',
        'muertos',
        
        // Insultos en inglés (comunes)
        'fuck',
        'fucking',
        'shit',
        'bitch',
        'asshole',
        'dick',
        'pussy',
        'motherfucker',
        'mf',
        
        // Palabras ofensivas varias
        'puto amo', // Aunque es "positivo" puede ser molesto
        'puta madre',
        'la concha',
        'reputa',
        'reputisima',
        'reputísima',
        
        // Variaciones comunes
        'hdp',
        'ptm',
        'pqti',
    ],

    /*
    |--------------------------------------------------------------------------
    | Carácter de reemplazo
    |--------------------------------------------------------------------------
    |
    | Carácter usado para reemplazar las palabras censuradas.
    |
    */
    'replacement' => '*',

    /*
    |--------------------------------------------------------------------------
    | Reemplazo parcial o total
    |--------------------------------------------------------------------------
    |
    | true: reemplaza todo el mensaje con "Mensaje censurado"
    | false: reemplaza solo las palabras malas con asteriscos
    |
    */
    'full_message_censor' => false,
];
