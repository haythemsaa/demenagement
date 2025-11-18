<?php
/**
 * Configuration pour le Royaume-Uni
 */

return [
    'code' => 'GB',
    'name' => 'United Kingdom',
    'language' => 'en',

    'currency' => [
        'code' => 'GBP',
        'symbol' => '£',
        'decimals' => 2,
        'decimal_separator' => '.',
        'thousand_separator' => ',',
        'symbol_position' => 'before'
    ],

    'date_format' => 'd/m/Y',
    'datetime_format' => 'd/m/Y H:i',
    'time_format' => 'H:i',

    'phone' => [
        'code' => '+44',
        'format' => '#### ### ####',
        'regex' => '/^[0-9]{10,11}$/',
        'placeholder' => '07123 456789'
    ],

    'postal_code' => [
        'regex' => '/^[A-Z]{1,2}[0-9]{1,2} [0-9][A-Z]{2}$/i',
        'placeholder' => 'SW1A 1AA'
    ],

    'units' => [
        'distance' => 'miles',
        'area' => 'sqft',
        'volume' => 'cu ft',
        'weight' => 'lbs'
    ],

    'pricing' => [
        'base_price' => 450,
        'price_per_sqm' => 10,
        'price_per_km' => 1.8,
        'floor_cost_per_level' => 120,
        'lift_cost' => 300,
        'house_multiplier' => 1.25,

        'formulas' => [
            'eco' => ['name' => 'Economy Package', 'multiplier' => 0.6, 'description' => 'Van hire with driver'],
            'standard' => ['name' => 'Standard Package', 'multiplier' => 1.0, 'description' => 'Loading and unloading help'],
            'premium' => ['name' => 'Premium Package', 'multiplier' => 1.5, 'description' => 'Full packing and moving service']
        ]
    ],

    'regions' => [
        'England' => ['London', 'South East', 'South West', 'East of England', 'West Midlands', 'East Midlands', 'Yorkshire', 'North West', 'North East'],
        'Scotland' => ['Glasgow', 'Edinburgh', 'Aberdeen', 'Highlands'],
        'Wales' => ['Cardiff', 'Swansea', 'Newport'],
        'Northern Ireland' => ['Belfast', 'Derry']
    ],

    'contact' => [
        'phone' => '0800 123 4567',
        'email' => 'contact@movers.co.uk',
        'address' => '123 High Street, London SW1A 1AA, UK'
    ],

    'legal' => [
        'company_name' => 'Movers UK Ltd',
        'registration' => '12345678',
        'vat' => 'GB123456789',
        'capital' => '£50,000'
    ],

    'seo' => [
        'title_suffix' => ' - Movers UK',
        'description' => 'Compare up to 6 free quotes from professional removal companies across the UK.'
    ]
];
