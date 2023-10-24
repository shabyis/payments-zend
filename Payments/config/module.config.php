<?php

use Payments\Controller\CaptureController;

return [

    'router'       => [
        'routes' => [
            // Extended Route from Payum Module so exceptions generated on it can be caught.
            'custom_payum_capture_do' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/payment/capture[/:payum_token]',
                    'constraints' => [
                        'payum_token' => '[a-zA-Z0-9_-]+'
                    ],
                    'defaults' => [
                        'controller' => CaptureController::class,
                        'action' => 'do'
                    ],
                ],
            ],
        ],
    ],

	'view_manager' => [
        'display_exceptions' => true,
        'template_map' => [
            'layout/payment_exception_response' => __DIR__ . '/../view/payments/exception-response.phtml',
        ],
        'strategies' => [
            'ViewJsonStrategy'
        ]
    ],

	'doctrine' => [
		'driver' => [
			'payments_driver' => [
				'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
				'cache' => 'array',
				'paths' => [ __DIR__ . '/../src/Payments/Entity' ]
			],
			'orm_default'             => [
				'drivers' => [
					'Payments\Entity' => 'payments_driver'
				]
			]
		],
	],
];
