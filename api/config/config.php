<?php
use Bono\App;

return [
    'bind9' => [
        'ns' => [
            'ns1.xinixhost.com',
            'ns2.xinixhost.com',
        ],
        'parkingIp' => '192.168.0.1',
        'indexFile' => '/etc/bind/named.conf.local',
        'libDir' => '/var/lib/bind'
    ],
    'middlewares' => [
        // write json response
        function ($context, $next) {
            $next($context);

            $context->withContentType('application/json');
            $context->write(json_encode($context->getState()));
        },
        [
            'class' => Bono\Middleware\BodyParser::class,
            'config' => [
                'parsers' => [
                    'application/json' => function ($context) {
                        $body = file_get_contents('php://input');
                        return $context->withParsedBody(json_decode($body, true));
                    },
                ]
            ]
        ]
    ],
    'bundles' => [
        [
            'uri' => '/zone',
            'class' => Bapi\Bundle\Zone::class,
        ],
    ],
    'routes' => [
        [
            'uri' => '/',
            'handler' => function ($context) {
                return [
                    'application_name' => 'bind9-api',
                    'modules' => [
                        'zone' => '/zone',
                    ]
                ];
            }
        ]
    ],
];
