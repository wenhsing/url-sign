<?php

/*
 |--------------------------------------------------------------------------
 | Url Signature configuration
 |--------------------------------------------------------------------------
 */
return [
    'default' => env('URL_SIGN_DRIVER', 'md5'),

    // md5 需要的配置参数
    'md5' => [
        // 需要排除的路径
        'except'     => [],

        // 签名加签密钥
        'secretKey' => env('URL_SIGN_SECRET_KEY', ''),

        // 允许签名请求时间与当前时间的误差值（秒）
        'timeError' => env('URL_SIGN_TIME_ERROR', 300),
    ],
];
