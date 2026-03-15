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
        // INSULTOS BÁSICOS EN ESPAÑOL (con variaciones)
        // ============================================
        'puta', 'puto', 'putos', 'putas',
        'PUTA', 'PUTO', 'PUTOS', 'PUTAS',
        'Puta', 'Puto', 'Putos', 'Putas',
        
        'polla', 'pollas', 'pollon', 'pollón', 'pollones',
        'POLLA', 'POLLAS', 'POLLON', 'POLLÓN', 'POLLONES',
        'Polla', 'Pollas', 'Pollon', 'Pollón',
        'chupapolla', 'chupapollas', 'chupa polla', 'chupa pollas',
        'CHUPAPOLLA', 'CHUPAPOLLAS', 'CHUPA POLLA', 'CHUPA POLLAS',
        
        'coño', 'coños', 'coñazo', 'coñazos',
        'COÑO', 'COÑOS', 'COÑAZO', 'COÑAZOS',
        'Coño', 'Coños', 'Coñazo',
        
        'cojones', 'cojon', 'cojones', 'cojonazos',
        'COJONES', 'COJON', 'COJONES', 'COJONAZOS',
        'Cojones', 'Cojon',
        
        'cabron', 'cabrón', 'cabrona', 'cabronas', 'cabrones', 'cabronazos', 'cabronazo', 'cabronaza',
        'CABRON', 'CABRÓN', 'CABRONA', 'CABRONAS', 'CABRONES', 'CABRONAZOS', 'CABRONAZO', 'CABRONAZA',
        'Cabron', 'Cabrón', 'Cabrona', 'Cabronas', 'Cabrones', 'Cabronazos', 'Cabronazo', 'Cabronaza',
        
        'gilipollas', 'gilipolla', 'gilipollas', 'gilipollazos',
        'GILIPOLLAS', 'GILIPOLLA', 'GILIPOLLAS',
        'Gilipollas', 'Gilipolla',
        
        'hijoputa', 'hijoputo', 'hijoputos', 'hijoputas',
        'hijo de puta', 'hijos de puta', 'hija de puta', 'hijas de puta',
        'HIJOPUTA', 'HIJOPUTO', 'HIJOPUTOS', 'HIJOPUTAS',
        'HIJO DE PUTA', 'HIJOS DE PUTA', 'HIJA DE PUTA', 'HIJAS DE PUTA',
        'Hijoputa', 'Hijoputo', 'Hijo de puta',
        
        'mierda', 'mierdas', 'mierditas',
        'MIERDA', 'MIERDAS', 'MIERDITAS',
        'Mierda', 'Mierdas',
        
        'maricon', 'maricón', 'maricones', 'maricona', 'mariconas',
        'MARICON', 'MARICÓN', 'MARICONES', 'MARICONA', 'MARICONAS',
        'Maricon', 'Maricón',

        'mamon', 'mamona', 'mamones', 
        'MAMON', 'MAMONA', 'MAMONES',
        'Mamon', 'Mamona', 'Mamones',

        'matar', 'te voy a matar', 'muerto', 'muerta',
        'MATAR', 'TE VOY A MATAR', 'MUERTO', 'MUERTA',
        'Matar', 'Te Voy A Matar', 'Muerto', 'Muerta',
        
        'estupido', 'estúpido', 'estupida', 'estúpida', 'estupidos', 'estúpidos', 'estupidas', 'estúpidas',
        'ESTUPIDO', 'ESTÚPIDO', 'ESTUPIDA', 'ESTÚPIDA', 'ESTUPIDOS', 'ESTÚPIDOS', 'ESTUPIDAS', 'ESTÚPIDAS',
        'Estupido', 'Estúpido', 'Estupida', 'Estúpida',
        
        'idiota', 'idiotas', 'idiotiza', 'idiotizado',
        'IDIOTA', 'IDIOTAS', 'IDIOTIZA', 'IDIOTIZADO',
        'Idiota', 'Idiotas',
        
        'joder', 'jodido', 'jodida', 'jodidos', 'jodidas', 'jodiendo', 'jode', 'joden',
        'JODER', 'JODIDO', 'JODIDA', 'JODIDOS', 'JODIDAS', 'JODIENDO', 'JODE', 'JODEN',
        'Joder', 'Jodido', 'Jodida',
        
        'tonto', 'tonta', 'tontos', 'tontas', 'tontaco', 'tontaco',
        'TONTO', 'TONTA', 'TONTOS', 'TONTAS', 'TONTACO',
        'Tonto', 'Tonta',
        
        'imbecil', 'imbécil', 'imbeciles', 'imbéciles', 'imbecilidad',
        'IMBECIL', 'IMBÉCIL', 'IMBECILES', 'IMBÉCILES', 'IMBECILIDAD',
        'Imbecil', 'Imbécil',
        
        'subnormal', 'subnormales', 'subnormalidad',
        'SUBNORMAL', 'SUBNORMALES', 'SUBNORMALIDAD',
        'Subnormal', 'Subnormales',
        
        'retrasado', 'retrasada', 'retrasados', 'retrasadas', 'retraso',
        'RETRASADO', 'RETRASADA', 'RETRASADOS', 'RETRASADAS', 'RETRASO',
        'Retrasado', 'Retrasada',
        
        'payaso', 'payasa', 'payasos', 'payasas',
        'PAYASO', 'PAYASA', 'PAYASOS', 'PAYASAS',
        'Payaso', 'Payasa',
        
        'zopenco', 'zopenco', 'zopencos',
        'ZOPENCO', 'ZOPENCO', 'ZOPENCOS',

        'zumbao', 'zumbaa', 'zumbado', 'zumbada',
        'ZUMBAO', 'ZUMBAA', 'ZUMBADO', 'ZUMBADA',
        'Zumbao', 'Zumbaa', 'Zumbado', 'Zumbada',
        
        // ============================================
        // PALABRAS SUBIDAS DE TONO (NIVEL 1)
        // ============================================
        'sexo', 'sexual', 'sexy', 'sexu', 'sexuales',
        'SEXO', 'SEXUAL', 'SEXY', 'SEXUALES',
        'Sexo', 'Sexual', 'Sexy',
        
        'porno', 'pornografia', 'pornografía', 'pornografico', 'pornográfico',
        'PORNO', 'PORNOGRAFIA', 'PORNOGRAFÍA', 'PORNOGRAFICO', 'PORNOGRÁFICO',
        'Porno', 'Pornografia', 'Pornografía',
        
        'desnudo', 'desnuda', 'desnudos', 'desnudas', 'desnudez',
        'DESNUDO', 'DESNUDA', 'DESNUDOS', 'DESNUDAS', 'DESNUDEZ',
        'Desnudo', 'Desnuda',
        
        'erotico', 'erótico', 'erotica', 'erótica', 'erotismo',
        'EROTICO', 'ERÓTICO', 'EROTICA', 'ERÓTICA', 'EROTISMO',
        'Erotico', 'Erótico',
        
        'follar', 'follando', 'follamos', 'follada', 'follado', 'follados', 'folladas', 'follemos',
        'FOLLAR', 'FOLLANDO', 'FOLLAMOS', 'FOLLADA', 'FOLLADO', 'FOLLADOS', 'FOLLADAS',
        'Follar', 'Follando',
        
        'coger', 'cogiendo', 'cogemos', 'cogida', 'cogido', 'cogidos', 'cogidas',
        'COGER', 'COGIENDO', 'COGEMOS', 'COGIDA', 'COGIDO', 'COGIDOS', 'COGIDAS',
        'Coger', 'Cogiendo',
        
        'chupar', 'chupando', 'chupada', 'chupado', 'chupones', 'chupete',
        'CHUPAR', 'CHUPANDO', 'CHUPADA', 'CHUPADO', 'CHUPONES',
        'Chupar', 'Chupando',
        
        'mamar', 'mamando', 'mamada', 'mamado', 'mamador', 'mamadora',
        'MAMAR', 'MAMANDO', 'MAMADA', 'MAMADO', 'MAMADOR', 'MAMADORA',
        'Mamar', 'Mamando',
        
        'culo', 'culos', 'culito', 'culitos', 'culazo',
        'CULO', 'CULOS', 'CULITO', 'CULITOS', 'CULAZO',
        'Culo', 'Culos',
        
        'tetas', 'teta', 'tetitas', 'tetazas', 'tetotas', 'tetillas',
        'TETAS', 'TETA', 'TETITAS', 'TETAZAS', 'TETOTAS',
        'Tetas', 'Teta',
        
        'pechos', 'pecho', 'pechonalidad', 'pechugon',
        'PECHOS', 'PECHO', 'PECHONALIDAD',
        'Pechos', 'Pecho',
        
        'nalgas', 'nalga', 'nalgona', 'nalgonas', 'nalguitas',
        'NALGAS', 'NALGA', 'NALGONA', 'NALGONAS',
        'Nalgas', 'Nalga',
        
        'pene', 'penes', 'pene enorme', 'penazo',
        'PENE', 'PENES', 'PENAZO',
        'Pene', 'Penes',
        
        'vagina', 'vaginas', 'vaginal', 'vaginoplastia',
        'VAGINA', 'VAGINAS', 'VAGINAL',
        'Vagina', 'Vaginas',
        
        'senos', 'seno', 'senos grandes',
        'SENOS', 'SENO',
        'Senos', 'Seno',
        
        'bragas', 'braga', 'braguitas',
        'BRAGAS', 'BRAGA', 'BRAGUITAS',
        'Bragas', 'Braga',
        
        'calzon', 'calzón', 'calzones', 'calzoncillos',
        'CALZON', 'CALZÓN', 'CALZONES', 'CALZONCILLOS',
        'Calzon', 'Calzón',
        
        'sujetador', 'sujetadores', 'sostén', 'sostenes',
        'SUJETADOR', 'SUJETADORES', 'SOSTÉN', 'SOSTENES',
        'Sujetador', 'Sostén',
        
        // ============================================
        // PALABRAS SUBIDAS DE TONO (NIVEL 2 - MÁS EXPLÍCITAS)
        // ============================================
        'xxx', 'porn', 'porno', 'hardcore',
        'XXX', 'PORN', 'PORNO', 'HARDCORE',
        
        'chichis', 'chichi', 'chichita',
        'CHICHIS', 'CHICHI', 'CHICHITA',
        
        'pito', 'pit0', 'pitO', 'pitito', 'pitote',
        'PITO', 'PIT0', 'PITO', 'PITITO',
        
        'verga', 'vergas', 'vergazo', 'vergota',
        'VERGA', 'VERGAS', 'VERGAZO',
        'Verga', 'Vergas',
        
        'concha', 'conchas', 'conchita', 'conchudo', 'conchuda',
        'CONCHA', 'CONCHAS', 'CONCHITA', 'CONCHUDO', 'CONCHUDA',
        'Concha', 'Conchas',
        
        'chocha', 'chocho', 'chochito', 'chochitos',
        'CHOCHA', 'CHOCHO', 'CHOCHITO',
        'Chocha', 'Chocho',
        
        'rabo', 'rabos', 'rabon', 'rabona',
        'RABO', 'RABOS', 'RABON',
        'Rabo', 'Rabos',
        
        'pija', 'pijas', 'pijazo', 'pijota',
        'PIJA', 'PIJAS', 'PIJAZO',
        'Pija', 'Pijas',
        
        'guarrada', 'guarrería', 'guarrerias', 'guarro', 'guarra',
        'GUARRADA', 'GUARRERÍA', 'GUARRERIAS', 'GUARRO', 'GUARRA',
        
        'marrano', 'marrana', 'marranada',
        'MARRANO', 'MARRANA', 'MARRANADA',
        
        'cerdo', 'cerda', 'cerdada', 'cerdito',
        'CERDO', 'CERDA', 'CERDADA',
        
        'baboso', 'babosa', 'baboseo',
        'BABOSO', 'BABOSA', 'BABOSEO',
        
        'caliente', 'calenton', 'calentona', 'calentorro',
        'CALIENTE', 'CALENTON', 'CALENTONA',

        // ============================================
        // PALABRAS EN LATINOAMERICA
        // ============================================
        

        'boludo', 'pelotudo', ' conchuda', 'cajetuda', 'argolluda', 'malcogida',  
        'pijotero', 'guampudo', 'lola', 'goma', 'culiao', 'culiado', 're-culiao', 
        'conchetumadre', 'agüeonao', 'mamón', 'huevón', 'weón', 'pendejo',  
        'güey', 'wey', 'chingar', 'chingada', 'chingado', 'pinche', 
        'menso', 'gafo', 'cabeceburro', 'cabeceduro', 'guacarnaco', 
        'gonorrea', 'malparido', 'comemierda', 'ladilla', 'pajuilado', 
        'pajuo', 'cachirulo', 'jueputa', 'parigüayo', 'parigüaya', 'lambón',
        'culiabierto', 'zonzoneco', 'tolongo', 'zanguango', 'paparulo', 
        'abombado', 'guachinango', 'lentejo', 'asnúpido', 'turuleco', 'samuro', 
        'soroco', 'majiriulo', 'bachilín', 'majirulo',

        // ============================================
        // PALABRAS EN INGLÉS (COMUNES)
        // ============================================
        'fuck', 'fucking', 'fucker', 'fuckers', 'fucked', 'fuckin',
        'FUCK', 'FUCKING', 'FUCKER', 'FUCKERS', 'FUCKED',
        'Fuck', 'Fucking',
        
        'shit', 'shitting', 'shitter', 'shitters', 'shitty',
        'SHIT', 'SHITTING', 'SHITTER', 'SHITTERS', 'SHITTY',
        'Shit', 'Shitting',
        
        'bitch', 'bitches', 'bitching', 'bitchy',
        'BITCH', 'BITCHES', 'BITCHING', 'BITCHY',
        'Bitch', 'Bitches',
        
        'asshole', 'assholes', 'assholish',
        'ASSHOLE', 'ASSHOLES', 'ASSHOLISH',
        'Asshole', 'Assholes',
        
        'dick', 'dicks', 'dickhead', 'dickheads',
        'DICK', 'DICKS', 'DICKHEAD', 'DICKHEADS',
        'Dick', 'Dicks',
        
        'pussy', 'pussies', 'pussyhole',
        'PUSSY', 'PUSSIES', 'PUSSYHOLE',
        'Pussy', 'Pussies',
        
        'motherfucker', 'motherfuckers', 'motherfucking', 'mf',
        'MOTHERFUCKER', 'MOTHERFUCKERS', 'MOTHERFUCKING', 'MF',
        'Motherfucker', 'Motherfuckers',
        
        'cock', 'cocks', 'cockhead', 'cocksucker', 'cocksucking',
        'COCK', 'COCKS', 'COCKHEAD', 'COCKSUCKER', 'COCKSUCKING',
        'Cock', 'Cocks',
        
        'whore', 'whores', 'whoring',
        'WHORE', 'WHORES', 'WHORING',
        'Whore', 'Whores',
        
        'slut', 'sluts', 'slutty', 'slutting',
        'SLUT', 'SLUTS', 'SLUTTY', 'SLUTTING',
        'Slut', 'Sluts',
        
        'bastard', 'bastards', 'bastardly',
        'BASTARD', 'BASTARDS', 'BASTARDLY',
        'Bastard', 'Bastards',
        
        'damn', 'damnit', 'goddamn', 'god damn',
        'DAMN', 'DAMNIT', 'GODDAMN', 'GOD DAMN',
        'Damn', 'Damnit',
        
        'hell', 'hells', 'hellish',
        'HELL', 'HELLS', 'HELLISH',
        'Hell', 'Hells',
        
        'crap', 'crappy', 'crapped',
        'CRAP', 'CRAPPY', 'CRAPPED',
        'Crap', 'Crappy',
        
        'screw', 'screwed', 'screwing', 'screw you',
        'SCREW', 'SCREWED', 'SCREWING', 'SCREW YOU',
        
        'nigga', 'nigger', 'niggas', 'niggers',
        'NIGGA', 'NIGGER', 'NIGGAS', 'NIGGERS',
        
        // ============================================
        // EXPRESIONES COMUNES
        // ============================================
        'puto amo', 'puto amo', 'PUTO AMO',
        'puta madre', 'puta madre', 'PUTA MADRE',
        'la concha', 'la concha de tu madre', 'LA CONCHA', 'LA CONCHA DE TU MADRE',
        'la puta', 'la putísima', 'LA PUTA', 'LA PUTÍSIMA',
        'reputa', 'reputisima', 'reputísima', 'REPUTA', 'REPUTISIMA',
        'me cago en', 'me cago en la', 'me cago en tus', 'me cago en tu',
        'ME CAGO EN', 'ME CAGO EN LA', 'ME CAGO EN TUS', 'ME CAGO EN TU',
        'que te folle', 'que te follen', 'QUE TE FOLLE', 'QUE TE FOLLEN',
        'vete a la mierda', 'vete a tomar por culo', 'VETE A LA MIERDA', 'VETE A TOMAR POR CULO',
        'a tomar por culo', 'a la mierda', 'A TOMAR POR CULO', 'A LA MIERDA',
        'que te den', 'que te den por culo', 'QUE TE DEN', 'QUE TE DEN POR CULO',
        'vete a la verga', 'VETE A LA VERGA',
        'vete al carajo', 'VETE AL CARAJO',
        'vete a la chingada', 'VETE A LA CHINGADA',
        
        // ============================================
        // ACRÓNIMOS Y ABREVIATURAS
        // ============================================
        'hdp', 'HDP', 'Hdp',
        'hpta', 'HPTA', 'Hpta',
        'ptm', 'PTM', 'Ptm',
        'pqti', 'PQTI', 'Pqti',
        'stfu', 'STFU', 'Stfu',
        'wtf', 'WTF', 'Wtf',
        'lol', 'LOL', 'Lol', // No son malas pero pueden ser spam
        'omg', 'OMG', 'Omg',
        'tkm', 'TKM', 'Tkm', // "te quiero mucho" - positivo pero lo incluimos
        'tqm', 'TQM', 'Tqm',
        'pvt', 'PVT', 'Pvt', // "puta"
        'pt', 'PT', 'Pt', // "puto"
        'mrda', 'MRDA', 'Mrda', // "mierda"
        'klg', 'KLG', 'Klg', // "calle"
        
        // ============================================
        // PALABRAS EN OTROS IDIOMAS
        // ============================================
        // Portugués
        'puta', 'puto', 'puta que pariu', 'caralho', 'porra', 'merda',
        'cu', 'cú', 'filho da puta', 'filha da puta',
        
        // Francés
        'putain', 'merde', 'connard', 'connasse', 'salope', 'enculé',
        
        // Italiano
        'cazzo', 'puttana', 'merda', 'stronzo', 'stronza', 'vaffanculo',
        
        // Alemán
        'scheiße', 'scheisse', 'fick', 'ficken', 'arschloch',
        
        // Catalán
        'collons', 'cony', 'merda', 'puta', 'fill de puta', 'filla de puta',
        
        // Gallego
        'carallo', 'cona', 'puta', 'fill de puta',
        
        // Vasco
        'puta', 'putakume', 'pizza', 'txakurra', 'astoa',
        
        
        // ============================================
        // AÑADIDOS ESPECIALES
        // ============================================
        'payaso', 'payasa', 'payasos', 'payasas',
        'pajaro', 'pájaro', 'pajarito',
        'huevon', 'huevón', 'huevona', 'huevones',
        'pendejo', 'pendeja', 'pendejos', 'pendejas',
        'chingar', 'chingada', 'chingado', 'chingaderas',
        'pinche', 'pinches',
        'webon', 'webón', 'webona', 'webones',
        'güey', 'wey', 'we', 'buey',
        'comemierda', 'come mierda',
        'tragaleche', 'traga leche',
        'soplapollas', 'soplapolla',
        'chupapollas', 'chupapolla',
        'cagan', 'cagan',
        'me cago', 'me cague',
        'jodete', 'jódete',
        'jodanse', 'jódanse',
        'maldito', 'maldita',
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

