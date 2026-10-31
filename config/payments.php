<?php

return [
    // The shop's selling rate, not a live rate from the National Bank of Cambodia.
    'khr_per_usd' => (int) env('POS_KHR_PER_USD', 4000),

    'card_enabled' => (bool) env('POS_CARD_ENABLED', false),

    // Use the shop's real, bank-issued KHQR images. Leave blank to use a counter stand.
    'khqr' => [
        'merchant_name' => env('POS_KHQR_MERCHANT_NAME', "Purr's Coffee"),
        'usd_image' => env('POS_KHQR_USD_IMAGE'),
        'khr_image' => env('POS_KHQR_KHR_IMAGE'),
    ],
];
