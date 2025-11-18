<?php
/**
 * Configuration pour les États-Unis
 */

return [
    'code' => 'US',
    'name' => 'United States',
    'language' => 'en',

    // Devise
    'currency' => [
        'code' => 'USD',
        'symbol' => '$',
        'decimals' => 2,
        'decimal_separator' => '.',
        'thousand_separator' => ',',
        'symbol_position' => 'before'
    ],

    // Formats
    'date_format' => 'm/d/Y',
    'datetime_format' => 'm/d/Y h:i A',
    'time_format' => 'h:i A',

    // Téléphone
    'phone' => [
        'code' => '+1',
        'format' => '(###) ###-####',
        'regex' => '/^[0-9]{10}$/',
        'placeholder' => '(555) 123-4567'
    ],

    // Code postal
    'postal_code' => [
        'regex' => '/^[0-9]{5}(-[0-9]{4})?$/',
        'placeholder' => '10001'
    ],

    // Unités
    'units' => [
        'distance' => 'miles',
        'area' => 'sqft',
        'volume' => 'cu ft',
        'weight' => 'lbs'
    ],

    // Formules de tarification
    'pricing' => [
        'base_price' => 600,
        'price_per_sqm' => 12,
        'price_per_km' => 2.0,
        'floor_cost_per_level' => 150,
        'lift_cost' => 350,
        'house_multiplier' => 1.3,

        'formulas' => [
            'eco' => [
                'name' => 'Economy Package',
                'multiplier' => 0.6,
                'description' => 'Truck rental with driver'
            ],
            'standard' => [
                'name' => 'Standard Package',
                'multiplier' => 1.0,
                'description' => 'Loading and unloading assistance'
            ],
            'premium' => [
                'name' => 'Full Service Package',
                'multiplier' => 1.6,
                'description' => 'Complete packing, moving and unpacking'
            ]
        ]
    ],

    // États
    'regions' => [
        'Northeast' => ['CT', 'ME', 'MA', 'NH', 'RI', 'VT', 'NJ', 'NY', 'PA'],
        'Midwest' => ['IL', 'IN', 'MI', 'OH', 'WI', 'IA', 'KS', 'MN', 'MO', 'NE', 'ND', 'SD'],
        'South' => ['DE', 'FL', 'GA', 'MD', 'NC', 'SC', 'VA', 'WV', 'AL', 'KY', 'MS', 'TN', 'AR', 'LA', 'OK', 'TX'],
        'West' => ['AZ', 'CO', 'ID', 'MT', 'NV', 'NM', 'UT', 'WY', 'AK', 'CA', 'HI', 'OR', 'WA']
    ],

    // Coordonnées de contact
    'contact' => [
        'phone' => '1-800-MOVERS-1',
        'email' => 'contact@movers.com',
        'address' => '123 Main Street, New York, NY 10001, USA'
    ],

    // Paramètres légaux
    'legal' => [
        'company_name' => 'Movers.com LLC',
        'ein' => '12-3456789',
        'vat' => '',
        'capital' => '$100,000'
    ],

    // SEO
    'seo' => [
        'title_suffix' => ' - Movers.com',
        'description' => 'Compare up to 6 free quotes from professional movers across the United States.'
    ]
];
