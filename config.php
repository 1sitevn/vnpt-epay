<?php

return [
    'vnpt' => [
        'epay' => [
            'ws_url' => env('VNPT_EPAY_WS_URL', null),
            'partner_username' => env('VNPT_EPAY_PARTNER_USERNAME', null),
            'partner_password' => env('VNPT_EPAY_PARTNER_PASSWORD', null),
            'key_sofpin' => env('VNPT_EPAY_KEY_SOFPIN', null),
            'private_key_path' => env('VNPT_EPAY_PRIVATE_KEY_PATH', null),
            'public_key_path' => env('VNPT_EPAY_PUBLIC_KEY_PATH', null),
        ]
    ]
];
