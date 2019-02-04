<?php

@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('bnomei/instagram', [
    'options' => [
        'cache' => true,
        'debugforce' => true,
        'expires' => (60*24), // minutes,
        'token' => null,
        'api' => 'https://api.instagram.com/v1',
        'endpoint' => 'users/self/media/recent',
        'params' => [],
    ],
    'siteMethods' => [
        'instagram' => function (string $token = null, string $endpoint = null, array $params = null, $force = null): ?array {
            return \Bnomei\Instagram::instagram($token, $endpoint, $params, $force);
        }
    ]
  ]);
