<?php

if (!function_exists('config')) {
    /**
     * @param $key
     * @param null $default
     * @return array|mixed|null
     */
    function config($key, $default = null)
    {
        $configs = [
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

        $data = $configs;

        $keys = explode('.', $key);

        foreach ($keys as $_key) {
            if (isset($data[$_key])) {
                $data = $data[$_key];

                continue;
            }

            return $default;
        }

        return $data;
    }
}

if (!function_exists('env')) {
    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    function env($key, $default = null)
    {
        return !empty($_ENV[$key]) ? $_ENV[$key] : $default;
    }
}
