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
        // ============================================
        // INSULTOS BÁSICOS EN ESPAÑOL
        // ============================================
        'puta', 'puto', 'putos', 'putas',
        'polla', 'pollas', 'chupapolla', 'chupa polla',
        'coño', 'coños',
        'cojones', 'cojon',
        'cabron', 'cabrón', 'cabrona', 'cabronas', 'cabrones',
        'gilipollas', 'gilipolla', 'gay',
        'hijoputa', 'hijoputo', 'hijoputos', 'hijoputas',
        'hijo de puta', 'hijos de puta', 'hija de puta', 'hijas de puta',
        'mierda', 'mierdas', 'muertos', 'muerto', 'maricon',
        'estupido', 'estúpido', 'estupida', 'estúpida',
        'idiota', 'idiotas',
        'tonto', 'tonta', 'tontos', 'tontas',
        'imbecil', 'imbécil', 'imbeciles', 'imbéciles',
        'subnormal', 'subnormales',
        'retrasado', 'retrasada', 'retrasados', 'retrasadas',
        
        // ============================================
        // PALABRAS SUBIDAS DE TONO (NIVEL 1)
        // ============================================
        'sexo', 'sexual', 'sexy',
        'porno', 'pornografia', 'pornografía',
        'desnudo', 'desnuda', 'desnudos', 'desnudas',
        'erotico', 'erótico', 'erotica', 'erótica',
        'follar', 'follando', 'follamos', 'follada', 'follado',
        'coger', 'cogiendo', 'cogemos', 'cogida', 'cogido',
        'chupar', 'chupando', 'chupada', 'chupado',
        'mamar', 'mamando', 'mamada', 'mamado',
        'culo', 'culos', 'culito',
        'tetas', 'teta', 'tetitas',
        'pechos', 'pecho',
        'nalgas', 'nalga',
        'pene', 'penes',
        'vagina', 'vaginas',
        'senos', 'seno',
        'bragas', 'braga',
        'calzon', 'calzón', 'calzones',
        'sujetador', 'sujetadores',
        
        // ============================================
        // PALABRAS SUBIDAS DE TONO (NIVEL 2 - MÁS EXPLÍCITAS)
        // ============================================
        'porno', 'porn', 'xxx',
        'culo', 'culos', 'culito',
        'tetas', 'tetas grandes', 'tetazas', 'tetotas',
        'chichis', 'chichi',
        'pito', 'pit0', 'pitO',
        'verga', 'vergas',
        'polla', 'pollon', 'pollón',
        'coño', 'coñazo',
        'concha', 'conchas',
        'chocha', 'chocho',
        'rabo', 'rabos',
        'pija', 'pijas',
        'fiesta', 'fiestuki', // Versiones juveniles
        'guarrada', 'guarrería',
        'marrano', 'marrana',
        'cerdo', 'cerda',
        
        // ============================================
        // PALABRAS EN INGLÉS (COMUNES)
        // ============================================
        'fuck', 'fucking', 'fucker', 'fuckers',
        'shit', 'shitting',
        'bitch', 'bitches', 'bitching',
        'asshole', 'assholes',
        'dick', 'dicks',
        'pussy', 'pussies',
        'motherfucker', 'motherfuckers', 'mf',
        'cock', 'cocks',
        'whore', 'whores',
        'slut', 'sluts',
        'bastard', 'bastards',
        'damn', 'damnit',
        'hell', 'hells',
        
        // ============================================
        // EXPRESIONES COMUNES
        // ============================================
        'puto amo', 'puta madre',
        'la concha', 'la concha de tu madre',
        'la puta', 'la putísima',
        'reputa', 'reputisima', 'reputísima',
        'me cago en', 'me cago en la',
        'me cago en tus', 'me cago en tu',
        'que te folle', 'que te follen',
        'vete a la mierda', 'vete a tomar por culo',
        'a tomar por culo', 'a la mierda',
        
        // ============================================
        // ACRÓNIMOS Y ABREVIATURAS
        // ============================================
        'hdp', 'hpta', 'ptm', 'pqti', 'pqti',
        'stfu', 'wtf', 'lol', 'omg', // Aunque no son malas, pueden ser molestas
        'tkm', 'tqm', // "te quiero mucho" - Positivo pero podemos incluirlo
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
