<?php
/**
 * Configuration pour la France
 */

return [
    'code' => 'FR',
    'name' => 'France',
    'language' => 'fr',

    // Devise
    'currency' => [
        'code' => 'EUR',
        'symbol' => '€',
        'decimals' => 2,
        'decimal_separator' => ',',
        'thousand_separator' => ' ',
        'symbol_position' => 'after' // before or after
    ],

    // Formats
    'date_format' => 'd/m/Y',
    'datetime_format' => 'd/m/Y H:i',
    'time_format' => 'H:i',

    // Téléphone
    'phone' => [
        'code' => '+33',
        'format' => '## ## ## ## ##',
        'regex' => '/^0[1-9][0-9]{8}$/',
        'placeholder' => '06 12 34 56 78'
    ],

    // Code postal
    'postal_code' => [
        'regex' => '/^[0-9]{5}$/',
        'placeholder' => '75001'
    ],

    // Unités
    'units' => [
        'distance' => 'km',
        'area' => 'm²',
        'volume' => 'm³',
        'weight' => 'kg'
    ],

    // Formules de tarification
    'pricing' => [
        'base_price' => 500,
        'price_per_sqm' => 8,
        'price_per_km' => 1.5,
        'floor_cost_per_level' => 100,
        'lift_cost' => 250,
        'house_multiplier' => 1.2,

        'formulas' => [
            'eco' => [
                'name' => 'Formule Éco',
                'multiplier' => 0.6,
                'description' => 'Location de camion avec chauffeur'
            ],
            'standard' => [
                'name' => 'Formule Standard',
                'multiplier' => 1.0,
                'description' => 'Aide au chargement et déchargement'
            ],
            'premium' => [
                'name' => 'Formule Clé en main',
                'multiplier' => 1.5,
                'description' => 'Emballage, transport et déballage complets'
            ]
        ]
    ],

    // Régions/Départements
    'regions' => [
        'Île-de-France' => ['75', '77', '78', '91', '92', '93', '94', '95'],
        'Auvergne-Rhône-Alpes' => ['01', '03', '07', '15', '26', '38', '42', '43', '63', '69', '73', '74'],
        'Provence-Alpes-Côte d\'Azur' => ['04', '05', '06', '13', '83', '84'],
        'Occitanie' => ['09', '11', '12', '30', '31', '32', '34', '46', '48', '65', '66', '81', '82'],
        'Nouvelle-Aquitaine' => ['16', '17', '19', '23', '24', '33', '40', '47', '64', '79', '86', '87'],
        'Grand Est' => ['08', '10', '51', '52', '54', '55', '57', '67', '68', '88'],
        'Hauts-de-France' => ['02', '59', '60', '62', '80'],
        'Bretagne' => ['22', '29', '35', '56'],
        'Pays de la Loire' => ['44', '49', '53', '72', '85'],
        'Normandie' => ['14', '27', '50', '61', '76'],
        'Centre-Val de Loire' => ['18', '28', '36', '37', '41', '45'],
        'Bourgogne-Franche-Comté' => ['21', '25', '39', '58', '70', '71', '89', '90'],
        'Corse' => ['2A', '2B'],
        'DOM-TOM' => ['971', '972', '973', '974', '976']
    ],

    // Coordonnées de contact
    'contact' => [
        'phone' => '09 78 45 02 18',
        'email' => 'contact@demenageur.com',
        'address' => '123 Avenue des Champs-Élysées, 75008 Paris, France'
    ],

    // Paramètres légaux
    'legal' => [
        'company_name' => 'Déménageur.com SARL',
        'siret' => '123 456 789 00012',
        'vat' => 'FR12 123456789',
        'capital' => '50 000 €'
    ],

    // SEO
    'seo' => [
        'title_suffix' => ' - Déménageur.com',
        'description' => 'Comparez gratuitement jusqu\'à 6 devis de déménageurs professionnels en France.'
    ]
];
