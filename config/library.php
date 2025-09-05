<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Library Identity
    |--------------------------------------------------------------------------
    |
    | These values define the identity and branding of your library system.
    |
    */

    'name' => env('LIBRARY_NAME', 'BiblioTech Hub'),
    'tagline' => env('LIBRARY_TAGLINE', 'Your Gateway to Knowledge'),
    'description' => 'A modern digital library management system that connects readers with their next great adventure.',

    /*
    |--------------------------------------------------------------------------
    | Contact Information
    |--------------------------------------------------------------------------
    */

    'contact' => [
        'email' => env('LIBRARY_EMAIL', 'info@bibliotech-hub.com'),
        'phone' => env('LIBRARY_PHONE', '+1-555-LIBRARY'),
        'address' => '123 Knowledge Street, Learning City, LC 12345',
        'hours' => [
            'monday' => '9:00 AM - 8:00 PM',
            'tuesday' => '9:00 AM - 8:00 PM',
            'wednesday' => '9:00 AM - 8:00 PM',
            'thursday' => '9:00 AM - 8:00 PM',
            'friday' => '9:00 AM - 6:00 PM',
            'saturday' => '10:00 AM - 5:00 PM',
            'sunday' => '12:00 PM - 5:00 PM',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Library Settings
    |--------------------------------------------------------------------------
    |
    | Configure the core business rules for your library.
    |
    */

    'settings' => [
        'max_loans_per_user' => env('LIBRARY_MAX_LOANS', 5),
        'loan_duration_days' => env('LIBRARY_LOAN_DURATION', 14),
        'max_renewals' => env('LIBRARY_MAX_RENEWALS', 2),
        'overdue_fee_per_day' => env('LIBRARY_OVERDUE_FEE', 0.50),
        'reservation_hold_days' => env('LIBRARY_RESERVATION_HOLD_DAYS', 7),
        'fine_grace_period_days' => 3,
        'max_reservation_queue' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Legacy Settings (Backward Compatibility)
    |--------------------------------------------------------------------------
    */
    'reservation' => [
        'expiry_days' => 7, // Default reservation duration
        'max_active_per_user' => 5, // Maximum active reservations per user
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific features of the library system.
    |
    */

    'features' => [
        'reviews_enabled' => env('LIBRARY_REVIEWS_ENABLED', true),
        'reservations_enabled' => env('LIBRARY_RESERVATIONS_ENABLED', true),
        'recommendations_enabled' => env('LIBRARY_RECOMMENDATIONS_ENABLED', true),
        'notifications_enabled' => env('LIBRARY_NOTIFICATIONS_ENABLED', true),
        'fines_enabled' => env('LIBRARY_FINES_ENABLED', true),
        'renewals_enabled' => env('LIBRARY_RENEWALS_ENABLED', true),
        'search_suggestions' => env('LIBRARY_SEARCH_SUGGESTIONS', true),
        'reading_lists' => env('LIBRARY_READING_LISTS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Branding & Theme
    |--------------------------------------------------------------------------
    |
    | Customize the visual appearance of your library.
    |
    */

    'theme' => [
        'primary_color' => '#1e3a8a', // Deep Blue
        'secondary_color' => '#f59e0b', // Gold/Amber
        'accent_color' => '#10b981', // Green for success states
        'background_color' => '#fefefe', // Warm White
        'text_color' => '#1f2937', // Dark Gray
        'logo_path' => '/images/library-logo.png',
        'favicon_path' => '/images/library-favicon.ico',
    ],

    /*
    |--------------------------------------------------------------------------
    | Social Media & Links
    |--------------------------------------------------------------------------
    */

    'social' => [
        'website' => env('LIBRARY_WEBSITE', 'https://bibliotech-hub.com'),
        'facebook' => env('LIBRARY_FACEBOOK', ''),
        'twitter' => env('LIBRARY_TWITTER', ''),
        'instagram' => env('LIBRARY_INSTAGRAM', ''),
        'linkedin' => env('LIBRARY_LINKEDIN', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Statistics Display
    |--------------------------------------------------------------------------
    |
    | Configure what statistics are displayed on the homepage.
    |
    */

    'stats' => [
        'show_total_books' => true,
        'show_active_members' => true,
        'show_genres_count' => true,
        'show_daily_loans' => true,
        'show_reading_hours' => false,
    ],
];
